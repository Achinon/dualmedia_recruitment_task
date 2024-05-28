<?php

namespace App\Service\PriceCalculators;

use App\Interfaces\PriceCalculator;
use App\Entity\OrderProduct;
use Achinon\ToolSet\Dumper;

class BasePriceCalculator implements PriceCalculator
{
    public static function calculate(OrderProduct $order_product): float
    {
        return $order_product->getProduct()->getPrice() * $order_product->getQuantity();
    }
}