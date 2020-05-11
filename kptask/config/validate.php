<?php
declare(strict_types=1);

return [
    'email' => \Kptask\User\Validator\EmailValidator::class,
    'password' => \Kptask\User\Validator\PasswordValidator::class,
    'password2' => \Kptask\User\Validator\PasswordRepeatValidator::class,
    'register' => \Kptask\User\Validator\UserNormalizationValidator::class,
];
