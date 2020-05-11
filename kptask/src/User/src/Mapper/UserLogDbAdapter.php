<?php
declare(strict_types=1);

namespace Kptask\User\Mapper;

use Kptask\Core\Mapper\UserMapperInterface;
use Kptask\User\Entity\User;
use Zend\Stdlib\ArrayObject;

/**
 * Class UserLogDbAdapter
 * @package Kptask\User\Mapper
 */
class UserLogDbAdapter implements UserMapperInterface
{

    /**
     * @inheritDoc
     */
    public function fetchAll($params = array())
    {
        // TODO: Implement fetchAll() method.
    }

    /**
     * @inheritDoc
     */
    public function fetchSingle($articleUuid)
    {
        // TODO: Implement fetchSingle() method.
    }

    /**
     * @inheritDoc
     */
    public function create(User $article)
    {
        // TODO: Implement create() method.
    }

    /**
     * @inheritDoc
     */
    public function update(User $article)
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function delete(User $article)
    {
        // TODO: Implement delete() method.
    }
}
