<?php
declare(strict_types=1);

namespace Kptask\User\Validator;

use Kptask\Core\Validator\AbstractValidator;

/**
 * Class PasswordValidator
 * @package Kptask\User\Validator
 */
class PasswordValidator extends AbstractValidator
{

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function handleValidation($data)
    {
        if (empty($data['password']) || mb_strlen($data['password']) < 8) {
            throw new \Exception('First password is to short or empty!');
        }

        return $this->next($data);
    }
}
