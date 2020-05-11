<?php
declare(strict_types=1);
namespace Kptask\User\Validator;

use Kptask\Core\Repository\UserRepositoryInterface;
use Kptask\Core\Validator\ValidatorInterface;
use Tamtamchik\SimpleFlash\Flash;
use Zend\Validator\EmailAddress;

/**
 * Class UserValidator
 * @package Kptask\User\Validator
 */
class UserValidator implements ValidatorInterface
{
    /**
     * @var Flash
     */
    private $flash;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepo;


    /**
     * UserValidator constructor.
     * @param Flash $flash
     * @param UserRepositoryInterface $userRepo
     */
    public function __construct(Flash $flash, UserRepositoryInterface $userRepo)
    {
        $this->flash = $flash;
        $this->userRepo = $userRepo;
    }


    /**
     * Validates provided data and sets errors with flash in session.
     * @param array $data
     * @return bool
     */
    public function isValid(array $data): bool
    {
        $validator = new EmailAddress();
        $valid = true;

        if (empty($data['email'])) {
            $this->flash->error('Error email.');
            $valid = false;
        }

        if (!$validator->isValid($data['email'])) {
            $this->flash->error('Email you entered is not valid.');
            $valid = false;
        }

        // password
        if (empty($data['password']) || mb_strlen($data['password']) < 8) {
            $this->flash->error('First password is to short or empty');
            $valid = false;
        }
        // password2
        if (empty($data['password2']) || mb_strlen($data['password2']) < 8) {
            $this->flash->error('Confirm password is to short or empty');
            $valid = false;
        }
        //password mismatch
        if ($data['password'] !== $data['password2']) {
            $this->flash->error('Passwords you entered do not match.');
            $valid = false;
        }
        //normalization check if user exist
        if (!empty($this->userRepo->fetchByEmail($data['email']))) {
            $this->flash->error('Email you entered already exists in system.');
            $valid = false;
        }
        return $valid;
    }

    /**
     * Method for email validating with RegEx. Used alternative solution.
     * @param string $email
     * @return void
     */
    protected function checkEmailFormat(string $email)
    {
        //return preg_match('/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD', $email));
    }
}
