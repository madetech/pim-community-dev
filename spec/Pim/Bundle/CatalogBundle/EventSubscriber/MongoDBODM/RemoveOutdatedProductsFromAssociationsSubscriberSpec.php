<?php

namespace spec\Pim\Bundle\CatalogBundle\EventSubscriber\MongoDBODM;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\Doctrine\MongoDBODM\ProductRepository;
use Pim\Bundle\CatalogBundle\Entity\Repository\AssociationTypeRepository;
use Pim\Bundle\CatalogBundle\Event\ProductEvents;
use Pim\Bundle\CatalogBundle\Model\ProductInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * @require Doctrine\ODM\MongoDB\DocumentManager
 */
class RemoveOutdatedProductsFromAssociationsSubscriberSpec extends ObjectBehavior
{
    function let(ProductRepository $productRepository, AssociationTypeRepository $assocTypeRepository)
    {
        $this->beConstructedWith($productRepository, $assocTypeRepository);
    }

    function it_is_an_event_subscriber()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_to_some_product_events()
    {
        $this->getSubscribedEvents()->shouldReturn(
            [
                ProductEvents::POST_REMOVE      => 'removeAssociatedProduct',
                ProductEvents::POST_MASS_REMOVE => 'removeAssociatedProducts'
            ]
        );
    }

    function it_removed_associated_product(
        $productRepository,
        $assocTypeRepository,
        GenericEvent $event,
        ProductInterface $product
    ) {
        $event->getSubject()->willReturn($product);
        $product->getId()->willReturn(2);

        $assocTypeRepository->countAll()->willReturn(5);
        $productRepository->removeAssociatedProduct(2, 5)->shouldBeCalled();

        $this->removeAssociatedProduct($event);
    }

    function it_removed_associated_products_on_many_products(
        $productRepository,
        $assocTypeRepository,
        GenericEvent $event
    ) {
        $event->getSubject()->willReturn([1, 2, 3]);
        $assocTypeRepository->countAll()->willReturn(5);

        $productRepository->removeAssociatedProduct(1, 5)->shouldBeCalled();
        $productRepository->removeAssociatedProduct(2, 5)->shouldBeCalled();
        $productRepository->removeAssociatedProduct(3, 5)->shouldBeCalled();

        $this->removeAssociatedProducts($event);
    }
}
