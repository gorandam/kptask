<?php
declare(strict_types = 1);
namespace Kptask\Core\App;

use Psr\Log\LoggerInterface as Logger;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest as Request;

/**
 * Class WebSkeletor
 * @package Kptask\Core\App
 */
class WebSkeletor
{
    /**
     * @var \DI\Container
     */
    private $dic;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var Logger
     */
    private $logger;

    private $timer;

    /**
     * WebSkeletor constructor.
     *
     * @param \DI\Container $dic
     * @param Logger $logger
     */
    public function __construct(\DI\Container $dic, Logger $logger)
    {
        $this->dic = $dic;
        $this->response = new Response();
        $this->logger = $logger;
        $this->handle();
    }

    /**
     * Handle request and dispatch route.
     */
    private function handle()
    {
//        $this->timer = microtime();
//        $this->logger->debug('init : ' . (microtime() - $this->timer));
        $dispatcher = $this->dic->get(\FastRoute\Dispatcher::class);
        $uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $route = $dispatcher->dispatch(
            $_SERVER['REQUEST_METHOD'],
            $uri
        );
//        $this->logger->debug('dispatch : ' . (microtime() - $this->timer));
        $request = Request::fromGlobals();

        // var_dump($request);
        // die();


        switch ($route[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
//                var_dump($_SERVER['REQUEST_URI']); die();
                $this->response->getBody()->write(\GuzzleHttp\json_encode([
                    'error' => sprintf('Requested route %s does not exist.', $_SERVER['REQUEST_URI'])
                ]));
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $this->response->getBody()->write(\GuzzleHttp\json_encode([
                    'error' => 'Method is not allowed.'
                ]));
                break;
            case \FastRoute\Dispatcher::FOUND:
                $controller = $route[1];
                $parameters = $route[2];

                // @TODO this must be better
                foreach ($parameters as $name => $value) {
                    $request = $request->withAttribute($name, $value);
                }

//                $this->logger->debug('route resolved : ' . print_r($route, true) . ' - ' . (microtime() - $this->timer));

                try {
                    $next = $this->dic->get($controller);

                    $this->response = $this->dic->call(\Kptask\Core\Middleware\AuthMiddleware::class, [
                        $request, $this->response, $next
                    ]);

                    // var_dump($this->response);
                    // die();

//                    $this->logger->debug('action complete : ' . (microtime() - $this->timer));
//                    $this->response = $this->dic->call($controller, [
//                        $request, $this->response
//                    ]);
                } catch (\Exception $e) {
//                    die('1231');
                    $this->handleErrors($e);
                }
//        $container->get(Logger::class)->notice('running app controller : ' . $controller);

                break;
        }
    }

    /**
     * Handle errors and prepare response object.
     *
     * @TODO send email notification
     *
     * @param \Exception $exception
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    private function handleErrors(\Exception $exception)
    {
        $msg = $exception->getMessage();

        switch (get_class($exception)) {
            case \InvalidArgumentException::class:
                $this->response->getBody()->write(\GuzzleHttp\json_encode([
                    'error' => $msg
                ]));

                break;
            case \Exception::class:
//            break;

            default:
                $this->response->getBody()->write(\GuzzleHttp\json_encode([
                    'error' => $msg . PHP_EOL . $exception->getTraceAsString(),
//                    'trace' => $exception->getTraceAsString()
                ]));

                break;
        }

        $this->dic->get(Logger::class)->error($msg);
        $this->dic->get(Logger::class)->error($exception->getTraceAsString());
    }

    /**
     * Sends respond back to client.
     *
     */
    public function respond()
    {
        // var_dump($this->response);
        // die();
        // Send response
        if (!headers_sent()) {
            // Status
            header(sprintf(
                'HTTP/%s %s %s',
                $this->response->getProtocolVersion(),
                $this->response->getStatusCode(),
                $this->response->getReasonPhrase()
            ));

            // Headers
            foreach ($this->response->getHeaders() as $name => $values) {
                foreach ($values as $value) {
                    header(sprintf('%s: %s', $name, $value), false);
                }
            }
        }
//        $this->logger->debug('set headers : ' . (microtime() - $this->timer));

        // Send Body
        if (!in_array($this->response->getStatusCode(), [205, 304])) {
            $body = $this->response->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }
            $chunkSize = 4096;
            $contentLength  = $this->response->getHeaderLine('Content-Length');
            if (!$contentLength) {
                $contentLength = $body->getSize();
            }
//            $this->logger->debug('start sending body : ' . (microtime() - $this->timer));

            if (isset($contentLength)) {
                $amountToRead = $contentLength;
                while ($amountToRead > 0 && !$body->eof()) {
                    $data = $body->read(min($chunkSize, $amountToRead));
                    echo $data;
                    $amountToRead -= strlen($data);

                    if (connection_status() != CONNECTION_NORMAL) {
                        break;
                    }
                }
            } else {
                while (!$body->eof()) {
                    echo $body->read($chunkSize);
                    if (connection_status() != CONNECTION_NORMAL) {
                        break;
                    }
                }
            }
//            $this->logger->debug('response sent : ' . (microtime() - $this->timer));
        }
    }
}
