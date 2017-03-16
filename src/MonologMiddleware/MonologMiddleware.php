<?php

namespace MonologMiddleware;

use Monolog\Logger;
use MonologMiddleware\Loggable\LoggableData;
use MonologMiddleware\Loggable\LoggableProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Response;
use Zend\Stratigility\MiddlewareInterface;

/**
 * Class MonologMiddleware
 * @package MonologMiddleware
 */
class MonologMiddleware implements MiddlewareInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var LoggableProvider
     */
    protected $loggableProvider;

    /**
     * MonologMiddleware constructor.
     * @param Logger $logger
     */
    public function __construct(Logger $logger, loggableProvider $loggableProvider)
    {
        $this->logger = $logger;
        $this->loggableProvider = $loggableProvider;
    }


    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return mixed
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $nextResponse = $next($request, $response);
        $level = $this->getLogLevel($response->getStatusCode());

        $this->log($level, $request, $nextResponse);

        return $nextResponse;
    }

    /**
     * @param int $responseCode
     * @return int
     */
    public function getLogLevel($responseCode)
    {
        // Log level will be dependant on Response Code
        switch ($responseCode) {
            case Response::HTTP_OK:
            case Response::HTTP_ACCEPTED:
            case Response::HTTP_CREATED:
            case Response::HTTP_FOUND:
                $level = Logger::INFO;
                break;
            case Response::HTTP_NOT_FOUND:
            case Response::HTTP_NOT_ACCEPTABLE:
            case Response::HTTP_BAD_REQUEST:
            case Response::HTTP_BAD_GATEWAY:
                $level = Logger::WARNING;
                break;
            case Response::HTTP_INTERNAL_SERVER_ERROR:
                $level = Logger::ERROR;
                break;
            default:
                $level = Logger::DEBUG;
        }

        return $level;
    }

    /**
     * @param $level
     * @param ServerRequestInterface $request
     * @param Response $response
     * @return bool
     */
    public function log($level, ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->logger->addRecord($level, $this->loggableProvider->format($request, $response));
    }
}
