<?php


namespace MonologMiddleware\Validator;

use MonologMiddleware\Exception\MonologConfigException;

/**
 * Class SlackHandlerConfigValidator
 * @package MonologMiddleware\Validator
 */
class SlackHandlerConfigValidator extends AbstractHandlerConfigValidator
{
    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate(): bool
    {
        if (parent::hasLevel() && $this->hasToken() && $this->hasChannel()) {
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

        throw new MonologConfigException("Missing token in Slack handler configuration");
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasChannel(): bool
    {
        if (isset($this->handlerConfigArray['channel'])) {
            return true;
        }

        throw new MonologConfigException("Missing channel in Slack handler configuration");
    }
}
