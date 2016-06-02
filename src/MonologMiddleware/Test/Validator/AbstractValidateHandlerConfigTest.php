<?php

namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\AbstractValidateHandlerConfig;

class AbstractValidateHandlerConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'  => 'stream',
            'level' => 'INFO',
        ];

        $abstractValidator = new AbstractValidateHandlerConfig($configArray);
        $this->assertTrue($abstractValidator->validate());
    }

    public function testHasLevel()
    {
        $configArray = [
            'type'  => 'stream',
            'level' => 'INFO',
        ];

        $abstractValidator = new AbstractValidateHandlerConfig($configArray);
        $this->assertTrue($abstractValidator->hasLevel());
    }

    public function testNotHasLevel()
    {
        $configArray = [
            'type' => 'stream',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $abstractValidator = new AbstractValidateHandlerConfig($configArray);
        $abstractValidator->hasLevel();
    }
}
