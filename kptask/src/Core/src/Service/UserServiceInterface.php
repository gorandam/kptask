<?php
declare(strict_types=1);

namespace Kptask\Core\Service;

/**
 * Interface UserServiceInterface
 * @package Kptask\Core\Service
 */
interface UserServiceInterface
{

    /**
     * @param $data
     * @return mixed
     */
    public function create($data);


    /**
     *
     * @return mixed
     */
    public function getAll();


    /**
     * @param $userId
     * @return mixed
     */
    public function getById($userId);


    /**
     * @param $data
     * @return mixed
     */
    public function update($data);


    /**
     * @param $data
     * @return mixed
     */
    public function delete($data);
}
