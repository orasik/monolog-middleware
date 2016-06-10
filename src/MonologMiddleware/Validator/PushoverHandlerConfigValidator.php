<?php


namespace MonologMiddleware\Validator;


use MonologMiddleware\Exception\MonologConfigException;


/**
 * Class PushoverHandlerConfigValidator
 * @package MonologMiddleware\Validator
 */
class PushoverHandlerConfigValidator extends AbstractHandlerConfigValidator
{

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate()
    {
        if (parent::hasLevel() && $this->hasToken() && $this->hasUser()) {
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
            throw new MonologConfigException("Missing token in Pushover handler configuration");
        }
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasUser()
    {
        if (isset($this->handlerConfigArray['user'])) {
            return true;
        } else {
            throw new MonologConfigException("Missing user in Pushover handler configuration");
        }
    }
}