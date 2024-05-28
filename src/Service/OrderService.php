<?php

namespace App\Service;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\Collection;
use App\Dto\CartEntryDto;
use App\Entity\Order;
use App\Repository\ProductRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Entity\OrderProduct;
use App\Service\PriceCalculators\TotalPriceCalculator;
use App\Service\PriceCalculators\BasePriceCalculator;
use App\Service\PriceCalculators\VatPriceCalculator;
use Achinon\ToolSet\Dumper;

class OrderService
{
    public function __construct(private readonly OrderRepository   $order_repository,
                                private readonly ProductRepository $product_repository)
    {
    }

    /** @param Collection<CartEntryDto> $products */
    public function createOrder(Collection $products): Order
    {
        $order = new Order();

        $net_price = $vat_price = $total_price = 0.0;

        $calculator_collector = new PriceCalculatorCollector(
          BasePriceCalculator::class,
          TotalPriceCalculator::class,
          VatPriceCalculator::class,
        );

        /** @var CartEntryDto $product_dto */
        foreach($products as $product_dto) {
            $product = $this->product_repository->find($product_id = $product_dto->product_id);
            if(is_null($product)) {
                throw new BadRequestHttpException(sprintf('Product ID (%s) does not exist.', $product_id));
            }
            $order_product = new OrderProduct($product);
            $order_product->setQuantity($product_dto->quantity);
            $order->addOrderProduct($order_product);

            $prices = $calculator_collector->calculate($order_product);

            $net_price += $prices[BasePriceCalculator::class];
            $vat_price += $prices[VatPriceCalculator::class];
            $total_price += $prices[TotalPriceCalculator::class];

        }

        $order->setTotalPrice($total_price)
              ->setNetPrice($net_price)
              ->setVatPrice($vat_price);

        return $order;
    }

    public function getOrder(int $id)
    {
        $order = $this->order_repository->find($id);
        if(is_null($order)) {
            throw new BadRequestHttpException(sprintf('Order ID (%s) does not exist.', $id));
        }
        return $order;
    }
}