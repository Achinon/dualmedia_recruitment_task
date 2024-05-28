<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\Common\Collections\ArrayCollection;
use App\Dto\CartDto;
use Achinon\ToolSet\Dumper;
use App\Service\OrderService;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/order')]
class OrderController extends AbstractController
{
    /**
     * @throws \Exception
     */
    #[Route('/create', name: 'order_create', methods: ['POST'])]
    public function createOrder(CartDto $cart_dto, OrderService $order_service, EntityManagerInterface $entity_manager)
    {
        $entity_manager->beginTransaction();
        try{
            $order = $order_service->createOrder($cart_dto->getEntries());
            $entity_manager->persist($order);
            $entity_manager->flush();
            $entity_manager->commit();
        }
        catch(\Exception $exception) {
            $entity_manager->rollback();
            throw $exception;
        }

        return $this->json([$order->serialize()]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/{id}', name: 'order_get', methods: ['GET'])]
    public function getOrder(string $id, OrderService $order_service, EntityManagerInterface $entity_manager)
    {
        $order = $order_service->getOrder($id);

        return $this->json([$order->serialize()]);
    }
}