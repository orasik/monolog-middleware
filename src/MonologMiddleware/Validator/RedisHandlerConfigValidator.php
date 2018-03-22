<?php

namespace MonologMiddleware\Validator;

use MonologMiddleware\Exception\MonologConfigException;

/**
 * Class RedisHandlerConfigValidator
 * @package MonologMiddleware\Validator
 */
class RedisHandlerConfigValidator extends AbstractHandlerConfigValidator
{
    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate(): bool
    {
        if (parent::hasLevel() && $this->hasRedisClient() && $this->hasKey()) {
            return true;
        }
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasRedisClient(): bool
    {
        if (isset($this->handlerConfigArray['redis_client']) && $this->handlerConfigArray['redis_client'] instanceof \Redis) {
            return true;
        }

        throw new MonologConfigException("Missing Redis client in Redis handler configuration");
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasKey(): bool
    {
        if (isset($this->handlerConfigArray['key'])) {
            return true;
        }

        throw new MonologConfigException("Missing Redis key in Redis handler configuration");
    }
}
