<?php

namespace MonologMiddleware\Validator;

use MonologMiddleware\Exception\MonologConfigException;

/**
 * Class LogglyHanlderConfigValidator
 * @package MonologMiddleware\Validator
 */
class LogglyHanlderConfigValidator extends AbstractHandlerConfigValidator
{

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate(): bool
    {
        if (parent::hasLevel() && $this->hasToken()) {
            return true;
        }
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasToken(): bool
    {
        if (isset($this->handlerConfigArray['token'])) {
            return true;
        }

        throw new MonologConfigException("Missing token in Loggly config");
    }
}
