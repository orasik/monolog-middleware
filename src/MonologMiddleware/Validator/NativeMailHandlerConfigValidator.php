<?php


namespace MonologMiddleware\Validator;

use MonologMiddleware\Exception\MonologConfigException;
/**
 * Class NativeMailHandlerConfigValidator
 * @package MonologMiddleware\Validator
 */
class NativeMailHandlerConfigValidator extends AbstractHandlerConfigValidator
{
    /**
     * @return bool
     * @throws MonologConfigException
     * @throws \MonologMiddleware\Exception\MonologConfigException
     */
    public function validate(): bool
    {
        if (parent::hasLevel() && $this->hasTo() && $this->hasSubject() && $this->hasFrom()) {
            return true;
        }

        throw new MonologConfigException("Missing data in handler configuration");
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasTo(): bool
    {
        if (isset($this->handlerConfigArray['to_email'])) {
            return true;
        }

        throw new MonologConfigException("Monolog To email is missing from config");
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasSubject(): bool
    {
        if (isset($this->handlerConfigArray['subject'])) {
            return true;
        }

        throw new MonologConfigException("Monolog email subject is missing from config");
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasFrom(): bool
    {
        if (isset($this->handlerConfigArray['from_email'])) {
            return true;
        }

        throw new MonologConfigException("Monolog email from is missing from config");
    }
}
