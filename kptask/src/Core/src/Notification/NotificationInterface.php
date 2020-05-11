<?php
declare(strict_types=1);

namespace Kptask\Core\Notification;

/**
 * Interface NotificationInterface
 * @package Kptask\Core\Notification
 */
interface NotificationInterface
{
    /**
     * @param $data
     * @return mixed
     */
    public function send($data);
}
