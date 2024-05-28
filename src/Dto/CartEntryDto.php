<?php

namespace App\Dto;

class CartEntryDto
{
    public function __construct(public readonly string $product_id,
                                public readonly int    $quantity)
    {
    }
}