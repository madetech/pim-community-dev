<?php

namespace Pim\Bundle\EnrichBundle\AbstractController;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Validator\ValidatorInterface;
use Symfony\Component\Translation\TranslatorInterface;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Base abstract controller for managing entities
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractDoctrineController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * Constructor
     *
     * @param Request                  $request
     * @param EngineInterface          $templating
     * @param RouterInterface          $router
     * @param SecurityContextInterface $securityContext
     * @param FormFactoryInterface     $formFactory
     * @param ValidatorInterface       $validator
     * @param TranslatorInterface      $translator
     * @param EventDispatcherInterface $eventDispatcher
     * @param ManagerRegistry          $doctrine
     */
    public function __construct(
        Request $request,
        EngineInterface $templating,
        RouterInterface $router,
        SecurityContextInterface $securityContext,
        FormFactoryInterface $formFactory,
        ValidatorInterface $validator,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        ManagerRegistry $doctrine
    ) {
        parent::__construct(
            $request,
            $templating,
            $router,
            $securityContext,
            $formFactory,
            $validator,
            $translator,
            $eventDispatcher
        );

        $this->doctrine = $doctrine;
    }

    /**
     * Returns the Doctrine registry service.
     *
     * @return ManagerRegistry
     */
    protected function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * Returns the Doctrine manager
     *
     * @return ObjectManager
     */
    protected function getManager()
    {
        return $this->doctrine->getManager();
    }

    /**
     * Returns the Doctrine manager for the given class
     *
     * @param string $class
     *
     * @return ObjectManager
     */
    protected function getManagerForClass($class)
    {
        return $this->doctrine->getManagerForClass($class);
    }

    /**
     * @param string $className
     *
     * @return ObjectRepository
     */
    protected function getRepository($className)
    {
        return $this->doctrine->getRepository($className);
    }

    /**
     * Persist an object
     *
     * @param object  $object
     * @param boolean $flush
     */
    protected function persist($object, $flush = true)
    {
        $manager = $this->doctrine->getManagerForClass(get_class($object));
        $manager->persist($object);

        if ($flush) {
            $manager->flush();
        }
    }

    /**
     * Remove an object
     *
     * @param object  $object
     * @param boolean $flush
     */
    protected function remove($object, $flush = true)
    {
        $manager = $this->doctrine->getManagerForClass(get_class($object));
        $manager->remove($object);

        if ($flush) {
            $manager->flush();
        }
    }

    /**
     * Find an entity or throw a 404
     *
     * @param string  $className Example: 'PimCatalogBundle:Category'
     * @param integer $id        The id of the entity
     *
     * @throws NotFoundHttpException
     * @return object
     */
    protected function findOr404($className, $id)
    {
        $result = $this->getRepository($className)->find($id);

        if (!$result) {
            throw $this->createNotFoundException(sprintf('%s entity not found', $className));
        }

        return $result;
    }
}
