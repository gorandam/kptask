<?php
declare(strict_types = 1);
namespace Kptask\User\Service;

use Kptask\Core\Email\MailerInterface;
use Kptask\Core\Notification\NotificationManagerInterface;
use Kptask\Core\Repository\UserRepositoryInterface;
use Kptask\Core\Service\UserServiceInterface;
use Kptask\User\Factory\UserEntityFactory;
use Kptask\User\Validator\UserValidationManager;
use Zend\Session\ManagerInterface;
use Zend\Stdlib\ArrayObject;

/**
 * Class UserRepository
 * @package Kptask\User\Repository
 */
class UserService implements UserServiceInterface
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;
    /**
     *
     * @var ManagerInterface
     */
    private $sessionManager;
    /**
     *
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var UserEntityFactory
     */
    private $domainFactory;
    /**
     * @var UserValidationManager
     */
    private $validationManager;
    /**
     * @var NotificationManagerInterface
     */
    private $notificationManager;


    /**
     * UserService constructor.
     * @param UserRepositoryInterface $userRepo
     * @param ManagerInterface $sessionManager
     * @param UserEntityFactory $domainFactory
     * @param UserValidationManager $validationManager
     * @param NotificationManagerInterface $notificationManager
     */
    public function __construct(UserRepositoryInterface $userRepo, ManagerInterface $sessionManager, UserEntityFactory $domainFactory, UserValidationManager $validationManager, NotificationManagerInterface $notificationManager)
    {
        $this->userRepo = $userRepo;
        $this->sessionManager = $sessionManager;
        $this->domainFactory = $domainFactory;
        $this->validationManager = $validationManager;
        $this->notificationManager = $notificationManager;
    }


    /**
     * @param $data
     * @return mixed
     */
    public function create($data)
    {
        if ($this->validationManager->validate($data)) {
            $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
            $userId = $this->createModel($data);
            if ($userId) {
                $this->notificationManager->dispatch($data);
            }
            return $userId;
        }
    }

    /**
     * @return ArrayObject
     */
    public function getAll()
    {
        return $this->userRepo->fetchAll();
    }

    /**
     * @param $userId
     * @return array
     */
    public function getById($userId)
    {
        $userModel = $this->userRepo->fetchById($userId);
        return $userModel->getArrayCopy();
    }

    /**
     * @param $data
     * @return bool
     */
    public function login($data)
    {
        $userModel = $this->userRepo->fetchByEmail($data['email']);
        if (empty($userModel)) {
            return false;
        }
        if (password_verify($data['password'], $userModel[0]->getPassword())) {
            $this->sessionManager->getStorage()->offsetSet('userId', $userModel[0]->getUserId());
            return $userModel[0]->getUserId();
        }

        return false;
    }

    /**
     * @param $data
     * @return string
     */
    protected function createModel($data)
    {
        $userModel = $this->domainFactory->create($data);
        return $this->userRepo->create($userModel);
    }

    /**
     * @param $data
     */
    public function update($data)
    {
        $userModel = $this->userRepo->fetchById($data['userId']);
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $userModel->exchangeArray($data);
        $this->userRepo->update($userModel);
    }

    /**
     * @param $data
     * @return void
     */
    public function delete($data)
    {
        if (!isset($data['userId'])) {
            throw new \InvalidArgumentException('No data given to process');
        }
        $userModel = $this->userRepo->fetchById($data['userId']);
        $this->userRepo->delete($userModel);
    }
}
