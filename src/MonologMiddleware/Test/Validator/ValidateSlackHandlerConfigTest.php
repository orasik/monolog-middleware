<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\ValidateSlackHandlerConfig;

class ValidateSlackHandlerConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'    => 'slack',
            'level'   => 'DEBUG',
            'token'   => '123-123-123-123',
            'channel' => '#hello_world',
        ];

        $slackValidator = new ValidateSlackHandlerConfig($configArray);
        $this->assertTrue($slackValidator->validate());
    }

    public function testHasChannel()
    {
        $configArray = [
            'type'    => 'slack',
            'channel' => '#hello_world',
        ];

        $slackValidator = new ValidateSlackHandlerConfig($configArray);
        $this->assertTrue($slackValidator->hasChannel());
    }

    public function testNotHasChannel()
    {
        $configArray = [
            'type'  => 'slack',
            'level' => 'DEBUG',
            'token' => '123-123-123-123',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $slackValidator = new ValidateSlackHandlerConfig($configArray);
        $slackValidator->hasChannel();

    }

    public function testHasToken()
    {
        $configArray = [
            'type'  => 'slack',
            'token' => '123-123-123',
        ];

        $slackValidator = new ValidateSlackHandlerConfig($configArray);
        $this->assertTrue($slackValidator->hasToken());
    }

    public function testNotHasToken()
    {
        $configArray = [
            'type'  => 'slack',
            'level' => 'DEBUG',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $slackValidator = new ValidateSlackHandlerConfig($configArray);
        $slackValidator->hasToken();
    }
}
