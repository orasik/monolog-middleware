<?php
namespace MonologMiddleware\Factory;

use Monolog\Logger;
use MonologMiddleware\Exception\MonologConfigException;
use MonologMiddleware\Extension\MonologConfigurationExtension;
use MonologMiddleware\Loggable\LoggableProvider;
use MonologMiddleware\MonologMiddleware;
use Psr\Container\ContainerInterface;

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
     * @throws \Exception
     * @throws \MonologMiddleware\Exception\MonologHandlerNotImplementedException
     * @throws \Monolog\Handler\MissingExtensionException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $serviceContainer)
    {
        $config = $serviceContainer->get('config');
        if (null === $config) {
            throw new MonologConfigException("Can not find monolog configuration in your config. Make sure to have monolog configuration array in your config");
        }

        $helper = new MonologConfigurationExtension($config['monolog']);
        $logHandlers = $helper->getLogHandlers();
        $loggerName = ($config['monolog']['logger_name'] ?? 'monolog');
        $loggables = ($config['monolog']['loggables'] ?? null);

        $loggableProvider = new LoggableProvider($loggables);

        /**
         * @var Logger
         */
        $monologLogger = new Logger($loggerName);
        $monologLogger->setHandlers($logHandlers);

        return new MonologMiddleware($monologLogger, $loggableProvider);
    }
}
