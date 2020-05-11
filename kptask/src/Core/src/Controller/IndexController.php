<?php
declare(strict_types=1);
namespace Kptask\Core\Controller;

use Kptask\Core\Controller\AbstractBaseController;
use Psr\Http\Message\ResponseInterface;
use Tamtamchik\SimpleFlash\Flash;
use Twig\Environment;
use Zend\Config\Config;
use Zend\Session\ManagerInterface;

/**
 * Class IndexController
 * @package Kptask\Core\Controller
 */
class IndexController extends AbstractBaseController
{
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

        parent::__construct($twig, $sessionManager, $flash, $config);
    }

    /**
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        return $this->render('index', ['message' => 'Home page']);
    }
}
