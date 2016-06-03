# Monolog Logger Middleware
Monolog Middleware to be used with PSR-7 middleware frameworks like Zend Expressive and Slim.

This is still in development, more handlers will be added later.

### Installation

##### 1) Install middleware using composer
```sh
composer require oras/monolog-middleware
```
##### 2) Add configuration
Then in your Zend Expressive `config/autoload/` directory, created a new config file call it: `logger.local.php`

As a starting point, you can have the following in the file:

```php
use Monolog\Logger;

return [
    'monolog' =>
        [
            'logger_name' => 'MyLog',
            'handlers' =>
                [
                    'main'   =>
                        [
                            'type'   => 'stream',
                            'path'   => "data/main.log",
                            'level'  => Logger::DEBUG,
                            'bubble' => true,
                        ],
                ],
        ],
];
```


##### 3) Add factory and middleware to `dependencies.global.php` file as follows:
```php
'factories' => [

            \MonologMiddleware\MonologMiddleware::class => \MonologMiddleware\Extension\MonologMiddlewareFactory::class,
        ],
```

##### 4) Now to start recording logs of request/response for a middleware, just put the following line after routing.

 Example:
 ```php
 'routes' => [
         [
             'name' => 'home',
             'path' => '/',
             'middleware' => [
                App\Action\HomePageAction::class,
                \MonologMiddleware\MonologMiddleware:class,
                ],
             'allowed_methods' => ['GET'],
         ],
];
 ```

 Now every time you call the route `/`, you'll get logs for request and response.

 **By default, MonologMiddleware will record logs in debug mode. If you want to handle different levels, please refer to Extending Middleware section.**


### Requirements
- PHP > 5.5


### Configuration examples
Full example of each implemented handler in Monolog Middleware. Please note that these might not be ALL handlers
supported by Monolog, they are just the implemented in this middleware.

All lines are required unless stated.

##### Stream
```php
$streamHandler = [
'main'   =>
    [
        'type'   => 'stream',
        'path'   => 'data/main.log',
        'level'  => Logger::DEBUG,
        'bubble' => true, // optional
    ],
];
```

##### Loggly
```php
$logglyHandler = [
'loggly'   =>
    [
        'type'   => 'loggly',
        'token'   => 'your-loggly-token',
        'level'  => Logger::DEBUG,
        'bubble' => true, //optional
    ],
];
```

##### Slack
```php
$slackHandler = [
'slack'   =>
    [
        'type'    => 'slack',
        'token'   => 'your-slack-token',
        'channel' => '#your-slack-channel',
        'level'   => Logger::DEBUG,
        'icon_emoji'    => '::ghost::', // optional
        'bubble'  => true, // optional
    ],
];
```

##### Pushover
```php
$pushOverHandler = [
'pushover'   =>
    [
        'type'    => 'pushover',
        'token'   => 'your-slack-token',
        'user' => '#your-slack-channel',
        'level'   => Logger::DEBUG,
        'title'    => 'Log title', // optional

        'bubble'  => true, // optional
    ],
];
```

##### Native Email handler
```php
$nativeEmailHandler = [
'native_email'   =>
    [
        'type'    => 'native_email',
        'level'   => Logger::DEBUG,
        'from_email'   => 'your-slack-token',
        'to_email' => '#your-slack-channel',
        'subject'    => 'Email subject', // optional
        'max_column_width' => 70, //optional
        'bubble'  => true, // optional
    ],
];
```

##### Browser Console handler
```php
$browserConsoleHandler = [
'browser_console'   =>
    [
        'type'    => 'browser_console',
        'level'   => Logger::DEBUG,
    ],
];
```
#### Extending Middleware

To extend the middleware to log your own format, or specific data like cookies, server params .. etc. You can do that easily using the following steps:

1. Create a factory class.
I have named it `MyMonologMiddlewareFactory` which will call a `MyMonologMiddleware` class which will be your customised middleware to log.

```php
class MyMonologMiddlewareFactory
{

    /**
     * @param ContainerInterface $serviceContainer
     * @return MonologMiddleware
     * @throws MonologConfigException
     */
    public function __invoke(ContainerInterface $serviceContainer)
    {
        $config = $serviceContainer->get('config');
        if (null === $config) {
            throw new MonologConfigException("Can not find monolog configuration in your config. Make sure to have monolog configuration array in your config");
        }

        $helper = new MonologConfigurationExtension($config['monolog']);
        $logHandlers = $helper->getLogHandlers();
        $loggerName = (isset($config['monolog']['logger_name']) ? $config['monolog']['logger_name'] : 'monolog');
        /**
         * @var Logger
         */
        $monologLogger = new Logger($loggerName);
        $monologLogger->setHandlers($logHandlers);

        return new MyMonologMiddleware($monologLogger);
    }
}

```

2. Create Middleware class

```php
class MonologMiddleware implements MiddlewareInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * MonologMiddleware constructor.
     * @param Logger $logger
     * @TODO: add monolog lib
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        // Here you set logger level, message or any data that you'd like from your request or response.
        // For example, I am going to log cookie params

        $this->logger->addInfo(Logger::INFO, implode(", ", $request->getCookieParams());
        return $next($request, $response);
    }

}
```

3. Add your factory and middleware to global dependency file. Assuming you have your middleware and factory in the same directory, the config will be:

```php
    'factories' => [
            MyMonologMiddleware::class => MyMonologMiddlewareFactory::class,
    ],
```

That's it ... you're ready to use your own customised logger.


#### TODO:
- Add more handlers (List TBC)

> Monolog Middleware was written  during my commute time. Written with passion on SouthWest Trains. **Please mind the gap!**
