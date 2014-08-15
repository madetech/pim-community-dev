<?php

namespace Pim\Bundle\CatalogBundle\Model;

use Symfony\Component\HttpFoundation\File\File;

/**
 * Abstract product media (backend type entity)
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2014 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class AbstractProductMedia extends AbstractMedia
{

    /**
     * @var AbstractProductValue
     */
    protected $value;

    /**
     * Set the product value
     *
     * @param ProductValueInterface $value
     *
     * @return AbstractProductMedia
     */
    public function setValue(ProductValueInterface $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get the product value
     *
     * @return ProductValueInterface
     */
    public function getValue()
    {
        return $this->value;
    }

}
