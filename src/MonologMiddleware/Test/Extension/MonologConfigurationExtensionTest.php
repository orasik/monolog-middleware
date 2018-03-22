<?php
namespace MonologMiddleware\Test;

use Monolog\Handler\LogglyHandler;
use Monolog\Handler\SlackHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use MonologMiddleware\Extension\MonologConfigurationExtension;
use PHPUnit\Framework\TestCase;

class MonologConfigurationExtensionTest extends TestCase
{
    public function testConstructorWithEmptyArrayShouldThrowMonologConfigException()
    {
        $config = [];

        $this->expectException('\MonologMiddleware\Exception\MonologConfigException');
        $configExtention = new MonologConfigurationExtension($config);
    }

    public function testHasHandlerConfig()
    {

        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'main' => [
                    'type'  => 'stream',
                    'path'  => "test/test.log",
                    'level' => Logger::DEBUG,
                ],
            ],
        ];

        $configExtension = new MonologConfigurationExtension($config);
        $this->assertTrue($configExtension->hasHandlersConfig());
    }

    public function testNotHasHandlerConfig()
    {
        $config = [
            'name'      => 'MonologTest',
            'handlers1' => [
                'main' => [
                    'type'  => 'stream',
                    'path'  => "test/test.log",
                    'level' => Logger::DEBUG,
                ],
            ],
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');

        $configExtension = new MonologConfigurationExtension($config);
    }

    public function testGetHandlerWithStreamType()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'main' => [
                    'type'  => 'stream',
                    'path'  => "test/test.log",
                    'level' => Logger::DEBUG,
                ],
            ],
        ];

        $configHelper = new MonologConfigurationExtension($config);

        $handler = $configHelper->getHandler('main', $config['handlers']['main']);

        $this->assertInstanceOf(StreamHandler::class, $handler);
    }

    public function testGetHandlerWithLogglyType()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'loggly' => [
                    'type'  => 'loggly',
                    'token' => "123456789",
                    'level' => Logger::CRITICAL,
                ],
            ],
        ];

        $conigHelper = new MonologConfigurationExtension($config);

        $handler = $conigHelper->getHandler('loggly', $config['handlers']['loggly']);

        $this->assertInstanceOf(LogglyHandler::class, $handler);
    }

    public function testGetHandlerWithLogglyTypeAndMissingToken()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'loggly' => [
                    'type'  => 'loggly',
                    'level' => Logger::CRITICAL,
                ],
            ],
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');;
        $conigHelper = new MonologConfigurationExtension($config);
        $handler = $conigHelper->getHandler('loggly', $config['handlers']['loggly']);
    }

    public function testGetHandlerWithLogglyTypeAndMissingTokenAndLevel()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'loggly' => [
                    'type' => 'loggly',
                ],
            ],
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');;
        $conigHelper = new MonologConfigurationExtension($config);
        $handler = $conigHelper->getHandler('loggly', $config['handlers']['loggly']);
    }

    public function testGetHandlerWithSlackType()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'slack' => [
                    'type'    => 'slack',
                    'token'   => "123456789",
                    'channel' => 'mychannel',
                    'level'   => Logger::CRITICAL,
                ],
            ],
        ];

        $conigHelper = new MonologConfigurationExtension($config);

        $handler = $conigHelper->getHandler('slack', $config['handlers']['slack']);

        $this->assertInstanceOf(SlackHandler::class, $handler);
    }

    public function testSlackTypeWithMissingChannel()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'slack' => [
                    'type'  => 'slack',
                    'token' => "123456789",
                    'level' => Logger::CRITICAL,
                ],
            ],
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $conigHelper = new MonologConfigurationExtension($config);

        $handler = $conigHelper->getHandler('slack', $config['handlers']['slack']);
    }

    public function testSlackTypeWithMissingToken()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'slack' => [
                    'type'    => 'slack',
                    'channel' => "mychannel",
                    'level'   => Logger::CRITICAL,
                ],
            ],
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $conigHelper = new MonologConfigurationExtension($config);

        $handler = $conigHelper->getHandler('slack', $config['handlers']['slack']);
    }

    public function testSlackTypeWithMissingTokenAndChannel()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'slack' => [
                    'type'  => 'slack',
                    'level' => Logger::CRITICAL,
                ],
            ],
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $conigHelper = new MonologConfigurationExtension($config);

        $handler = $conigHelper->getHandler('slack', $config['handlers']['slack']);
    }

    public function testSlackTypeWithMissingTokenAndChannelAndLevel()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'slack' => [
                    'type' => 'slack',
                ],
            ],
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $conigHelper = new MonologConfigurationExtension($config);

        $handler = $conigHelper->getHandler('slack', $config['handlers']['slack']);
    }

    public function testErrorGetHandler()
    {
        $config = [
            'name'     => 'MonologTest',
            'handlers' => [
                'rubbishHandler' => [
                    'type'  => 'rubbish',
                    'token' => "123456789",
                    'level' => Logger::CRITICAL,
                ],
            ],
        ];

        $configExtension = new MonologConfigurationExtension($config);

        $this->expectException('MonologMiddleware\Exception\MonologHandlerNotImplementedException');

        $configExtension->getHandler('rubbish', $config['handlers']['rubbishHandler']);
    }

    public function testGetHandlers()
    {
        $config = [
            'logger_name' => 'LoggerTest',
            'handlers'    =>
                [
                    'slack' =>
                        [
                            'type'      => 'slack',
                            'token'     => 'token-token',
                            'channel'   => '#test-channel',
                            'level'     => Logger::DEBUG,
                            'iconEmoji' => '::ghost::', // optional
                            'bubble'    => false, // optional
                        ],
                    'main'  =>
                        [
                            'type'   => 'stream',
                            'path'   => "data/main.log",
                            'level'  => Logger::DEBUG,
                            'bubble' => false,
                        ],
                ],
        ];

        $configExtension = new MonologConfigurationExtension($config);
        $handlers = $configExtension->getLogHandlers();
        $this->assertArrayHasKey('slack', $handlers);
        $this->assertArrayHasKey('main', $handlers);
        $this->assertInstanceOf(SlackHandler::class, $handlers['slack']);
        $this->assertInstanceOf(StreamHandler::class, $handlers['main']);
    }

    public function testEmptyGetHandlers()
    {
        // This should return empty array
    }
}
