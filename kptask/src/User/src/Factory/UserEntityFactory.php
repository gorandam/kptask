<?php
declare(strict_types=1);

namespace Kptask\User\Factory;

use Kptask\Core\Factory\AbstractDomainFactory;
use Kptask\User\Entity\User;

/**
 * Class UserEntityFactory
 * @package Kptask\User\Factory
 */
class UserEntityFactory extends AbstractDomainFactory
{
    /**
     * @inheritDoc
     */
    protected function getClassName(): string
    {
        return User::class;
    }
}
