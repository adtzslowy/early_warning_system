<?php

declare(strict_types=1);

namespace App\Prediction;

use RuntimeException;

/**
 * Multiple Linear Regression via Ordinary Least Squares (normal
 * equations): b = (XᵀX)⁻¹ Xᵀy. Kolom intercept ditambahkan otomatis.
 */
final class LinearRegression
{
    /** @var float[] */
    private array $coefficients = [];

    /**
     * @param array<int, float[]> $features setiap baris = 1 sample, TANPA kolom intercept
     * @param float[] $targets
     */
    public function fit(array $features, array $targets): void
    {
        if ($features === []) {
            throw new RuntimeException('Tidak ada data training.');
        }

        $designMatrix = array_map(
            fn (array $row) => array_merge([1.0], $row),
            $features
        );

        $xTranspose = LinearAlgebra::transpose($designMatrix);
        $xtx = LinearAlgebra::multiply($xTranspose, $designMatrix);
        $xtxInverse = LinearAlgebra::invert($xtx);
        $xty = LinearAlgebra::multiplyVector($xTranspose, $targets);

        $this->coefficients = LinearAlgebra::multiplyVector($xtxInverse, $xty);
    }

    /**
     * @param float[] $features harus urutan & jumlah sama persis dengan fit()
     */
    public function predict(array $features): float
    {
        if ($this->coefficients === []) {
            throw new RuntimeException('Model belum di-fit — panggil fit() dulu.');
        }

        $sum = $this->coefficients[0]; // intercept

        foreach ($features as $i => $value) {
            $sum += $this->coefficients[$i + 1] * $value;
        }

        return $sum;
    }

    /** @return float[] [intercept, b1, b2, ...] — berguna buat ditampilkan di BAB IV */
    public function coefficients(): array
    {
        return $this->coefficients;
    }
}
