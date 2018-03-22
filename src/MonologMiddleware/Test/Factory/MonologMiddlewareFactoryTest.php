<?php
namespace MonologMiddleware\Test;

use Monolog\Logger;
use MonologMiddleware\MonologMiddleware;
use PHPUnit\Framework\TestCase;

class MonologMiddlewareFactoryTest extends TestCase
{
    protected $serviceContainer;

    public function setUp()
    {
        $monologArray = [
            'monolog' =>
                [
                    'name'     => 'MonologTest',
                    'handlers' => [
                        'main' => [
                            'type'  => 'stream',
                            'path'  => "test/test.log",
                            'level' => Logger::DEBUG,
                        ],
                    ],
                ],
        ];

    }

    public function testInvoke()
    {

    }


}
