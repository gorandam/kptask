<?php
declare(strict_types=1);

namespace Kptask\Core\Validator;

/**
 * Class AbstractValidationManager
 * @package Kptask\Core\Validator
 */
abstract class AbstractValidationManager
{
    /**
     * @var array
     */
    private $valConfig;


    /**
     * AbstractValidationManager constructor.
     * @param array $valConfig
     */
    public function __construct(array $valConfig)
    {
        $this->valConfig = $valConfig;
    }

    /**
     * @param $data
     * @return AbstractValidator
     */
    public function createPipeline($data): AbstractValidator
    {
        $validatorPipe = new \SplQueue();
        foreach ($data as $field => $value) {
            if (!array_key_exists($field, $this->valConfig)) {
                continue;
            }
            $validatorClass = $this->valConfig[$field];
            $validatorPipe->enqueue($this->validatorFactory($validatorClass));
            $validatorPipe->rewind();
        }

        while ($validatorPipe->valid()) {
            $currentValidator = $validatorPipe->current();
            $validatorPipe->next();
            if ($validatorPipe->current() !== null) {
                $currentValidator->succeedWith($validatorPipe->current());
            }
        }

        return $validatorPipe->dequeue();
    }

    /**
     * @param $data
     * @return bool
     */
    public function validate($data)
    {
        $validator = $this->createPipeline($data);
        return $validator->handleValidation($data);
    }

    /**
     * @param string $validatorClass
     * @return AbstractValidator
     */
    abstract protected function validatorFactory(string $validatorClass): AbstractValidator;
}
