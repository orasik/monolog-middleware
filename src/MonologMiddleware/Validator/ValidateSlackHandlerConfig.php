<?php


namespace MonologMiddleware\Validator;

use MonologMiddleware\Exception\MonologConfigException;

/**
 * Class ValidateSlackHandlerConfig
 * @package MonologMiddleware\Validator
 */
class ValidateSlackHandlerConfig extends AbstractValidateHandlerConfig
{
    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $channel;

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate()
    {
        if (parent::hasLevel() && $this->hasToken() && $this->hasChannel()) {
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
            throw new MonologConfigException("Missing token in Slack handler configuration");
        }
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasChannel()
    {
        if (isset($this->handlerConfigArray['channel'])) {
            return true;
        } else {
            throw new MonologConfigException("Missing channel in Slack handler configuration");
        }
    }

}