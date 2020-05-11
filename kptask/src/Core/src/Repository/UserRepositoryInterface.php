<?php
declare(strict_types=1);

namespace Kptask\Core\Repository;

use Kptask\User\Entity\User;
use Zend\Stdlib\ArrayObject;

/**
 * Interface UserRepositoryInterface
 * @package Kptask\Core\Repository
 */
interface UserRepositoryInterface
{
    /**
     * Fetches a list of UserEntity models.
     *
     * @param array $params
     *
     * @return ArrayObject
     */
    public function fetchAll($params = array());

    /**
     * Fetches a single User by params
     *
     * @param array $params
     *
     * @return User
     */
    public function fetch($params = array());

    /**
     * Fetches a single User by params
     *
     * @param string $email
     *
     * @return User
     */
    public function fetchByEmail($email);

    /**
     * Fetches a single User model by id.
     *
     * @param int $userId
     *
     * @return User
     */
    public function fetchById($userId);

    /**
     * Creates User model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user);

    /**
     * Updates User model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function update(User $user);

    /**
     * Deletes a single User entity.
     *
     * @param User $user
     *
     * @return bool
     */
    public function delete(User $user);
}
