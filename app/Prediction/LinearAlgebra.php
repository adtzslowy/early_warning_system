<?php

declare(strict_types=1);

namespace App\Prediction;

use RuntimeException;

/**
 * Operasi matrix minimal yang dibutuhkan buat OLS: transpose,
 * perkalian matrix, perkalian matrix-vector, dan inversi
 * (Gauss-Jordan dengan partial pivoting).
 */
final class LinearAlgebra
{
    /** @param float[][] $matrix */
    public static function transpose(array $matrix): array
    {
        return array_map(null, ...$matrix);
    }

    /**
     * @param float[][] $a
     * @param float[][] $b
     * @return float[][]
     */
    public static function multiply(array $a, array $b): array
    {
        $bTransposed = self::transpose($b);
        $result = [];

        foreach ($a as $i => $rowA) {
            foreach ($bTransposed as $j => $colB) {
                $sum = 0.0;
                foreach ($rowA as $k => $value) {
                    $sum += $value * $colB[$k];
                }
                $result[$i][$j] = $sum;
            }
        }

        return $result;
    }

    /**
     * @param float[][] $matrix
     * @param float[] $vector
     * @return float[]
     */
    public static function multiplyVector(array $matrix, array $vector): array
    {
        return array_map(
            fn (array $row) => array_sum(array_map(fn ($a, $b) => $a * $b, $row, $vector)),
            $matrix
        );
    }

    /**
     * Inversi matrix persegi via Gauss-Jordan + partial pivoting.
     *
     * @param float[][] $matrix
     * @return float[][]
     *
     * @throws RuntimeException kalau matrix singular (data training
     *                          kurang bervariasi — mis. semua device
     *                          punya water_level yang identik terus).
     */
    public static function invert(array $matrix): array
    {
        $n = count($matrix);

        $aug = [];
        foreach ($matrix as $i => $row) {
            $aug[$i] = array_merge($row, array_fill(0, $n, 0.0));
            $aug[$i][$n + $i] = 1.0;
        }

        for ($col = 0; $col < $n; $col++) {
            $pivotRow = $col;
            $maxVal = abs($aug[$col][$col]);

            for ($r = $col + 1; $r < $n; $r++) {
                if (abs($aug[$r][$col]) > $maxVal) {
                    $maxVal = abs($aug[$r][$col]);
                    $pivotRow = $r;
                }
            }

            if ($maxVal < 1e-10) {
                throw new RuntimeException(
                    'Matrix singular — data training tidak cukup bervariasi untuk regresi.'
                );
            }

            if ($pivotRow !== $col) {
                [$aug[$col], $aug[$pivotRow]] = [$aug[$pivotRow], $aug[$col]];
            }

            $pivotVal = $aug[$col][$col];
            for ($k = 0; $k < 2 * $n; $k++) {
                $aug[$col][$k] /= $pivotVal;
            }

            for ($r = 0; $r < $n; $r++) {
                if ($r === $col) {
                    continue;
                }

                $factor = $aug[$r][$col];
                for ($k = 0; $k < 2 * $n; $k++) {
                    $aug[$r][$k] -= $factor * $aug[$col][$k];
                }
            }
        }

        $inverse = [];
        foreach ($aug as $i => $row) {
            $inverse[$i] = array_slice($row, $n);
        }

        return $inverse;
    }
}
