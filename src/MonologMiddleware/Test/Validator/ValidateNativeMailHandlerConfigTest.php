<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\ValidateNativeMailHandlerConfig;

class ValidateNativeMailHandlerConfigTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'    => 'native_mailer',
            'level'   => 'ERROR',
            'to'      => 'someemail@somedomain.com',
            'subject' => 'Error in your application',
            'from'    => 'monolog@yoursystem.com',
        ];

        $nativeMailValidator = new ValidateNativeMailHandlerConfig($configArray);
        $this->assertTrue($nativeMailValidator->validate());
    }

    public function testHasTo()
    {
        $configArray = [
            'type'  => 'native_mailer',
            'level' => 'ERROR',
            'to'    => 'someemail@somedomain.com',
        ];

        $nativeMailValidator = new ValidateNativeMailHandlerConfig($configArray);
        $this->assertTrue($nativeMailValidator->hasTo());
    }

    public function testHasFrom()
    {
        $configArray = [
            'type'  => 'native_mailer',
            'level' => 'ERROR',
            'from'  => 'someemail@somedomain.com',
        ];

        $nativeMailValidator = new ValidateNativeMailHandlerConfig($configArray);
        $this->assertTrue($nativeMailValidator->hasFrom());
    }

    public function testHasSubject()
    {
        $configArray = [
            'type'    => 'native_mailer',
            'level'   => 'ERROR',
            'subject' => 'someemail@somedomain.com',
        ];

        $nativeMailValidator = new ValidateNativeMailHandlerConfig($configArray);
        $this->assertTrue($nativeMailValidator->hasSubject());
    }

    public function testHasToAndFromButNotSubject()
    {
        $configArray = [
            'type'  => 'native_mailer',
            'level' => 'ERROR',
            'to'    => 'someemail@somedomain.com',
            'from'  => 'monolog@yoursystem.com',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $nativeMailValidator = new ValidateNativeMailHandlerConfig($configArray);
        $this->assertTrue($nativeMailValidator->hasTo());
        $this->assertTrue($nativeMailValidator->hasFrom());
        $this->assertTrue($nativeMailValidator->hasSubject());
    }
}
