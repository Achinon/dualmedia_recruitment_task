<?php

namespace App\Service\PriceCalculators;

use App\Interfaces\PriceCalculator;
use App\Entity\OrderProduct;

class TotalPriceCalculator implements PriceCalculator
{
    public static function calculate(OrderProduct $order_product): float
    {
        $base_price = BasePriceCalculator::calculate($order_product);
        $vat_price = VatPriceCalculator::calculate($order_product);
        return $base_price + $vat_price;
    }
}