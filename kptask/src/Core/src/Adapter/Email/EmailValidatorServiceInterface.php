<?php
declare(strict_types=1);

namespace Kptask\Core\Adapter\Email;

/**
 * Interface EmailValidatorServiceInterface
 * @package Kptask\Core\Adapter\Email
 */
interface EmailValidatorServiceInterface
{
    /**
     * @param string $email
     * @return bool
     */
    public function isValid(string $email): bool;
}
