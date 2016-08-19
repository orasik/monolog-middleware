<?php

namespace MonologMiddleware;

use Monolog\Logger;
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
        $level = $this->getLogLevel($response->getStatusCode());

        $this->logRequest($level, $request);
        $this->logResponse($level, $response);

        return $next($request, $response);
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
     * @param int $level
     * @param ServerRequestInterface $request
     */
    public function logRequest($level, $request)
    {
        $message = sprintf("Request body: %s ", $request->getBody());
        $context = [
            'uri'    => $request->getUri()->getPath(),
            'method' => $request->getMethod(),
            'params' => $request->getQueryParams(),
        ];

        $this->logger->addRecord($level, $message, $context);
    }

    /**
     * @param int $level
     * @param ResponseInterface $response
     */
    public function logResponse($level, $response)
    {
        $message = sprintf("Response body: %s ", $response->getBody());
        $context = [
            'response_code' => $response->getStatusCode()
        ];

        $this->logger->addRecord($level, $message, $context);
    }
}
