<?php
declare(strict_types=1);

namespace Kptask\Core\Factory;

use Kptask\Core\Entity\DomainModelInterface;

/**
 * Class AbstractDomainFactory
 * @package Kptask\Core\Factory
 */
abstract class AbstractDomainFactory implements DomainFactoryInterface
{

    /**
     * @param array $data
     * @return DomainModelInterface
     */
    public function create(array $data): DomainModelInterface
    {
        $class = $this->getClassName();
        $domainModel = new $class();
        $domainModel->exchangeArray($data);
        return $domainModel;
    }

    /**
     * @return string
     */
    abstract protected function getClassName(): string;
}
