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
     * @throws MonologConfigException
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->validate();
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function validate(): bool
    {
        return $this->hasHandlersConfig();
    }

    /**
     * @return bool
     * @throws MonologConfigException
     */
    public function hasHandlersConfig(): bool
    {
        if (isset($this->config['handlers'])) {
            return true;
        }

        throw new MonologConfigException("Can not find monolog handlers in your config. Make sure to have monolog configuration array in your config");
    }

    /**
     * @return array
     * @throws MonologConfigException
     * @throws MonologHandlerNotImplementedException
     * @throws \Exception
     * @throws \Monolog\Handler\MissingExtensionException
     */
    public function getLogHandlers(): array
    {
        $handlers = [];
        foreach ($this->config['handlers'] as $key => $value) {
            $handlers[ $key ] = $this->getHandler($key, $value);
        }

        return $handlers;
    }

    /**
     * @param string $name
     * @param array  $handlerConfig
     * @return BrowserConsoleHandler|ChromePHPHandler|FirePHPHandler|LogglyHandler|NativeMailerHandler|NewRelicHandler|PushoverHandler|RedisHandler|SlackHandler|StreamHandler
     * @throws \LogicException
     * @throws \InvalidArgumentException
     * @throws MonologConfigException
     * @throws MonologHandlerNotImplementedException
     * @throws \Exception
     * @throws \Monolog\Handler\MissingExtensionException
     */
    public function getHandler($name, $handlerConfig)
    {
        if (!isset($handlerConfig['type'])) {
            throw new MonologConfigException(sprintf("Hander %s does not have type", $name));
        }

        $bubble = ($handlerConfig['bubble'] ?? true);

        switch ($handlerConfig['type']) {
            case 'stream':
                /**
                 * @var Validator\StreamHandlerConfigValidator $streamHandlerValidator
                 */
                $streamHandlerValidator = new Validator\StreamHandlerConfigValidator($handlerConfig);
                $streamHandlerValidator->validate();
                $filePermission = ($handlerConfig['permission'] ?? null);
                $useLocking = ($handlerConfig['use_locking'] ?? false);

                return new StreamHandler($handlerConfig['path'], $handlerConfig['level'], $bubble, $filePermission, $useLocking);
                break;

            case 'slack':
                /**
                 * @var Validator\SlackHandlerConfigValidator $slackHandlerValidator
                 */
                $slackHandlerValidator = new Validator\SlackHandlerConfigValidator($handlerConfig);
                $slackHandlerValidator->validate();

                $username = ($handlerConfig['username'] ?? 'Monolog');
                $useAttachment = ($handlerConfig['use_attachment'] ?? true);
                $iconEmoji = ($handlerConfig['icon_emoji'] ?? null);
                $useShortAttachment = ($handlerConfig['useShortAttachment'] ?? false);
                $includeContextAndExtra = ($handlerConfig['includeContextAndExtra'] ?? false);

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
                $maxColumnWidth = ($handlerConfig['max_column_width'] ?? 70);

                return new NativeMailerHandler($handlerConfig['to'], $handlerConfig['subject'], $handlerConfig['from'], $handlerConfig['level'], $bubble, $maxColumnWidth);
                break;

            case 'new_relic':
                $newRelicHandlerValidator = new Validator\AbstractHandlerConfigValidator($handlerConfig);
                $newRelicHandlerValidator->validate();

                $appName = ($handlerConfig['app_name'] ?? null);

                return new NewRelicHandler($handlerConfig['level'], $bubble, $appName);
                break;
            case 'php_console':
                break;
            case 'pushover':
                $pushoverHandlerValidator = new Validator\PushoverHandlerConfigValidator($handlerConfig);
                $pushoverHandlerValidator->validate();
                $title = ($handlerConfig['title'] ?? null);

                return new PushoverHandler($handlerConfig['token'], $handlerConfig['user'], $title, $handlerConfig['level'], $bubble);
                break;
            case 'redis':
                $redisHandlerValidator = new Validator\RedisHandlerConfigValidator($handlerConfig);
                $redisHandlerValidator->validate();
                $capSize = ($handlerConfig['cap_size'] ?? false);

                return new RedisHandler($handlerConfig['redis_client'], $handlerConfig['key'], $bubble, $capSize);
                break;
            case 'rotating_file':

                $rotatingFileHanlderValidator = new Validator\StreamHandlerConfigValidator($handlerConfig);
                $rotatingFileHanlderValidator->validate();
                $maxFiles = ($handlerConfig['max_files'] ?? 0);
                $filePermission = ($handlerConfig['file_permission'] ?? null);
                $filenameFormat = ($handlerConfig['filename_format'] ?? '{filename}-{date}');
                $dateFormat = ($handlerConfig['date_format'] ?? 'Y-m-d');

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
