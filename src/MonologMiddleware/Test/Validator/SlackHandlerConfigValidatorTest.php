<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\SlackHandlerConfigValidator;

class SlackHandlerConfigValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'    => 'slack',
            'level'   => 'DEBUG',
            'token'   => '123-123-123-123',
            'channel' => '#hello_world',
        ];

        $slackValidator = new SlackHandlerConfigValidator($configArray);
        $this->assertTrue($slackValidator->validate());
    }

    public function testHasChannel()
    {
        $configArray = [
            'type'    => 'slack',
            'channel' => '#hello_world',
        ];

        $slackValidator = new SlackHandlerConfigValidator($configArray);
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
        $slackValidator = new SlackHandlerConfigValidator($configArray);
        $slackValidator->hasChannel();

    }

    public function testHasToken()
    {
        $configArray = [
            'type'  => 'slack',
            'token' => '123-123-123',
        ];

        $slackValidator = new SlackHandlerConfigValidator($configArray);
        $this->assertTrue($slackValidator->hasToken());
    }

    public function testNotHasToken()
    {
        $configArray = [
            'type'  => 'slack',
            'level' => 'DEBUG',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $slackValidator = new SlackHandlerConfigValidator($configArray);
        $slackValidator->hasToken();
    }
}
