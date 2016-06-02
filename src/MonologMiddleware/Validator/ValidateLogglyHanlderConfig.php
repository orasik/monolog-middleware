<?php

namespace MonologMiddleware\Validator;

use MonologMiddleware\Exception\MonologConfigException;

/**
 * Class ValidateLogglyHanlderConfig
 * @package MonologMiddleware\Validator
 */
class ValidateLogglyHanlderConfig extends AbstractValidateHandlerConfig
{

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate()
    {
        if (parent::hasLevel() && $this->hasToken()) {
            return true;
        }
    }


    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasToken()
    {
        if (isset($this->handlerConfigArray['token'])) {
            return true;
        } else {
            throw new MonologConfigException("Missing token in Loggly config");
        }
    }
}