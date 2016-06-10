<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\NativeMailHandlerConfigValidator;

class NativeMailHandlerConfigValidatorTest extends \PHPUnit_Framework_TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'       => 'native_mailer',
            'level'      => 'ERROR',
            'to_email'   => 'someemail@somedomain.com',
            'subject'    => 'Error in your application',
            'from_email' => 'monolog@yoursystem.com',
        ];

        $nativeMailValidator = new NativeMailHandlerConfigValidator($configArray);
        $this->assertTrue($nativeMailValidator->validate());
    }

    public function testHasTo()
    {
        $configArray = [
            'type'     => 'native_mailer',
            'level'    => 'ERROR',
            'to_email' => 'someemail@somedomain.com',
        ];

        $nativeMailValidator = new NativeMailHandlerConfigValidator($configArray);
        $this->assertTrue($nativeMailValidator->hasTo());
    }

    public function testHasFrom()
    {
        $configArray = [
            'type'       => 'native_mailer',
            'level'      => 'ERROR',
            'from_email' => 'someemail@somedomain.com',
        ];

        $nativeMailValidator = new NativeMailHandlerConfigValidator($configArray);
        $this->assertTrue($nativeMailValidator->hasFrom());
    }

    public function testHasSubject()
    {
        $configArray = [
            'type'    => 'native_mailer',
            'level'   => 'ERROR',
            'subject' => 'someemail@somedomain.com',
        ];

        $nativeMailValidator = new NativeMailHandlerConfigValidator($configArray);
        $this->assertTrue($nativeMailValidator->hasSubject());
    }

    public function testHasToAndFromButNotSubject()
    {
        $configArray = [
            'type'       => 'native_mailer',
            'level'      => 'ERROR',
            'to_email'   => 'someemail@somedomain.com',
            'from_email' => 'monolog@yoursystem.com',
        ];

        self::setExpectedException('MonologMiddleware\Exception\MonologConfigException');
        $nativeMailValidator = new NativeMailHandlerConfigValidator($configArray);
        $this->assertTrue($nativeMailValidator->hasTo());
        $this->assertTrue($nativeMailValidator->hasFrom());
        $this->assertTrue($nativeMailValidator->hasSubject());
    }
}
