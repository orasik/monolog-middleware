<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\PushoverHandlerConfigValidator;
use PHPUnit\Framework\TestCase;

class PushoverHandlerConfigValidatorTest extends TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'  => 'pushover',
            'level' => 'DEBUG',
            'token' => '123-123-123-123',
            'user'  => 'username',
        ];

        $pushoverValidator = new PushoverHandlerConfigValidator($configArray);
        $this->assertTrue($pushoverValidator->validate());
    }

    public function testHasToken()
    {
        $configArray = [
            'type'  => 'pushover',
            'token' => 'token-token-token',
        ];

        $pushoverValidator = new PushoverHandlerConfigValidator($configArray);
        $this->assertTrue($pushoverValidator->hasToken());
    }

    public function testNotHasToken()
    {
        $configArray = [
            'type'  => 'pushover',
            'level' => 'DEBUG',
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $pushoverValidator = new PushoverHandlerConfigValidator($configArray);
        $pushoverValidator->hasToken();

    }

    public function testHasUser()
    {
        $configArray = [
            'type' => 'pushover',
            'user' => '123-123-123',
        ];

        $pushoverValidator = new PushoverHandlerConfigValidator($configArray);
        $this->assertTrue($pushoverValidator->hasUser());
    }

    public function testNotHasUser()
    {
        $configArray = [
            'type'  => 'pushover',
            'level' => 'DEBUG',
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $pushoverValidator = new PushoverHandlerConfigValidator($configArray);
        $pushoverValidator->hasUser();
    }
}
