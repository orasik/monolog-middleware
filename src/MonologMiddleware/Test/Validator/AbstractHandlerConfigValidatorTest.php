<?php

namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\AbstractHandlerConfigValidator;
use PHPUnit\Framework\TestCase;

class AbstractHandlerConfigValidatorTest extends TestCase
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

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $abstractValidator = new AbstractHandlerConfigValidator($configArray);
        $abstractValidator->hasLevel();
    }
}
