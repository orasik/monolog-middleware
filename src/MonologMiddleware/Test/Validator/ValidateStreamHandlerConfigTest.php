<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\ValidateStreamHandlerConfig;

class ValidateStreamHandlerConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'  => 'stream',
            'level' => 'INFO',
            'path'  => '/my/path',
        ];

        $streamValidator = new ValidateStreamHandlerConfig($configArray);
        $this->assertTrue($streamValidator->validate());
    }

    public function testHasPath()
    {
        $configArray = [
            'type'  => 'stream',
            'level' => 'INFO',
            'path'  => '/my/path',
        ];

        $streamValidator = new ValidateStreamHandlerConfig($configArray);
        $this->assertTrue($streamValidator->hasPath());
    }

    public function testNotHasPath()
    {
        $configArray = [
            'type' => 'stream',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $streamValidator = new ValidateStreamHandlerConfig($configArray);
        $streamValidator->hasPath();

    }
}
