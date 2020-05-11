<?php
declare(strict_types=1);

namespace Kptask\Core\Validator;

/**
 * Class AbstractValidator
 * @package Kptask\Core\Validator
 */
abstract class AbstractValidator
{

    /**
     * @var AbstractValidator
     */
    private $successor;


    /**
     * @param AbstractValidator $successor
     */
    public function succeedWith(AbstractValidator $successor)
    {
        $this->successor = $successor;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function next(array $data)
    {
        if ($this->successor) {
            $this->successor->handleValidation($data);
        }

        return true;
    }

    /**
     * @param $data
     * @return mixed
     */
    abstract public function handleValidation($data);
}
