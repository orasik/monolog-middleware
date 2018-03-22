<?php


namespace MonologMiddleware\Validator;


use MonologMiddleware\Exception\MonologConfigException;

/**
 * Class StreamHandlerConfigValidator
 * @package MonologMiddleware\Validator
 */
class StreamHandlerConfigValidator extends AbstractHandlerConfigValidator
{

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate(): bool
    {
        if (parent::hasLevel() && $this->hasPath()) {
            return true;
        }
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasPath(): bool
    {
        if (isset($this->handlerConfigArray['path'])) {
            return true;
        }

        throw new MonologConfigException("Missing Path in Stream handler configuration");
    }
}
