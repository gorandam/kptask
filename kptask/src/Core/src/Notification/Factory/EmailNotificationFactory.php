<?php
declare(strict_types=1);

namespace Kptask\Core\Notification\Factory;

use Kptask\Core\Notification\EmailNotification;
use Kptask\Core\Notification\NotificationInterface;
use Psr\Container\ContainerInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;

/**
 * Class EmailNotificationFactory
 * @package Kptask\Core\Notification\Factory
 */
class EmailNotificationFactory
{  /**
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
        $emailService = $this->container->get(Message::class);
        $transportService = $this->container->get(Sendmail::class);
        return new EmailNotification($emailService, $transportService);
    }
}
