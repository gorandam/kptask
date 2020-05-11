<?php
declare(strict_types=1);

namespace Kptask\Core\Notification\Factory;

use Kptask\Core\Notification\NotificationInterface;
use Kptask\Core\Notification\SmsNotification;
use Psr\Container\ContainerInterface;

/**
 * Class SmsNotificationFactory
 * @package Kptask\Core\Notification\Factory
 */
class SmsNotificationFactory
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
        return new SmsNotification();
    }
}
