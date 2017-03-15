<?php


namespace MonologMiddleware\Extension;

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\LogglyHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\NewRelicHandler;
use Monolog\Handler\PushoverHandler;
use Monolog\Handler\RedisHandler;
use Monolog\Handler\RotatingFileHandler;
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

        $bubble = (isset($handlerConfig['bubble']) ? $handlerConfig['bubble'] : true);

        switch ($handlerConfig['type']) {
            case 'stream':
                /**
                 * @var Validator\StreamHandlerConfigValidator $streamHandlerValidator
                 */
                $streamHandlerValidator = new Validator\StreamHandlerConfigValidator($handlerConfig);
                $streamHandlerValidator->validate();
                $filePermission = (isset($handlerConfig['permission']) ? $handlerConfig['permission'] : null);
                $useLocking = (isset($handlerConfig['use_locking']) ? $handlerConfig['use_locking'] : false);

                return new StreamHandler($handlerConfig['path'], $handlerConfig['level'], $bubble, $filePermission, $useLocking);
                break;

            case 'slack':
                /**
                 * @var Validator\SlackHandlerConfigValidator $slackHandlerValidator
                 */
                $slackHandlerValidator = new Validator\SlackHandlerConfigValidator($handlerConfig);
                $slackHandlerValidator->validate();

                $username = (isset($handlerConfig['username']) ? $handlerConfig['username'] : 'Monolog');
                $useAttachment = (isset($handlerConfig['use_attachment']) ? $handlerConfig['use_attachment'] : true);
                $iconEmoji = (isset($handlerConfig['icon_emoji']) ? $handlerConfig['icon_emoji'] : null);
                $useShortAttachment = (isset($handlerConfig['useShortAttachment']) ? $handlerConfig['useShortAttachment'] : false);
                $includeContextAndExtra = (isset($handlerConfig['includeContextAndExtra']) ? $handlerConfig['includeContextAndExtra'] : false);

                return new SlackHandler($handlerConfig['token'], $handlerConfig['channel'], $username, $useAttachment, $iconEmoji, $handlerConfig['level'], $bubble, $useShortAttachment, $includeContextAndExtra);
                break;

            case 'loggly':
                /**
                 * @var Validator\LogglyHanlderConfigValidator $logglyHandlerValidator
                 */
                $logglyHandlerValidator = new Validator\LogglyHanlderConfigValidator($handlerConfig);
                $logglyHandlerValidator->validate();

                return new LogglyHandler($handlerConfig['token'], $handlerConfig['level'], $bubble);
                break;

            case 'native_mailer':
                $nativeMailHandlerValidator = new Validator\NativeMailHandlerConfigValidator($handlerConfig);
                $nativeMailHandlerValidator->validate();
                $maxColumnWidth = (isset($handlerConfig['max_column_width']) ? $handlerConfig['max_column_width'] : 70);

                return new NativeMailerHandler($handlerConfig['to'], $handlerConfig['subject'], $handlerConfig['from'], $handlerConfig['level'], $bubble, $maxColumnWidth);
                break;

            case 'new_relic':
                $newRelicHandlerValidator = new Validator\AbstractHandlerConfigValidator($handlerConfig);
                $newRelicHandlerValidator->validate();

                $appName = (isset($handlerConfig['app_name']) ? $handlerConfig['app_name'] : null);

                return new NewRelicHandler($handlerConfig['level'], $bubble, $appName);
                break;
            case 'php_console':
                break;
            case 'pushover':
                $pushoverHandlerValidator = new Validator\PushoverHandlerConfigValidator($handlerConfig);
                $pushoverHandlerValidator->validate();
                $title = (isset($handlerConfig['title']) ? $handlerConfig['title'] : null);

                return new PushoverHandler($handlerConfig['token'], $handlerConfig['user'], $title, $handlerConfig['level'], $bubble);
                break;
            case 'redis':
                $redisHandlerValidator = new Validator\RedisHandlerConfigValidator($handlerConfig);
                $redisHandlerValidator->validate();
                $capSize = (isset($handlerConfig['cap_size']) ? $handlerConfig['cap_size'] : false);

                return new RedisHandler($handlerConfig['redis_client'], $handlerConfig['key'], $bubble, $capSize);
                break;
            case 'rotating_file':

                $rotatingFileHanlderValidator = new Validator\StreamHandlerConfigValidator($handlerConfig);
                $rotatingFileHanlderValidator->validate();
                $maxFiles = (isset($handlerConfig['max_files']) ? $handlerConfig['max_files'] : 0);
                $filePermission = (isset($handlerConfig['file_permission']) ? $handlerConfig['file_permission'] : null);
                $filenameFormat = (isset($handlerConfig['filename_format']) ? $handlerConfig['filename_format'] : '{filename}-{date}');
                $dateFormat = (isset($handlerConfig['date_format']) ? $handlerConfig['date_format'] : 'Y-m-d');

                $rotatingFileHandler = new RotatingFileHandler($handlerConfig['filename'], $maxFiles, $handlerConfig['level'], $bubble, $filePermission, false);
                $rotatingFileHandler->setFilenameFormat($filenameFormat, $dateFormat);

                return $rotatingFileHandler;
                break;
            case 'firephp':
                $firePhpHandlerValidator = new Validator\AbstractHandlerConfigValidator($handlerConfig);
                $firePhpHandlerValidator->validate();

                return new FirePHPHandler($handlerConfig['level'], $bubble);
                break;
            case 'chromephp':
                $chromePhpHandlerValidator = new Validator\AbstractHandlerConfigValidator($handlerConfig);
                $chromePhpHandlerValidator->validate();

                return new ChromePHPHandler($handlerConfig['level'], $bubble);
                break;
            case 'browser_console':
                $browserConsoleHandlerValidator = new Validator\BrowserConsoleHandlerConfigValidator($handlerConfig);
                $browserConsoleHandlerValidator->validate();

                return new BrowserConsoleHandler($handlerConfig['level'], $bubble);
                break;
            case 'dynamodb':
            case 'couchdb':
            case 'swift_mailer':
            case 'sys_log':
            case 'zend_monitor':
            case 'hipchat':
            case 'iftt':
            case 'mongo':
                break;
            default:
                throw new MonologHandlerNotImplementedException(
                    sprintf("Handler %s does not exist or not implemented yet in the middleware", $handlerConfig['type'])
                );
        }
    }

}