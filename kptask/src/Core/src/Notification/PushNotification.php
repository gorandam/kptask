<?php
declare(strict_types=1);

namespace Kptask\Core\Notification;

/**
 * Class PushNotification
 * @package Kptask\Core\Notification
 */
class PushNotification implements NotificationInterface
{
    private $pushService;


    /**
     * PushNotification constructor.
     * @param null $pushService
     */
    public function __construct($pushService = null)
    {
        $this->pushService = $pushService;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        echo 'This push notification is sent';
    }
}
