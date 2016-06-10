<?php

namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\AbstractHandlerConfigValidator;

class AbstractHandlerConfigValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'  => 'stream',
            'level' => 'INFO',
        ];

        $abstractValidator = new AbstractHandlerConfigValidator($configArray);
        $this->assertTrue($abstractValidator->validate());
    }

    public function testHasLevel()
    {
        $configArray = [
            'type'  => 'stream',
            'level' => 'INFO',
        ];

        $abstractValidator = new AbstractHandlerConfigValidator($configArray);
        $this->assertTrue($abstractValidator->hasLevel());
    }

    public function testNotHasLevel()
    {
        $configArray = [
            'type' => 'stream',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $abstractValidator = new AbstractHandlerConfigValidator($configArray);
        $abstractValidator->hasLevel();
    }
}
