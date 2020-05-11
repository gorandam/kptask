<?php
declare(strict_types=1);

namespace Kptask\Core\Notification\Factory;

use Kptask\Core\Notification\NotificationInterface;
use Kptask\Core\Notification\PushNotification;
use Psr\Container\ContainerInterface;

/**
 * Class PushNotificationFactory
 * @package Kptask\Core\Notification\Factory
 */
class PushNotificationFactory
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
     * @return NotificationInterface
     */
    public function create(): NotificationInterface
    {
        return new PushNotification();
    }
}
