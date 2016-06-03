<?php


namespace MonologMiddleware\Extension;

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\LogglyHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\SlackHandler;
use Monolog\Handler\StreamHandler;
use MonologMiddleware\Exception\MonologConfigException;
use MonologMiddleware\Exception\MonologHandlerNotImplementedException;
use MonologMiddleware\Validator;

/**
 * Class MonologConfigurationExtension
 * @package MonologMiddleware\Extension
 */
class MonologConfigurationExtension
{

    /**
     * @var array
     */
    protected $config;

    /**
     * MonologConfigurationExtension constructor.
     * This will get the configuration array from Service Container
     * @param array $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->validate();
    }

    public function validate()
    {
        return $this->hasHandlersConfig();
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasHandlersConfig()
    {
        if (isset($this->config['handlers'])) {
            return true;
        } else {
            throw new MonologConfigException("Can not find monolog handlers in your config. Make sure to have monolog configuration array in your config");
        }
    }

    /**
     * @return array
     * @throws MonologConfigException
     * @throws MonologHandlerNotImplementedException
     */
    public function getLogHandlers()
    {
        $handlers = [];
        foreach ($this->config['handlers'] as $key => $value) {
            $handlers[ $key ] = $this->getHandler($key, $value);
        }

        return $handlers;
    }

    /**
     * @param string $name
     * @param array $handlerConfig
     * @return SlackHandler|StreamHandler
     * @throws MonologConfigException
     * @throws MonologHandlerNotImplementedException
     */
    public function getHandler($name, $handlerConfig)
    {
        if (!isset($handlerConfig['type'])) {
            throw new MonologConfigException(sprintf("Hander %s does not have type", $name));
        }

        switch ($handlerConfig['type']) {
            case 'stream':
                /**
                 * @var Validator\ValidateStreamHandlerConfig $streamHandlerValidator
                 */
                $streamHandlerValidator = new Validator\ValidateStreamHandlerConfig($handlerConfig);
                $streamHandlerValidator->validate();
                $bubble = (isset($handlerConfig['bubble']) ? $handlerConfig['bubble'] : true);
                $filePermission = (isset($handlerConfig['permission']) ? $handlerConfig['permission'] : null);
                $useLocking = (isset($handlerConfig['use_locking']) ? $handlerConfig['use_locking'] : false);

                return new StreamHandler($handlerConfig['path'], $handlerConfig['level'], $bubble, $filePermission, $useLocking);
                break;

            case 'slack':
                /**
                 * @var Validator\ValidateSlackHandlerConfig $slackHandlerValidator
                 */
                $slackHandlerValidator = new Validator\ValidateSlackHandlerConfig($handlerConfig);
                $slackHandlerValidator->validate();

                $username = (isset($handlerConfig['username']) ? $handlerConfig['username'] : 'Monolog');
                $useAttachment = (isset($handlerConfig['use_attachment']) ? $handlerConfig['use_attachment'] : true);
                $iconEmoji = (isset($handlerConfig['icon_emoji']) ? $handlerConfig['icon_emoji'] : null);
                $bubble = (isset($handlerConfig['bubble']) ? $handlerConfig['bubble'] : true);
                $useShortAttachment = (isset($handlerConfig['useShortAttachment']) ? $handlerConfig['useShortAttachment'] : false);
                $includeContextAndExtra = (isset($handlerConfig['includeContextAndExtra']) ? $handlerConfig['includeContextAndExtra'] : false);

                return new SlackHandler($handlerConfig['token'], $handlerConfig['channel'], $username, $useAttachment, $iconEmoji, $handlerConfig['level'], $bubble, $useShortAttachment, $includeContextAndExtra);
                break;

            case 'loggly':
                /**
                 * @var Validator\ValidateLogglyHanlderConfig $logglyHandlerValidator
                 */
                $logglyHandlerValidator = new Validator\ValidateLogglyHanlderConfig($handlerConfig);
                $logglyHandlerValidator->validate();

                $bubble = (isset($handlerConfig['bubble']) ? $handlerConfig['bubble'] : true);

                return new LogglyHandler($handlerConfig['token'], $handlerConfig['level'], $bubble);
                break;

            case 'mandril':

            case 'mongo':
            case 'native_mailer':
                $nativeMailHandlerValidator = new Validator\ValidateNativeMailHandlerConfig($handlerConfig);
                $nativeMailHandlerValidator->validate();
                $bubble = (isset($handlerConfig['bubble']) ? $handlerConfig['bubble'] : true);
                $maxColumnWidth = (isset($handlerConfig['max_column_width']) ? $handlerConfig['max_column_width'] : 70);

                return new NativeMailerHandler($handlerConfig['to'], $handlerConfig['subject'], $handlerConfig['from'], $handlerConfig['level'], $bubble, $maxColumnWidth);
                break;

            case 'new_relic':
            case 'php_console':
            case 'pushover':
            case 'redis':
            case 'rotating_file':
            case 'swift_mailer':
            case 'sys_log':
            case 'zend_monitor':
            case 'hipchat':
            case 'iftt':
            case 'firephp':
            case 'dynamodb':
            case 'couchdb':
            case 'browser_console':
                $browserConsoleHandlerValidator = new Validator\ValidateBrowserConsoleHandlerConfig($handlerConfig);
                $browserConsoleHandlerValidator->validate();
                $bubble = (isset($handlerConfig['bubble']) ? $handlerConfig['bubble'] : true);

                return new BrowserConsoleHandler($handlerConfig['level'], $bubble);
                break;
            default:
                throw new MonologHandlerNotImplementedException(
                    sprintf("Handler %s does not exist or not implemented yet in the middleware", $handlerConfig['type'])
                );
        }
    }

}