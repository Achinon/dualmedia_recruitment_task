<?php

namespace App\Interfaces;

use App\Entity\OrderProduct;

interface PriceCalculator
{
    public static function calculate(OrderProduct $order_product): float;
}