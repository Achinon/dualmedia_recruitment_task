<?php

namespace App\Service;

use App\Interfaces\PriceCalculator;
use App\Entity\OrderProduct;
use Exception;
use Achinon\ToolSet\Dumper;

class PriceCalculatorCollector
{
    /** @var class-string<PriceCalculator>[] */
    private array $price_calculators;

    /** @param class-string<PriceCalculator>[] $price_calculators
     * @throws Exception
     */
    public function __construct(...$price_calculators)
    {
        foreach($price_calculators as $calculator){
            if (!class_exists($calculator)) {
                throw new Exception('Provided class does not exist.');
            }
            if (!in_array(PriceCalculator::class, class_implements($calculator))) {
                throw new Exception(sprintf('Class added to %s must be the implementation of %s', static::class, PriceCalculator::class));
            }
        }
        $this->price_calculators = $price_calculators;
    }

    public function calculate(OrderProduct $order_product): array
    {
        $prices = [];
        foreach ($this->price_calculators as $calculator) {
            $prices[$calculator] = $calculator::calculate($order_product);
//            Dumper::generic($this->price_calculators);
        }

        return $prices;
    }
}