<?php

namespace Pim\Bundle\CatalogBundle\Tests\Unit\DependencyInjection\Compiler;

use Pim\Bundle\CatalogBundle\DependencyInjection\Compiler\RegisterAttributeConstraintGuessersPass;

/**
 * Test related class
 *
 * @author    Gildas Quemener <gildas@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class RegisterAttributeConstraintGuessersPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->target = new RegisterAttributeConstraintGuessersPass();
    }

    /**
     * Test related method
     */
    public function testInstanceOfCompilerPassInterface()
    {
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface', $this->target);
    }

    /**
     * Test related method
     */
    public function testProcessUnknownAttributeConstraintGuesserService()
    {
        $container = $this->getContainerBuilderMock();

        $this->target->process($container);
    }

    /**
     * Test related method
     */
    public function testProcessTaggedAttributeConstraintGuessersService()
    {
        $definition = $this->getDefinitionMock();
        $container  = $this->getContainerBuilderMock(
            $definition,
            array('pim_catalog.validator.constraint_guesser.foo', 'pim_catalog.validator.constraint_guesser.bar')
        );

        $definition->expects($this->exactly(2))
            ->method('addMethodCall')
            ->with('addConstraintGuesser', $this->anything());

        $this->target->process($container);
    }

    /**
     * @param mixed $definition
     * @param array $taggedServices
     *
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function getContainerBuilderMock($definition = null, array $taggedServices = array())
    {
        $container = $this->getMock('Symfony\Component\DependencyInjection\ContainerBuilder');

        $container->expects($this->any())
            ->method('hasDefinition')
            ->with('pim_catalog.validator.constraint_guesser.chained_attribute')
            ->will($this->returnValue(null !== $definition));

        if ($definition) {
            $container->expects($this->any())
                ->method('getDefinition')
                ->will($this->returnValue($definition));

            $container->expects($this->any())
                ->method('findTaggedServiceIds')
                ->with('pim_catalog.constraint_guesser.attribute')
                ->will($this->returnValue($taggedServices));
        } else {
            $container->expects($this->never())
                ->method('getDefinition');
        }

        return $container;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    private function getDefinitionMock()
    {
        return $this->getMock('Symfony\Component\DependencyInjection\Definition');
    }
}
