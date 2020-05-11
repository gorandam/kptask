<?php
declare(strict_types=1);

namespace Kptask\Core\Email;

/**
 * Interface MailerInterface
 * @package Kptask\Core\Email
 */
interface MailerInterface
{
    /**
     * @param $data
     * @return void
     */
    public function send($data);
}
