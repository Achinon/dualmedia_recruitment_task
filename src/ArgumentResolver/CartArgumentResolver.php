<?php

namespace App\ArgumentResolver;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use App\Utils\Utils;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Dto\CartDto;
use App\Dto\CartEntryDto;
use Achinon\ToolSet\Dumper;
use App\Repository\ProductRepository;

class CartArgumentResolver implements ValueResolverInterface
{
    public function __construct(private readonly ProductRepository $product_repository)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($argument)) return [];

        $data = $request->getContent();
        $JSON = json_decode($data, 1);

        if(gettype($JSON) != 'array'){
            throw new BadRequestHttpException('Invalid JSON');
        }

        $cartDto = new CartDto();
        foreach ($JSON as $product_id => $quantity) {
            if(!is_numeric($quantity)){
                throw new BadRequestHttpException(sprintf('Quantity (%s) must be numeric.', $quantity));
            }
            $cartDto->addEntry(new CartEntryDto($product_id, $quantity));
        }

        yield $cartDto;
    }

    public function supports(ArgumentMetadata $argument): bool
    {
        return $argument->getType() === CartDto::class;
    }
}