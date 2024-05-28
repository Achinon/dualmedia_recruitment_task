<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Achinon\ToolSet\Time;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Service\PriceCalculators\BasePriceCalculator;
use App\Service\PriceCalculatorCollector;
use App\Service\PriceCalculators\TotalPriceCalculator;
use App\Service\PriceCalculators\VatPriceCalculator;
use Achinon\ToolSet\Parser;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $timestamp_ms;

    #[ORM\Column(type: Types::DECIMAL, scale: 2)]
    private ?string $total_price = null;
    #[ORM\Column(type: Types::DECIMAL, scale: 2)]
    private ?string $vat_price = null;
    #[ORM\Column(type: Types::DECIMAL, scale: 2)]
    private ?string $net_price = null;

    /**
     * @var Collection<int, OrderProduct>
     */
    #[ORM\OneToMany(targetEntity: OrderProduct::class, mappedBy: 'order_entity', cascade: ['persist', 'remove'])]
    private Collection $order_products;

    public function __construct() {
        $this->timestamp_ms = Time::currentMs();
        $this->order_products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestampMs(): ?string
    {
        return $this->timestamp_ms;
    }

    public function getTotalPrice(): ?string
    {
        return $this->total_price;
    }

    public function setTotalPrice(string $total_price): static
    {
        $this->total_price = $total_price;

        return $this;
    }

    /**
     * @return Collection<int, OrderProduct>
     */
    public function getOrderProducts(): Collection
    {
        return $this->order_products;
    }

    public function addOrderProduct(OrderProduct $product): static
    {
        if (!$this->order_products->contains($product)) {
            $this->order_products->add($product);
            $product->setOrder($this);
        }

        return $this;
    }

    public function setNetPrice(float $net_price): static
    {
        $this->net_price = $net_price;
        return $this;
    }

    public function setVatPrice(float $price): static
    {
        $this->vat_price = $price;
        return $this;
    }

    public function getNetPrice(): ?string
    {
        return $this->net_price;
    }

    public function getVatPrice(): ?string
    {
        return $this->vat_price;
    }

    public function serialize(): array
    {
        $calc_collection = new PriceCalculatorCollector(
          BasePriceCalculator::class,
          TotalPriceCalculator::class,
          VatPriceCalculator::class,
        );

        $f = function(OrderProduct $op) use ($calc_collection) {
            $prices = $calc_collection->calculate($op);
            return [
                'product_id' => $op->getProduct()->getId(),
                'name' => $op->getProduct()->getName(),
                'quantity' => $op->getQuantity(),
                'price' => [
                  'net' => Parser::amountToString($prices[BasePriceCalculator::class]),
                  'vat' => Parser::amountToString($prices[VatPriceCalculator::class]),
                  'total' => Parser::amountToString($prices[TotalPriceCalculator::class]),
                ]
              ];
        };

        $products = $this->getOrderProducts();
        $product_count = $products->count();
        return [
            'order_id' => $this->getId(),
            'product_count' => $product_count,
            'products' => array_map($f, $products->toArray()),
            'time_order_placed' => Time::msToDate($this->getTimestampMs()),
            'order_price' => [
              'net' => Parser::amountToString($this->getNetPrice()),
              'vat' => Parser::amountToString($this->getVatPrice()),
              'total' => Parser::amountToString($this->getTotalPrice()),
            ]
          ];
    }
}
