<?php
declare(strict_types=1);

namespace Kptask\Core\Notification;

use Kptask\Core\Notification\Factory\NotificationFactory;

/**
 * Class NotificationManager
 * @package Kptask\Core\Notification
 */
class NotificationManager implements NotificationManagerInterface
{

    /**
     * @var array
     */
    private $notifiers;

    /**
     * @var array
     */
    private $notifierConfig;

    /**
     * @var NotificationFactory
     */
    private $notificationFactory;


    /**
     * NotificationManager constructor.
     * @param array $notifierConfig
     * @param NotificationFactory $notificationFactory
     * @throws \Exception
     */
    public function __construct(array $notifierConfig, NotificationFactory $notificationFactory)
    {
        $this->notifierConfig = $notifierConfig;
        $this->notificationFactory = $notificationFactory;
        $this->prepareNotifiers();
    }

    /**
     * @param $notifiers
     * @return mixed
     * @throws \Exception
     */
    public function attach($notifiers)
    {
        if (is_array($notifiers)) {
            return $this->attachNotifiers($notifiers);
        }

        $this->notifiers[] = $notifiers;

        return $this;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function detach(string $key)
    {
        unset($this->notifiers[0]);
    }

    /**
     * @param $data
     */
    public function dispatch($data)
    {
        $this->notify($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    public function notify($data)
    {
        foreach ($this->notifiers as $notifier) {
            $notifier->send($data);
        }
    }

    /**
     * @param $notifiers
     * @throws \Exception
     */
    protected function attachNotifiers($notifiers)
    {
        ;
        foreach ($notifiers as $notifier) {
            if (! $notifier instanceof NotificationInterface) {
                throw new \Exception('Provided class is not Notification type Class!');
            }
            $this->notifiers[] = $notifier;
        }
    }

    /**
     * @throws \Exception
     */
    protected function prepareNotifiers()
    {
        $notifiers = [];
        foreach ($this->notifierConfig as $key => $notifierClass) {
            $notifiers[] = $this->notificationFactory->create($notifierClass);
        }
        $this->attach($notifiers);
    }
}
