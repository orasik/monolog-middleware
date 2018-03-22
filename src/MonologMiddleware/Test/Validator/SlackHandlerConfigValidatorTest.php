<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\SlackHandlerConfigValidator;
use PHPUnit\Framework\TestCase;

class SlackHandlerConfigValidatorTest extends TestCase
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

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
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

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $slackValidator = new SlackHandlerConfigValidator($configArray);
        $slackValidator->hasToken();
    }
}
