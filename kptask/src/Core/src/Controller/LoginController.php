<?php
declare(strict_types=1);
namespace Kptask\Core\Controller;

use Kptask\Core\Controller\AbstractBaseController;

;
use Kptask\Core\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Tamtamchik\SimpleFlash\Flash;
use Twig\Environment;
use Zend\Config\Config;
use Zend\Session\ManagerInterface;

/**
 * Class LoginController
 * @package Kptask\Core\Controller
 */
class LoginController extends AbstractBaseController
{

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * Controller constructor.
     *
     * @param Environment $twig
     * @param ManagerInterface $sessionManager
     * @param Flash $flash
     * @param Config $config
     * @param UserServiceInterface $userService
     */
    public function __construct(Environment $twig, ManagerInterface $sessionManager, Flash $flash, Config $config, UserServiceInterface $userService)
    {
        $this->twig = $twig;
        $this->sessionManager = $sessionManager;
        $this->flash = $flash;
        $this->config = $config;
        $this->userService = $userService;

        parent::__construct($twig, $sessionManager, $flash, $config);
    }


    /**
     * Render the login form.
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        return $this->render('form');
    }

    /**
     * @return ResponseInterface
     */
    public function logout()
    {
        $this->sessionManager->getStorage()->offsetSet('userId', null);
        $this->flash->success('You have successfully logged out your account.');

        return $this->redirect('/');
    }


    /**
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function login()
    {
        $data = $this->request->getParsedBody();
        if ($userId = $this->userService->login($data)) {
            $this->flash->success('Welcome ' . $data['email']);
            return $this->render('loggedIn', ['userId' => $userId]);
        }
        $this->flash->error('Login failed.');
        return $this->redirect('/login/index');
    }
}
