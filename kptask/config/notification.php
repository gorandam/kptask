<?php

/**
 *  This is notification observers factory hash map
 */
return [
    'email' => \Kptask\Core\Notification\Factory\EmailNotificationFactory::class,
    'sms' => \Kptask\Core\Notification\Factory\SmsNotificationFactory::class,
    'push' => \Kptask\Core\Notification\Factory\PushNotificationFactory::class,
];
