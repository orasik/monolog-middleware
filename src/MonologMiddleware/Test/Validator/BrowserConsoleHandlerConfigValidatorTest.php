<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\BrowserConsoleHandlerConfigValidator;

class BrowserConsoleHandlerConfigValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'  => 'browser_console',
            'level' => 'INFO',
        ];

        $browserConsoleValidator = new BrowserConsoleHandlerConfigValidator($configArray);
        $this->assertTrue($browserConsoleValidator->validate());
    }
}
