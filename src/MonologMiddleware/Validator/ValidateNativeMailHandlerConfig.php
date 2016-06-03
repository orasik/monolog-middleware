<?php


namespace MonologMiddleware\Validator;


/**
 * Class ValidateNativeMailHandlerConfig
 * @package MonologMiddleware\Validator
 */
class ValidateNativeMailHandlerConfig extends AbstractValidateHandlerConfig
{
    /**
     * @return bool
     * @throws MonologConfigException
     * @throws \MonologMiddleware\Exception\MonologConfigException
     */
    public function validate()
    {
        if (parent::hasLevel() && $this->hasTo() && $this->hasSubject() && $this->hasFrom()) {
            return true;
        } else {
            throw new MonologConfigException("Missing data in handler configuration");
        }
    }


    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasTo()
    {
        if (isset($this->handlerConfigArray['to'])) {
            return true;
        } else {
            throw new MonologConfigException("Monolog To email is missing from config");
        }
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasSubject()
    {
        if (isset($this->handlerConfigArray['subject'])) {
            return true;
        } else {
            throw new MonologConfigException("Monolog email subject is missing from config");
        }
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasFrom()
    {
        if (isset($this->handlerConfigArray['from'])) {
            return true;
        } else {
            throw new MonologConfigException("Monolog email from is missing from config");
        }
    }
}