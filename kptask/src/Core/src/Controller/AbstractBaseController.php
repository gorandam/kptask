<?php
declare(strict_types=1);

namespace Kptask\Core\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Psr7\ServerRequest as Request;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use Zend\Session\ManagerInterface;
use Tamtamchik\SimpleFlash\Flash;
use Zend\Config\Config;

/**
 * Class AbstractBaseController
 * @package Kptask\Core\Controller
 */
abstract class AbstractBaseController
{
    /**
     * @var Environment
     */
    public $twig;

    /**
     * @var ManagerInterface
     */
    public $sessionManager;

    /**
     * @var Request
     */
    public $request;

    /**
     * @var Response
     */
    public $response;

    /**
     * @var Flash
     */
    public $flash;

    /**
     * @var Config
     */
    public $config;

    /**
     * Controller constructor.
     *
     * @param Environment $twig
     * @param ManagerInterface $sessionManager
     * @param Flash $flash
     * @param Config $config
     */
    public function __construct(Environment $twig, ManagerInterface $sessionManager, Flash $flash, Config $config)
    {
        $this->twig = $twig;
        $this->sessionManager = $sessionManager;
        $this->flash = $flash;
        $this->config = $config;
    }

    /**
     * Executes required action. Main application handler will handle all general exceptions.
     * If required, exceptions can be handled on action basis.
     *
     * @param Request $request
     * @param ResponseInterface $response
     * @param callable|null $next
     * @return bool
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __invoke(Request $request, Response $response, callable $next = null)
    {
        $action = $request->getAttribute('action', 'index');

        if (!method_exists($this, $action)) {
            echo $this->twig->render('error404.twig', ['msg' => 'Action does not exist']);
            return false;
        }

        $this->request  = $request;
        $this->response = $response;

        return $this->$action();
    }

    /**
     * Redirect client to given uri.
     *
     * @param $uri
     *
     * @return ResponseInterface
     */
    public function redirect($uri)
    {
        $url = $this->config->offsetGet('baseUrl') . $uri;
        return $this->response->withStatus(302)->withHeader('Location', $url);
    }

    /**
     * @param $action
     * @param array $params
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function render($action, $params = [])
    {
        $className = explode('\\', get_class($this));
        $controller = strtolower(str_replace('Controller', '', $className[count($className) - 1]));

        //set flash messages now if any
        if ($this->flash->hasMessages()) {
            $this->twig->addGlobal('messages', $this->flash->display());
        }


        $template = "/{$controller}/" . $action . '.twig';

        $this->response->getBody()->write($this->twig->render($template, $params));

        return $this->response;
    }
}
