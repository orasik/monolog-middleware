<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\StreamHandlerConfigValidator;

class StreamHandlerConfigValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'  => 'stream',
            'level' => 'INFO',
            'path'  => '/my/path',
        ];

        $streamValidator = new StreamHandlerConfigValidator($configArray);
        $this->assertTrue($streamValidator->validate());
    }

    public function testHasPath()
    {
        $configArray = [
            'type'  => 'stream',
            'level' => 'INFO',
            'path'  => '/my/path',
        ];

        $streamValidator = new StreamHandlerConfigValidator($configArray);
        $this->assertTrue($streamValidator->hasPath());
    }

    public function testNotHasPath()
    {
        $configArray = [
            'type' => 'stream',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $streamValidator = new StreamHandlerConfigValidator($configArray);
        $streamValidator->hasPath();

    }
}
