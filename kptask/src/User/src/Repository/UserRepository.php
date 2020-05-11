<?php
declare(strict_types = 1);
namespace Kptask\User\Repository;

use Exception;
use Kptask\Core\Mapper\UserMapperInterface;
use Kptask\Core\Repository\UserRepositoryInterface;
use Kptask\User\Entity\User;
use Zend\Stdlib\ArrayObject;

/**
 * Class UserRepository
 * @package Kptask\User\Repository
 */
class UserRepository implements UserRepositoryInterface
{

    /**
     * @var UserMapperInterface
     */
    private $userStorage;

    /**
     * @var \DateTime
     */
    private $dateTime;


    /**
     * UserRepository constructor.
     * @param UserMapperInterface $userStorage
     * @param \DateTime $dateTime
     */
    public function __construct(UserMapperInterface $userStorage, \DateTime $dateTime)
    {
        $this->userStorage = $userStorage;
        $this->dateTime = $dateTime;
    }

    /**
     * Fetches a list of UserEntity models.
     *
     * @param array $params
     *
     * @return ArrayObject
     */
    public function fetchAll($params = array())
    {
        return $this->userStorage->fetchAll($params);
    }

    /**
     * Fetches a single User by params
     *
     * @param array $params
     *
     * @return ArrayObject
     */
    public function fetch($params = array())
    {
        $limit = 1;
        return $this->userStorage->fetchAll($params, $limit);
    }

    /**
     * Fetches a single User by params
     *
     * @param string $email
     *
     * @return User
     */
    public function fetchByEmail($email)
    {
        return $this->fetch(['email' => $email]);
    }

    /**
     * Fetches a single User model by id.
     *
     * @param int $userId
     *
     * @return User
     */
    public function fetchById($userId)
    {
        return $this->userStorage->fetchSingle($userId);
    }

    /**
     * Creates user model.
     *
     * @param User $userModel
     *
     * @return string
     * @throws Exception
     */
    public function create(User $userModel)
    {
        return $this->userStorage->create($userModel);
    }

    /**
     * Updates user model.
     *
     * @param User $user
     *
     * @return void
     */
    public function update(User $user)
    {
        $this->userStorage->update($user);
    }

    /**
     * Deletes a single user entity.
     *
     * @param User $userModel
     * @return void
     */
    public function delete(User $userModel)
    {
        $this->userStorage->delete($userModel);
    }
}
