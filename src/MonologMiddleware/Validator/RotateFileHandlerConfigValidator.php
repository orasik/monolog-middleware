<?php


namespace MonologMiddleware\Validator;


use MonologMiddleware\Exception\MonologConfigException;


/**
 * Class RotateFileHandlerConfigValidator
 * @package MonologMiddleware\Validator
 */
class RotateFileHandlerConfigValidator extends AbstractHandlerConfigValidator
{

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate(): bool
    {
        if (parent::hasLevel() && $this->hasFilename()) {
            return true;
        }
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasFilename(): bool
    {
        if (isset($this->handlerConfigArray['filename'])) {
            return true;
        }

        throw new MonologConfigException("Missing filename in Rotate File handler configuration");
    }
}
