<?php
declare(strict_types=1);

namespace Kptask\Core\Notification;

/**
 * Class SmsNotification
 * @package Kptask\Core\Notification
 */
class SmsNotification implements NotificationInterface
{
    private $smsService;


    /**
     * SmsNotification constructor.
     * @param null $smsService
     */
    public function __construct($smsService = null)
    {
        $this->smsService = $smsService;
    }

    /**
     * @inheritDoc
     */
    public function send($data)
    {
        echo 'this Sms notification is sent.';
    }
}
