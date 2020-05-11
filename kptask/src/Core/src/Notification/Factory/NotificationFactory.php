<?php
declare(strict_types=1);


namespace Kptask\Core\Notification\Factory;

use Psr\Container\ContainerInterface;

/**
 * Class NotificationFactory
 * @package Kptask\Core\Notification\Factory
 */
class NotificationFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * NotificationFactory constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function create(string $className)
    {
        $abstractClass = new $className($this->container);
        return $abstractClass->create();
    }
}
