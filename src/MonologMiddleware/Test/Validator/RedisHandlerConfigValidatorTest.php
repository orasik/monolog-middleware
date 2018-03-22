<?php


namespace MonologMiddleware\Test;

use MonologMiddleware\Validator\RedisHandlerConfigValidator;
use PHPUnit\Framework\TestCase;

class RedisHandlerConfigValidatorTest extends TestCase
{

    public function testValidate()
    {
        $configArray = [
            'type'         => 'redis',
            'level'        => 'INFO',
            'redis_client' => new \Redis(),
            'key'          => 'monolog'
        ];

        $redisValidator = new RedisHandlerConfigValidator($configArray);
        $this->assertTrue($redisValidator->validate());
    }

    public function testHasRedisClient()
    {
        $configArray = [
            'type'         => 'redis',
            'level'        => 'INFO',
            'redis_client' => new \Redis(),
        ];

        $redisValidator = new RedisHandlerConfigValidator($configArray);
        $this->assertTrue($redisValidator->hasRedisClient());
    }

    public function testNotHasRedisClient()
    {
        $configArray = [
            'type'  => 'redis',
            'level' => 'INFO',
            'key'   => 'monolog'
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $redisValidator = new RedisHandlerConfigValidator($configArray);
        $redisValidator->hasRedisClient();
    }

    public function testHasRedisValueButNotRedisClient()
    {
        $configArray = [
            'type'         => 'redis',
            'level'        => 'INFO',
            'redis_client' => 'REDIS',
        ];
        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $redisValidator = new RedisHandlerConfigValidator($configArray);
        $redisValidator->hasRedisClient();
    }

    public function testHasKey()
    {
        $configArray = [
            'type'  => 'redis',
            'level' => 'INFO',
            'key'   => 'monolog'
        ];

        $redisValidator = new RedisHandlerConfigValidator($configArray);
        $this->assertTrue($redisValidator->hasKey());
    }

    public function testHasNoKey()
    {
        $configArray = [
            'type'  => 'redis',
            'level' => 'INFO',
        ];

        $this->expectException('MonologMiddleware\Exception\MonologConfigException');
        $redisValidator = new RedisHandlerConfigValidator($configArray);
        $this->assertTrue($redisValidator->hasKey());
    }
}
