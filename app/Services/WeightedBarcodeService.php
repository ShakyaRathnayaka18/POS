<?php

namespace App\Services;

use App\Models\Product;

class WeightedBarcodeService
{
    /**
     * Detect if barcode is weighted format (11 digits).
     */
    public function isWeightedBarcode(string $barcode): bool
    {
        return strlen($barcode) === 11 && ctype_digit($barcode);
    }

    /**
     * Parse weighted barcode into product code and weight.
     *
     * @return array{product_code: string, weight_grams: int}|null
     */
    public function parseWeightedBarcode(string $barcode): ?array
    {
        if (! $this->isWeightedBarcode($barcode)) {
            return null;
        }

        $productCode = substr($barcode, 0, 6);
        $weightGrams = (int) substr($barcode, 6, 5);

        if ($weightGrams === 0) {
            return null;
        }

        return [
            'product_code' => $productCode,
            'weight_grams' => $weightGrams,
        ];
    }

    /**
     * Convert grams to kilograms (decimal).
     */
    public function gramsToKg(int $grams): float
    {
        return round($grams / 1000, 3);
    }

    /**
     * Find product by weighted code.
     */
    public function findProductByCode(string $productCode): ?Product
    {
        return Product::where('weighted_product_code', $productCode)
            ->where('is_weighted', true)
            ->first();
    }
}
