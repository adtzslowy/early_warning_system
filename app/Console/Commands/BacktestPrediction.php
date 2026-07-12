<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Device;
use App\Prediction\LinearRegression;
use App\Prediction\PredictionFeatureBuilder;
use App\Prediction\PredictionHorizons;
use Illuminate\Console\Command;
use RuntimeException;

/**
 * Backtest akurasi prediksi ketinggian air.
 *
 * Memakai model & fitur yang SAMA dengan sistem produksi (regresi linear
 * per-device). Untuk tiap device & horizon: dataset histori dibagi kronologis
 * — sebagian awal untuk melatih, sisanya (masa depan) untuk menguji — supaya
 * evaluasinya out-of-sample (jujur, tanpa "mengintip" jawaban).
 *
 * Metrik:
 *  - MAE / RMSE  : rata-rata error (cm)
 *  - R²          : seberapa besar variasi yang dijelaskan model (1=sempurna, 0=setara nebak rata-rata)
 *  - ≤5cm / ≤10cm: % prediksi yang meleset ≤ toleransi (angka "keberhasilan" intuitif)
 *  - MAE naif    : baseline persistence ("air tetap") — model berguna hanya jika MAE < MAE naif
 */
class BacktestPrediction extends Command
{
    /**
     * Asumsi interval sampling sensor (menit). rob:sync dijadwalkan
     * everyMinute() di routes/console.php, jadi ~1 baris/menit per device.
     * Dipakai untuk menghitung embargo DEFAULT yang berskala dengan horizon
     * (lihat handle()) — bukan konstanta tetap untuk semua horizon.
     */
    private const SAMPLING_INTERVAL_MINUTES = 1;

    protected $signature = 'prediction:backtest
        {--train=0.7 : Proporsi data awal untuk melatih (sisanya untuk uji)}
        {--embargo= : Jumlah sampel jeda antara train & test. Kosong = otomatis per horizon (horizon/interval-sampling), supaya buffer anti-kebocoran cukup besar utk horizon jauh}
        {--device= : Batasi ke satu device_code tertentu}';

    protected $description = 'Backtest akurasi prediksi (MAE/RMSE/R²/% dalam toleransi) per horizon';

    public function handle(PredictionFeatureBuilder $builder): int
    {
        $trainRatio = (float) $this->option('train');
        $embargoOption = $this->option('embargo');
        // null = hitung otomatis per horizon di dalam loop (lihat bawah);
        // diisi eksplisit = dipakai sama untuk semua horizon (override manual).
        $embargoOverride = $embargoOption !== null ? max(0, (int) $embargoOption) : null;
        $tolerances = [5.0, 10.0];

        if ($trainRatio <= 0.0 || $trainRatio >= 1.0) {
            $this->error('--train harus di antara 0 dan 1 (mis. 0.7).');

            return self::FAILURE;
        }

        $devices = Device::query()
            ->when($this->option('device'), fn ($q, $code) => $q->where('device_code', $code))
            ->get();

        if ($devices->isEmpty()) {
            $this->warn('Tidak ada device untuk diuji.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Backtest %d device · latih %d%% awal, uji sisanya%s',
            $devices->count(),
            (int) round($trainRatio * 100),
            $embargoOverride !== null
                ? " · embargo {$embargoOverride} sampel (manual)"
                : ' · embargo otomatis per horizon',
        ));

        $rows = [];
        $grandN = 0;
        $grandAbs = 0.0;
        $grandSq = 0.0;
        $grandNaiveAbs = 0.0;

        foreach (PredictionHorizons::MINUTES as $horizon) {
            // Buffer anti-kebocoran harus berskala dgn horizon: target sampel
            // training paling akhir "mengintip" horizon menit ke depan, jadi
            // butuh jeda >= horizon (dalam satuan sampel) sebelum area test
            // dimulai, supaya window target training & feature test benar2
            // terpisah. Horizon 120 menit butuh embargo jauh lebih besar dari
            // horizon 15 menit — konstanta tunggal tidak cukup untuk keduanya.
            $embargo = $embargoOverride ?? (int) ceil($horizon / self::SAMPLING_INTERVAL_MINUTES);

            $abs = [];
            $sq = [];
            $actuals = [];
            $naiveAbs = [];
            $within = [5 => 0, 10 => 0];
            $n = 0;
            $devicesUsed = 0;

            foreach ($devices as $device) {
                // windowDays: null → seluruh histori (backtest butuh sampel
                // sebanyak mungkin untuk evaluasi, beda dari produksi yang
                // dibatasi jendela demi kecepatan).
                $data = $builder->build($device, $horizon, windowDays: null);
                if ($data === null) {
                    continue;
                }

                $features = $data['features'];
                $targets = $data['targets'];
                $total = count($features);

                $cut = (int) floor($total * $trainRatio);
                $testStart = $cut + $embargo;

                // Butuh cukup data latih (>=10) dan minimal ada 1 sampel uji.
                if ($cut < 10 || $testStart >= $total) {
                    continue;
                }

                $model = new LinearRegression();
                try {
                    $model->fit(array_slice($features, 0, $cut), array_slice($targets, 0, $cut));
                } catch (RuntimeException) {
                    continue; // data latih kurang bervariasi
                }
                $devicesUsed++;

                for ($i = $testStart; $i < $total; $i++) {
                    $predicted = $model->predict($features[$i]);
                    $actual = $targets[$i];
                    $error = abs($predicted - $actual);

                    $abs[] = $error;
                    $sq[] = ($predicted - $actual) ** 2;
                    $actuals[] = $actual;
                    // Baseline persistence: tebak "air tetap" = nilai sekarang (fitur ke-0).
                    $naiveAbs[] = abs($features[$i][0] - $actual);

                    foreach ($tolerances as $t) {
                        if ($error <= $t) {
                            $within[(int) $t]++;
                        }
                    }
                    $n++;
                }
            }

            if ($n === 0) {
                $rows[] = ["+{$horizon} mnt", '—', '—', '—', '—', '—', '—', '—'];

                continue;
            }

            $mae = array_sum($abs) / $n;
            $rmse = sqrt(array_sum($sq) / $n);
            $naiveMae = array_sum($naiveAbs) / $n;

            $mean = array_sum($actuals) / $n;
            $ssTot = 0.0;
            foreach ($actuals as $a) {
                $ssTot += ($a - $mean) ** 2;
            }
            $ssRes = array_sum($sq);
            $r2 = $ssTot > 0 ? 1 - ($ssRes / $ssTot) : null;

            $rows[] = [
                "+{$horizon} mnt",
                $n,
                number_format($mae, 2),
                number_format($rmse, 2),
                $r2 === null ? '—' : number_format($r2, 3),
                round($within[5] / $n * 100) . '%',
                round($within[10] / $n * 100) . '%',
                number_format($naiveMae, 2),
            ];

            $grandN += $n;
            $grandAbs += array_sum($abs);
            $grandSq += array_sum($sq);
            $grandNaiveAbs += array_sum($naiveAbs);
        }

        $this->newLine();
        $this->table(
            ['Horizon', 'N uji', 'MAE(cm)', 'RMSE(cm)', 'R²', '≤5cm', '≤10cm', 'MAE naif'],
            $rows,
        );

        if ($grandN > 0) {
            $overallMae = $grandAbs / $grandN;
            $overallNaive = $grandNaiveAbs / $grandN;
            $verdict = $overallMae < $overallNaive
                ? 'model MENGALAHKAN baseline naif ✔'
                : 'model TIDAK lebih baik dari baseline naif ✘ (perlu perbaikan)';

            $this->newLine();
            $this->info(sprintf(
                'Keseluruhan: %d prediksi diuji · MAE %.2f cm · RMSE %.2f cm · MAE naif %.2f cm — %s',
                $grandN,
                $overallMae,
                sqrt($grandSq / $grandN),
                $overallNaive,
                $verdict,
            ));
            $this->line('<comment>Baca:</comment> "≤5cm/≤10cm" = % prediksi yang meleset dalam toleransi itu. '
                . '"MAE naif" = model persistence ("air tetap") sebagai pembanding.');
        } else {
            $this->warn('Data belum cukup untuk backtest — butuh lebih banyak histori sensor per device.');
        }

        return self::SUCCESS;
    }
}
