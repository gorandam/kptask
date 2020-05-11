<?php
declare(strict_types=1);
namespace Kptask\User\Controller;

use Kptask\Core\Controller\AbstractBaseController;
use Kptask\Core\Validator\ValidatorInterface;
use Kptask\Core\Service\UserServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Twig\Environment;
use Zend\Session\ManagerInterface;
use Tamtamchik\SimpleFlash\Flash;
use Zend\Config\Config;


/**
 * Class UserController
 * @package Kptask\User\Controller
 */
class UserController extends AbstractBaseController
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
    public function register()
    {
        return $this->render('form');
    }

    /**
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $data['case'] = 'userindex';
        $users = $this->userService->getAll();
        return $this->render('index', [
//            'data' => $data,
            'userId' => $this->sessionManager->getStorage()->offsetGet('userId'),
            'users' => $users
        ]);
    }

    /**
     * @return ResponseInterface
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function form()
    {
        $data = $this->request->getParsedBody();
        if (isset($data['save'])) {
            try {
                if ((int) $data['userId'] === 0) {
                    unset($data['save']);
                    unset($data['userId']);
                   $this->create($data);
                } else {
                    $this->update($data);
                }
            } catch (\Exception $e) {
                $this->flash->error('Could not create user.');

                return $this->render('form', [
                    'data' => $data,
                ]);
            }
            $this->flash->success('User manually created/updated');
            return $this->redirect('/user/index');
        }
        //display user data when updating
        $userId = $this->request->getAttribute('userId', null);

        if ((int) $userId > 0) {
            $data = $this->userService->getById($userId);
        }
        //render form
        return $this->render('form', [
            'data' => $data,
            'userId' => $userId
        ]);
    }

     /**
     * @return ResponseInterface
     */
    public function delete()
    {
        $data = $this->request->getParsedBody();
        try {
            $this->userService->delete($data);
        } catch (\Exception $e) {
            $this->flash->error('Could not delete user.');
            return $this->redirect('/user/index');
        }

        $this->flash->success('User successfully deleted.');
        return $this->redirect('/user/index');
    }

    /**
     * @param $data
     * @return string
     */
    protected function create($data)
    {
        return $this->userService->create($data);
    }

    /**
     * @param $data
     */
    protected function update($data)
    {
        $this->userService->update($data);
    }
}
