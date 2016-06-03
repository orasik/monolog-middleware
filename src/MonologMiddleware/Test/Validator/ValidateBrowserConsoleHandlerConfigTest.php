<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\ValidateBrowserConsoleHandlerConfig;

class ValidateBrowserConsoleHandlerConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'  => 'browser_console',
            'level' => 'INFO',
        ];

        $browserConsoleValidator = new ValidateBrowserConsoleHandlerConfig($configArray);
        $this->assertTrue($browserConsoleValidator->validate());
    }
}
