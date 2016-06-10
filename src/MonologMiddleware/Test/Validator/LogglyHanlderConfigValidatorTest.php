<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\LogglyHanlderConfigValidator;

class LogglyHanlderConfigValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'  => 'loggly',
            'level' => 'DEBUG',
            'token' => '123-123-123-123',
        ];

        $logglyValidator = new LogglyHanlderConfigValidator($configArray);
        $this->assertTrue($logglyValidator->validate());
    }

    public function testHasToken()
    {
        $configArray = [
            'type'  => 'loggly',
            'token' => '123-123-123-123',
        ];

        $logglyValidator = new LogglyHanlderConfigValidator($configArray);
        $this->assertTrue($logglyValidator->hasToken());
    }

    public function testNotHasToken()
    {
        $configArray = [
            'type' => 'loggly',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $logglyValidator = new LogglyHanlderConfigValidator($configArray);
        $this->assertTrue($logglyValidator->hasToken());
    }
}
