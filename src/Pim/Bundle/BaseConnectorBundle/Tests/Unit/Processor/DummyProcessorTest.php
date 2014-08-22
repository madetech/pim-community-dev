<?php

namespace Pim\Bundle\BaseConnectorBundle\Tests\Unit\Processor;

use Pim\Bundle\BaseConnectorBundle\Processor\DummyProcessor;

/**
 * Tests related class
 *
 * @author    Antoine Guigan <antoine@akeneo.com>
 * @copyright 2013 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class DummyProcessorTest extends \PHPUnit_Framework_TestCase
{
    public function testProcess()
    {
        $processor = new DummyProcessor();
        $this->assertNull($processor->process(array()));
    }

    public function testGetConfigurationFields()
    {
        $processor = new DummyProcessor();
        $this->assertEquals(array(), $processor->getConfigurationFields());
    }
}
