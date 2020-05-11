<?php
declare(strict_types=1);

namespace Kptask\User\Validator;

use Kptask\Core\Validator\AbstractValidator;

/**
 * Class PasswordRepeatValidator
 * @package Kptask\User\Validator
 */
class PasswordRepeatValidator extends AbstractValidator
{

    /**
     * @inheritDoc
     */
    public function handleValidation($data)
    {
        // password2
        if (empty($data['password2']) || mb_strlen($data['password2']) < 8) {
            throw new \Exception('Password2 is to short or empty!');
        }

        if ($data['password'] !== $data['password2']) {
            throw new \Exception('Passwords dont match!');
        }

        return $this->next($data);
    }
}
