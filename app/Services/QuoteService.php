<?php

namespace App\Services;

use App\Models\Material;
use App\Models\ModelFile;
use App\Models\PricingRule;
use App\Models\ThreeDModel;

class QuoteService
{
    /**
     * Calculate a quote based on configuration.
     *
     * @param array $config Configuration array with:
     *   - three_d_model_id (optional)
     *   - volume_mm3 (if no model)
     *   - material_id
     *   - color
     *   - quantity
     *   - layer_height (0.12, 0.20, 0.28)
     *   - infill (10-100)
     *   - shipping_enabled
     *   - country
     * @return array Quote breakdown
     */
    public function calculate(array $config): array
    {
        // Get volume
        $volumeMm3 = $this->getVolume($config);
        
        // Get material
        $material = Material::findOrFail($config['material_id']);
        
        // Calculate weight in grams
        $weightGrams = $this->calculateWeight($volumeMm3, $material);
        
        // Find pricing rule
        $pricingRule = $this->findPricingRule($material, $volumeMm3, $weightGrams);
        
        if (!$pricingRule) {
            throw new \Exception('No pricing rule found for this configuration');
        }
        
        // Calculate material cost
        $materialCost = $this->calculateMaterialCost($weightGrams, $pricingRule);
        
        // Estimate print time and calculate machine cost
        $printTimeHours = $this->estimatePrintTime($volumeMm3, $config['layer_height'] ?? 0.20);
        $machineCost = $printTimeHours * ($pricingRule->machine_time_rate ?? 0);
        
        // Setup fee
        $setupFee = $pricingRule->setup_fee ?? 0;
        
        // Subtotal before margin
        $subtotalBeforeMargin = $materialCost + $machineCost + $setupFee;
        
        // Apply margin
        $margin = $subtotalBeforeMargin * ($pricingRule->margin_percent / 100);
        $subtotal = $subtotalBeforeMargin + $margin;
        
        // Apply quantity
        $quantity = max(1, $config['quantity'] ?? 1);
        $subtotal = $subtotal * $quantity;
        
        // Ensure minimum price
        $subtotal = max($subtotal, $pricingRule->min_price ?? 0);
        
        // Calculate VAT
        $vatEnabled = config('modela.pricing.vat_enabled', true);
        $vatRate = config('modela.pricing.vat_rate', 0.24);
        $vatAmount = $vatEnabled ? $subtotal * $vatRate : 0;
        
        // Calculate shipping
        $shippingCost = 0;
        if ($config['shipping_enabled'] ?? true) {
            $shippingCost = $this->calculateShipping($config['country'] ?? 'GR', $weightGrams);
        }
        
        // Total
        $total = $subtotal + $vatAmount + $shippingCost;
        
        return [
            'volume_mm3' => round($volumeMm3, 2),
            'weight_grams' => round($weightGrams, 2),
            'print_time_hours' => round($printTimeHours, 2),
            'breakdown' => [
                'material_cost' => round($materialCost * $quantity, 2),
                'machine_cost' => round($machineCost * $quantity, 2),
                'setup_fee' => round($setupFee * $quantity, 2),
                'margin' => round($margin * $quantity, 2),
            ],
            'subtotal' => round($subtotal, 2),
            'vat_rate' => $vatRate,
            'vat_amount' => round($vatAmount, 2),
            'shipping_cost' => round($shippingCost, 2),
            'total' => round($total, 2),
            'currency' => config('modela.pricing.default_currency', 'EUR'),
            'quantity' => $quantity,
            'pricing_rule_id' => $pricingRule->id,
        ];
    }
    
    /**
     * Get volume from model or config.
     */
    protected function getVolume(array $config): float
    {
        if (isset($config['volume_mm3'])) {
            return (float) $config['volume_mm3'];
        }
        
        if (isset($config['three_d_model_id'])) {
            $model = ThreeDModel::findOrFail($config['three_d_model_id']);
            $file = $model->files()->where('processing_status', 'completed')->first();
            
            if ($file && isset($file->metadata['volume_mm3'])) {
                return (float) $file->metadata['volume_mm3'];
            }
            
            throw new \Exception('Model file metadata not available');
        }
        
        throw new \Exception('Volume or model ID required');
    }
    
    /**
     * Calculate weight in grams.
     */
    protected function calculateWeight(float $volumeMm3, Material $material): float
    {
        // Convert mm³ to cm³
        $volumeCm3 = $volumeMm3 / 1000;
        
        // Weight = volume * density
        $weight = $volumeCm3 * $material->density_g_cm3;
        
        // Apply waste factor
        $wasteFactor = config('modela.pricing.material_waste_factor', 1.15);
        
        return $weight * $wasteFactor;
    }
    
    /**
     * Find the best matching pricing rule.
     */
    protected function findPricingRule(Material $material, float $volumeMm3, float $weightGrams): ?PricingRule
    {
        return PricingRule::active()
            ->where(function ($query) use ($material) {
                $query->where('material_id', $material->id)
                      ->orWhereNull('material_id');
            })
            ->where(function ($query) use ($volumeMm3, $weightGrams) {
                $query->where(function ($q) use ($volumeMm3) {
                    $q->where('min_volume', '<=', $volumeMm3)
                      ->where('max_volume', '>=', $volumeMm3);
                })->orWhere(function ($q) use ($volumeMm3) {
                    $q->whereNull('min_volume')
                      ->whereNull('max_volume');
                });
            })
            ->first();
    }
    
    /**
     * Calculate material cost.
     */
    protected function calculateMaterialCost(float $weightGrams, PricingRule $pricingRule): float
    {
        if ($pricingRule->price_per_gram) {
            return $weightGrams * $pricingRule->price_per_gram;
        }
        
        // Fallback to material base cost
        $material = $pricingRule->material;
        if ($material) {
            $pricePerGram = $material->base_cost_per_kg / 1000;
            return $weightGrams * $pricePerGram;
        }
        
        return 0;
    }
    
    /**
     * Estimate print time in hours.
     */
    protected function estimatePrintTime(float $volumeMm3, float $layerHeight): float
    {
        // Base time estimation: larger volumes take longer
        // This is a simplified calculation - real time depends on many factors
        $volumeCm3 = $volumeMm3 / 1000;
        
        // Base time: approximately 1 hour per 10cm³ at 0.20mm
        $baseTime = $volumeCm3 / 10;
        
        // Apply layer height factor
        $layerFactors = config('modela.pricing.time_factor_by_layer_height', [
            '0.12' => 1.5,
            '0.20' => 1.0,
            '0.28' => 0.7,
        ]);
        
        $layerKey = number_format($layerHeight, 2);
        $layerFactor = $layerFactors[$layerKey] ?? 1.0;
        
        return max(0.5, $baseTime * $layerFactor); // Minimum 30 minutes
    }
    
    /**
     * Calculate shipping cost.
     */
    protected function calculateShipping(string $country, float $weightGrams): float
    {
        // Simple shipping calculation
        // In production, this would integrate with shipping APIs
        
        $baseShipping = config('modela.pricing.default_shipping_cost', 5.00);
        
        // Greece: base shipping
        if ($country === 'GR') {
            return $baseShipping;
        }
        
        // EU: higher shipping
        $euCountries = ['AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE'];
        if (in_array($country, $euCountries)) {
            return $baseShipping * 2;
        }
        
        // Rest of world
        return $baseShipping * 3;
    }
}
