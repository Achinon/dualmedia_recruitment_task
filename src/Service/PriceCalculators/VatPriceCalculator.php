<?php

namespace App\Service\PriceCalculators;

use App\Interfaces\PriceCalculator;
use App\Entity\OrderProduct;

class VatPriceCalculator implements PriceCalculator
{
    private const VAT_percent = 23;

    public static function calculate(OrderProduct $order_product): float
    {
        $base_price = BasePriceCalculator::calculate($order_product);
        return (self::VAT_percent / 100) * $base_price;
    }
}