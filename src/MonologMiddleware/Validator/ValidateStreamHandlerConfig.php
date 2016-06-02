<?php


namespace MonologMiddleware\Validator;


use MonologMiddleware\Exception\MonologConfigException;

/**
 * Class ValidateStreamHandlerConfig
 * @package MonologMiddleware\Validator
 */
class ValidateStreamHandlerConfig extends AbstractValidateHandlerConfig
{
    /**
     * @var string
     */
    protected $path;


    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate()
    {
        if (parent::hasLevel() && $this->hasPath()) {
            return true;
        }
    }


    /**
     * @return bool
     */
    public function hasPath()
    {
        if (isset($this->handlerConfigArray['path'])) {
            return true;
        } else {
            throw new MonologConfigException("Missing Path in Stream handler configuration");
        }
    }


}