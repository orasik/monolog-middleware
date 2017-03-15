<?php
namespace MonologMiddleware\Factory;

use Interop\Container\ContainerInterface;
use Monolog\Logger;
use MonologMiddleware\Exception\MonologConfigException;
use MonologMiddleware\Extension\MonologConfigurationExtension;
use MonologMiddleware\Loggable\LoggableProvider;
use MonologMiddleware\MonologMiddleware;

/**
 * Class MonologMiddlewareFactory
 * @package MonologMiddleware\Factory
 */
class MonologMiddlewareFactory
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
        $loggables = (isset($config['monolog']['loggables']) ? $config['monolog']['loggables'] : null);

        $loggableProvider = new LoggableProvider($loggables);

        /**
         * @var Logger
         */
        $monologLogger = new Logger($loggerName);
        $monologLogger->setHandlers($logHandlers);

        return new MonologMiddleware($monologLogger, $loggableProvider);
    }
}