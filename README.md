[![Build Status](https://travis-ci.org/orasik/monolog-middleware.svg?branch=master)](https://travis-ci.org/orasik/monolog-middleware)
[![Latest Stable Version](https://poser.pugx.org/oras/monolog-middleware/v/stable)](https://packagist.org/packages/oras/monolog-middleware)
[![Total Downloads](https://poser.pugx.org/oras/monolog-middleware/downloads)](https://packagist.org/packages/oras/monolog-middleware)
[![License](https://poser.pugx.org/oras/monolog-middleware/license)](https://packagist.org/packages/oras/monolog-middleware)

# Monolog Logger Middleware
Monolog Middleware to be used with PSR-7 middleware frameworks like mezzio (formerly Zend Expressive) and Slim.

**Now it does support mezzio `3.*`**

To use with Zend Expressive `1.*` please install version `1.1.4`
To use with Zend Expressive `2.*` please install version `2.0.0`

 `loggables` setting inspired by Guzzle Log Format. You can set any data in request/response/headers that you want to log from config file
 rather than in code to give more flexibility in logging more/less data based on your needs.



### Installation

##### 1) Install middleware using composer
```sh
composer require oras/monolog-middleware
```
##### 2) Add configuration
Then in your mezzio `config/autoload/` directory, created a new config file call it: `logger.local.php`

As a starting point, you can have the following in the file:

```php
use Monolog\Logger;

return [
    'monolog' =>
        [
            'logger_name' => 'MyLog',
            'loggables' => '[{host}] {request}/{response}', // optional and current one is default format that will be logged
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

Please refer to Loggables list at end for all possible variables.


##### 3) Add factory and middleware to `dependencies.global.php` file as follows:
```php
'factories' => [

            \MonologMiddleware\MonologMiddleware::class => \MonologMiddleware\Factory\MonologMiddlewareFactory::class,
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
                \MonologMiddleware\MonologMiddleware::class,
                ],
             'allowed_methods' => ['GET'],
         ],
];
 ```

 Now every time you call the route `/`, you'll get logs for request and response.

 **By default, MonologMiddleware will record logs in debug mode. If you want to handle different levels, just change `level` in config.**


### Requirements
- PHP >= 7.3


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
        'type'       => 'slack',
        'token'      => 'your-slack-token',
        'channel'    => '#your-slack-channel',
        'level'      => Logger::DEBUG,
        'icon_emoji' => '::ghost::', // optional
        'bubble'     => true, // optional
    ],
];
```

##### Pushover
```php
$pushOverHandler = [
'pushover'   =>
    [
        'type'    => 'pushover',
        'token'   => 'your-pushover-token',
        'user'    => 'pushover user',
        'level'   => Logger::ERROR,
        'title'   => 'Log title', // optional
        'bubble'  => true, // optional
    ],
];
```

##### Native Email handler
```php
$nativeEmailHandler = [
'native_email'   =>
    [
        'type'             => 'native_email',
        'level'            => Logger::CRITICAL,
        'from_email'       => 'logs@yourserver.com',
        'to_email'         => 'email@email.com',
        'subject'          => 'Email subject', // optional
        'max_column_width' => 70, //optional
        'bubble'           => true, // optional
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

##### Redis handler
```php
$redisHandler = [
'redis'   =>
    [
        'type'          => 'redis',
        'level'         => Logger::DEBUG,
        'redis_client'  => new \Redis(),
        'key'           => 'monolog',
    ],
];
```

##### FirePHP handler
```php
$redisHandler = [
'firephp'   =>
    [
        'type'          => 'firephp',
        'level'         => Logger::DEBUG,
    ],
];
```

##### NewRelic handler
```php
$redisHandler = [
'new_relic'   =>
    [
        'type'          => 'new_relic',
        'level'         => Logger::DEBUG,
        'app_name'      => 'Monolog', // optional
    ],
];
```


#### Loggables list

To log request/response body you can use `{req_body}` and `{res_body}` respectively in `format` setting.

Full list of logs variables with description:

| Variable | 	Substitution |
| --- | --- |
| {request}	| Full HTTP request message |
| {response}	| Full HTTP response message |
| {ts}	 | Timestamp |
| {host} |	Host of the request |
| {method} |	Method of the request |
| {url}	 | URL of the request |
| {host} |	Host of the request |
| {protocol} | 	Request protocol |
| {version} | Protocol version |
| {resource}|	Resource of the request (path + query + fragment) |
| {port}	| Port of the request |
| {hostname} | 	Hostname of the machine that sent the request |
| {code} | Status code of the response (if available) |
| {phrase} | Reason phrase of the response (if available) |
| {curl_error} | Curl error message (if available) |
| {curl_code} | Curl error code (if available) |
| {curl_stderr} | Curl standard error (if available) |
| {connect_time} | Time in seconds it took to establish the connection (if available) |
| {total_time}	 | Total transaction time in seconds for last transfer (if available) |
| {req_header_*} | Replace * with the lowercased name of a request header to add to the message |
| {res_header_*} | Replace * with the lowercased name of a response header to add to the message |
| {req_body} | Request body  |
| {res_body} | Response body|



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


> Monolog Middleware was written  during my commute time. Written with passion on SouthWest Trains. **Please mind the gap!**
