<?php
declare(strict_types=1);

namespace Kptask\Core\Notification;

/**
 * Interface NotificationManagerInterface
 * @package Kptask\Core\Notification
 */
interface NotificationManagerInterface
{
    /**
     * @param $observable
     * @return mixed
     */
    public function attach($observable);

    /**
     * @param string $key
     * @return mixed
     */
    public function detach(string $key);

    /**
     * @param $data
     * @return mixed
     */
    public function notify($data);
}
