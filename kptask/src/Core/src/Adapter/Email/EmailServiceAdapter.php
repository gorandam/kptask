<?php
declare(strict_types=1);

namespace Kptask\Core\Adapter\Email;

use Zend\Validator\EmailAddress;

/**
 * Class EmailServiceAdapter
 * @package Kptask\Core\Adapter\Email
 */
class EmailServiceAdapter implements EmailValidatorServiceInterface
{
    /**
     * @var EmailAddress
     */
    private $emailValidator;

    /**
     * EmailServiceAdapter constructor.
     * @param EmailAddress $emailValidator
     */
    public function __construct(EmailAddress $emailValidator)
    {
        $this->emailValidator = $emailValidator;
    }


    /**
     * @inheritDoc
     */
    public function isValid(string $email): bool
    {
        return $this->emailValidator->isValid($email);
    }
}
