<?php

namespace Pim\Bundle\DataGridBundle\Datasource;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Pim\Bundle\CatalogBundle\Repository\MassActionRepositoryInterface;
use Pim\Bundle\DataGridBundle\Datagrid\Product\ContextConfigurator;
use Pim\Bundle\DataGridBundle\Datasource\ResultRecord\HydratorInterface;

/**
 * Pim agnostic datasource
 *
 * @author    Julien Janvier <julien.janvier@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Datasource implements DatasourceInterface, ParameterizableInterface
{
    /** @var \Doctrine\ORM\QueryBuilder|\Doctrine\ODM\MongoDB\Query\Builder */
    protected $qb;

    /** @var ObjectManager */
    protected $om;

    /** @var ObjectRepository */
    protected $repository;

    /** @var MassActionRepositoryInterface */
    protected $massActionRepository;

    /** @var HydratorInterface */
    protected $hydrator;

    /** @var array */
    protected $configuration;

    /** @var array */
    protected $parameters = [];

    /**
     * @param ObjectManager     $om
     * @param HydratorInterface $hydrator
     */
    public function __construct(ObjectManager $om, HydratorInterface $hydrator)
    {
        $this->om       = $om;
        $this->hydrator = $hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function setMassActionRepository(MassActionRepositoryInterface $massActionRepository)
    {
        $this->massActionRepository = $massActionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function process(DatagridInterface $grid, array $config)
    {
        $this->configuration = $config;

        if (isset($config['repository_method']) && $method = $config['repository_method']) {
            if (isset($config[ContextConfigurator::REPOSITORY_PARAMETERS_KEY])) {
                $this->qb = $this->getRepository()->$method($config[ContextConfigurator::REPOSITORY_PARAMETERS_KEY]);
            } else {
                $this->qb = $this->getRepository()->$method();
            }
        } else {
            $this->qb = $this->getRepository()->createQueryBuilder('o');
        }

        $grid->setDatasource(clone $this);
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
    {
        return $this->hydrator->hydrate($this->qb);
    }

    /**
     * {@inheritdoc}
     */
    public function getQueryBuilder()
    {
        return $this->qb;
    }

    /**
     * {@inheritdoc}
     */
    public function setQueryBuilder($qb)
    {
        $this->qb = $qb;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectManager()
    {
        return $this->om;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        if (null === $this->repository) {
            $this->repository = $this->om->getRepository($this->getConfiguration('entity'));
        }

        return $this->repository;
    }

    /**
     * {@inheritdoc}
     */
    public function getMassActionRepository()
    {
        if (null === $this->massActionRepository) {
            return $this->getRepository();
        }

        return $this->massActionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function setParameters($parameters)
    {
        $this->parameters += $parameters;

        if ($this->qb instanceof QueryBuilder) {
            $this->qb->setParameters($this->parameters);
        }

        return $this;
    }

    /**
     * Get a configuration value
     *
     * @param string  $key
     * @param boolean $isRequired
     *
     * @return mixed
     *
     * @throws \LogicException
     * @throws \Exception
     */
    protected function getConfiguration($key, $isRequired = true)
    {
        if (!$this->configuration) {
            throw new \LogicException('Datasource is not yet built. You need to call process method before');
        }

        if ($isRequired && !isset($this->configuration[$key])) {
            throw new \Exception(sprintf('"%s" expects to be configured with "%s"', get_class($this), $key));
        }

        return $this->configuration[$key];
    }
}
