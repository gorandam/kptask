<?php
declare(strict_types=1);

namespace Kptask\Core\Controller;

use Kptask\Core\Controller\AbstractBaseController;
use Kptask\Core\Validator\ValidatorInterface;
use Kptask\Core\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use Zend\Session\ManagerInterface;
use Tamtamchik\SimpleFlash\Flash;
use Zend\Config\Config;

/**
 * Class RegisterController
 * @package Kptask\Core\Controller
 */
class RegisterController extends AbstractBaseController
{
    /**
     * @var ValidatorInterface
     */
    private $validator;


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
     * @param ValidatorInterface $validator
     * @param UserServiceInterface $userService
     */
    public function __construct(
        Environment $twig,
        ManagerInterface $sessionManager,
        Flash $flash,
        Config $config,
        ValidatorInterface $validator,
        UserServiceInterface $userService
    ) {
        parent::__construct($twig, $sessionManager, $flash, $config);
        $this->validator = $validator;
        $this->userService = $userService;
    }


    /**
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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    protected function create()
    {
        $data = $this->request->getParsedBody();
        try {
            $userId = $this->userService->create($data);
        } catch (\Exception $e) {
            $this->flash->error($e->getMessage());
            $this->flash->error('Could not register.');

            return $this->render('form', [
                    'data' => $data,
                ]);
        }
        $this->flash->success('Welcome to Kptask. Go to you email to confirm registration.');
        return $this->redirect('/login/index');
    }
}
