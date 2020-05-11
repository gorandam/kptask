<?php
declare(strict_types=1);

namespace Kptask\Core\Factory;

use Kptask\Core\Entity\DomainModelInterface;

/**
 * Interface DomainFactoryInterface
 * @package Kptask\Core\Factory
 */
interface DomainFactoryInterface
{
    /**
     * @param array $data
     * @return DomainModelInterface
     */
    public function create(array $data): DomainModelInterface;
}
