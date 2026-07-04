<?php

namespace App\Console\Commands;

use App\Services\RobSyncService;
use Illuminate\Console\Command;
use Throwable;

class RobData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rob:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sinkronisasi data sensor terbaru dari iot.ketapangkab.go.id/api/rob';

    /**
     * Execute the console command.
     */
    public function handle(RobSyncService $service): int
    {
        $this->info('Memulai sinkronisasi data sensor dari API');

        try {
            $count = $service->sync();
        } catch (Throwable $e) {
            $this->error("Gagal sinkronisasi data sensor: {$e->getMessage()}");
            return self::FAILURE;
        }

        $this->info("Selesai. {$count} device berhasil diproses.");
        return self::SUCCESS;
    }
}
