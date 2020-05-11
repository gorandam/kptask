<?php
declare(strict_types=1);

namespace Kptask\Core\Mapper;

use Kptask\User\Entity\User;
use Zend\Stdlib\ArrayObject;

/**
 * Interface UserMapperInterface
 * @package Kptask\Core\Mapper
 */
interface UserMapperInterface
{
    /**
     * Fetches a list of User models.
     *
     * @param array $params
     * @param $limit
     *
     * @return ArrayObject
     */
    public function fetchAll($params, $limit);

    /**
     * Fetches a single User model.
     *
     * @param string $userId
     *
     * @return User
     */
    public function fetchSingle($userId);

    /**
     * Creates User model.
     *
     * @param User $userModel
     *
     * @return bool
     */
    public function create(User $userModel);

    /**
     * Updates User model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function update(User $user);

    /**
     * Deletes a single User Model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function delete(User $user);
}
