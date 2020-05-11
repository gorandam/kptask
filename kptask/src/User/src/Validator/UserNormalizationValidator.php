<?php
declare(strict_types=1);

namespace Kptask\User\Validator;

use Kptask\Core\Repository\UserRepositoryInterface;
use Kptask\Core\Validator\AbstractValidator;

/**
 * Class UserNormalizationValidator
 * @package Kptask\User\Validator
 */
class UserNormalizationValidator extends AbstractValidator
{
    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    /**
     * UserNormalizationValidator constructor.
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }


    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function handleValidation($data)
    {
        if (!empty($this->userRepository->fetchByEmail($data['email']))) {
            throw new \Exception('Email you entered already exists in system.');
        }

        return $this->next($data);
    }
}
