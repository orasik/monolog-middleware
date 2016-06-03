<?php


namespace MonologMiddleware\Extension;

use Monolog\Handler\BrowserConsoleHandler;
use Monolog\Handler\LogglyHandler;
use Monolog\Handler\NativeMailerHandler;
use Monolog\Handler\PushoverHandler;
use Monolog\Handler\RedisHandler;
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

        /*
         * From Symfony monolog bundle, I use it as todo list
         *
         * - console:
         *   - [verbosity_levels]: level => verbosity configuration
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - firephp:
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - gelf:
         *   - publisher: {id: ...} or {hostname: ..., port: ..., chunk_size: ...}
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - chromephp:
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - rotating_file:
         *   - path: string
         *   - [max_files]: files to keep, defaults to zero (infinite)
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *   - [file_permission]: string|null, defaults to null
         *   - [filename_format]: string, defaults to '{filename}-{date}'
         *   - [date_format]: string, defaults to 'Y-m-d'
         *
         * - mongo:
         *   - mongo:
         *      - id: optional if host is given
         *      - host: database host name, optional if id is given
         *      - [port]: defaults to 27017
         *      - [user]: database user name
         *      - pass: mandatory only if user is present
         *      - [database]: defaults to monolog
         *      - [collection]: defaults to logs
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - elasticsearch:
         *   - elasticsearch:
         *      - id: optional if host is given
         *      - host: elastic search host name
         *      - [port]: defaults to 9200
         *   - [index]: index name, defaults to monolog
         *   - [document_type]: document_type, defaults to logs
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - fingers_crossed:
         *   - handler: the wrapped handler's name
         *   - [action_level|activation_strategy]: minimum level or service id to activate the handler, defaults to WARNING
         *   - [excluded_404s]: if set, the strategy will be changed to one that excludes 404s coming from URLs matching any of those patterns
         *   - [buffer_size]: defaults to 0 (unlimited)
         *   - [stop_buffering]: bool to disable buffering once the handler has been activated, defaults to true
         *   - [passthru_level]: level name or int value for messages to always flush, disabled by default
         *   - [bubble]: bool, defaults to true
         *
         * - filter:
         *   - handler: the wrapped handler's name
         *   - [accepted_levels]: list of levels to accept
         *   - [min_level]: minimum level to accept (only used if accepted_levels not specified)
         *   - [max_level]: maximum level to accept (only used if accepted_levels not specified)
         *   - [bubble]: bool, defaults to true
         *
         * - buffer:
         *   - handler: the wrapped handler's name
         *   - [buffer_size]: defaults to 0 (unlimited)
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *   - [flush_on_overflow]: bool, defaults to false
         *
         * - deduplication:
         *   - handler: the wrapper handler's name
         *   - [store]: The file/path where the deduplication log should be kept, defaults to %kernel.cache_dir%/monolog_dedup_*
         *   - [deduplication_level]: The minimum logging level for log records to be looked at for deduplication purposes, defaults to ERROR
         *   - [time]: The period (in seconds) during which duplicate entries should be suppressed after a given log is sent through, defaults to 60
         *   - [bubble]: bool, defaults to true
         *
         * - group:
         *   - members: the wrapped handlers by name
         *   - [bubble]: bool, defaults to true
         *
         * - whatfailuregroup:
         *   - members: the wrapped handlers by name
         *   - [bubble]: bool, defaults to true
         *
         * - syslog:
         *   - ident: string
         *   - [facility]: defaults to 'user', use any of the LOG_* facility constant but without LOG_ prefix, e.g. user for LOG_USER
         *   - [logopts]: defaults to LOG_PID
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - syslogudp:
         *   - host: syslogd host name
         *   - [port]: defaults to 514
         *   - [facility]: defaults to 'user', use any of the LOG_* facility constant but without LOG_ prefix, e.g. user for LOG_USER
         *   - [logopts]: defaults to LOG_PID
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - swift_mailer:
         *   - from_email: optional if email_prototype is given
         *   - to_email: optional if email_prototype is given
         *   - subject: optional if email_prototype is given
         *   - [email_prototype]: service id of a message, defaults to a default message with the three fields above
         *   - [content_type]: optional if email_prototype is given, defaults to text/plain
         *   - [mailer]: mailer service, defaults to mailer
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *   - [lazy]: use service lazy loading, bool, defaults to true
         *
         *
         * - socket:
         *   - connection_string: string
         *   - [timeout]: float
         *   - [connection_timeout]: float
         *   - [persistent]: bool
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - raven:
         *   - dsn: connection string
         *   - client_id: Raven client custom service id (optional)
         *   - [release]: release number of the application that will be attached to logs, defaults to null
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *   - [auto_stack_logs]: bool, defaults to false
         *
         * - newrelic:
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *   - [app_name]: new relic app name, default null
         *
         * - hipchat:
         *   - token: hipchat api token
         *   - room: room id or name
         *   - [notify]: defaults to false
         *   - [nickname]: defaults to Monolog
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *   - [use_ssl]: bool, defaults to true
         *   - [message_format]: text or html, defaults to text
         *   - [host]: defaults to "api.hipchat.com"
         *   - [api_version]: defaults to "v1"
         *
         *
         * - cube:
         *   - url: http/udp url to the cube server
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - amqp:
         *   - exchange: service id of an AMQPExchange
         *   - [exchange_name]: string, defaults to log
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - error_log:
         *   - [message_type]: int 0 or 4, defaults to 0
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - null:
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - debug:
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - logentries:
         *   - token: logentries api token
         *   - [use_ssl]: whether or not SSL encryption should be used, defaults to true
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *   - [timeout]: float
         *   - [connection_timeout]: float
         *
         * - flowdock:
         *   - token: flowdock api token
         *   - source: human readable identifier of the application
         *   - from_email: email address of the message sender
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         *
         * - rollbar:
         *   - id: RollbarNotifier service (mandatory if token is not provided)
         *   - token: rollbar api token (skip if you provide a RollbarNotifier service id)
         *   - [config]: config values from https://github.com/rollbar/rollbar-php#configuration-reference
         *   - [level]: level name or int value, defaults to DEBUG
         *   - [bubble]: bool, defaults to true
         */
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
                $pushoverHandlerValidator = new Validator\ValidatePushoverHandlerConfig($handlerConfig);
                $pushoverHandlerValidator->validate();
                $title = (isset($handlerConfig['title']) ? $handlerConfig['title'] : null);
                $bubble = (isset($handlerConfig['bubble']) ? $handlerConfig['bubble'] : true);

                return new PushoverHandler($handlerConfig['token'], $handlerConfig['user'], $title, $handlerConfig['level'], $bubble);
                break;
            case 'redis':
                $redisHandlerValidator = new Validator\ValidateRedisHandlerConfig($handlerConfig);
                $redisHandlerValidator->validate();
                $bubble = (isset($handlerConfig['bubble']) ? $handlerConfig['bubble'] : true);
                $capSize = (isset($handlerConfig['cap_size']) ? $handlerConfig['cap_size'] : false);

                return new RedisHandler($handlerConfig['redis_client'], $handlerConfig['key'], $bubble, $capSize);
                break;
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