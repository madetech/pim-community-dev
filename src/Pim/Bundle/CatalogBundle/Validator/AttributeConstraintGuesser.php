<?php

namespace Pim\Bundle\CatalogBundle\Validator;

use Pim\Bundle\CatalogBundle\Model\AbstractAttribute;
use Symfony\Component\Validator\Constraints;
use Pim\Bundle\CatalogBundle\AttributeType\AbstractAttributeType;

/**
 * Attribute constraint guesser
 *
 * @author    Nicolas Dupont <nicolas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class AttributeConstraintGuesser implements ConstraintGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportAttribute(AbstractAttribute $attribute)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function guessConstraints(AbstractAttribute $attribute)
    {
        $constraints = array();

        if ($attribute->isRequired()) {
            $constraints[] = new Constraints\NotBlank();
        }

        switch ($attribute->getBackendType()) {
            case AbstractAttributeType::BACKEND_TYPE_DATE:
                $constraints[] = new Constraints\Date();
                break;
            case AbstractAttributeType::BACKEND_TYPE_DATETIME:
                $constraints[] = new Constraints\DateTime();
                break;
        }

        return $constraints;
    }
}
