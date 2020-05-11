<?php
declare(strict_types = 1);
namespace Kptask\User\Mapper;

use Kptask\Core\Mapper\UserMapperInterface;
use Kptask\User\Entity\User;

/**
 * Class UserDbAdapter
 * @package Kptask\User\Mapper
 */
class UserDbAdapter implements UserMapperInterface
{
    /**
     * @var \PDO
     */
    private $driver;


    private $tableName = 'user';


    /**
     * UserDbAdapter constructor.
     * @param \PDO $pdo
     */
    public function __construct(\PDO $pdo)
    {
        $this->driver = $pdo;
    }

    /**
     * Fetches a list of User models.
     *
     * @param array $params
     *
     * @param null $limit
     * @return array
     */
    public function fetchAll($params = array(), $limit = null)
    {
        $sql = "SELECT * FROM `{$this->tableName}` ";
        $i = 0;
        foreach ($params as $name => $value) {
            if ($i === 0) {
                $sql .= " WHERE `{$name}` = '{$value}' ";
            } else {
                $sql .= " AND `{$name}` = '{$value}' ";
            }
            $i++;
        }
        if (isset($limit)) {
            $sql .= " LIMIT " . (int) $limit;
        }
        $stmt = $this->driver->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_CLASS, User::class);
    }

    /**
     * @param string $userId
     * @return User
     */
    public function fetchSingle($userId)
    {
        $sql = "SELECT * FROM `{$this->tableName}` WHERE `userId` = :userId";
        $stmt = $this->driver->prepare($sql);
        $stmt->execute([':userId' => (int) $userId]);

        return $stmt->fetchObject(User::class);
    }

    /**
     * Tries to create user and throws exception if failed.
     *
     * @param User $userModel
     * @return string
     * @throws \Exception
     */
    public function create(User $userModel)
    {
        $names = array();
        $values = array();
        foreach ($userModel->getArrayCopy() as $attribute => $value) {
            $names[] = $attribute;
            $values[] = $value;
        }
        unset($names[0]);
        unset($values[0]);

        $names = implode("`,`", $names);
        $values = implode("','", $values);

        $this->driver->beginTransaction();
        $sql = "INSERT INTO `{$this->tableName}` (`{$names}`) VALUES ('{$values}')";

        $result = $this->driver->prepare($sql)->execute();
        if (!$result) {
            throw new \Exception($this->driver->errorInfo()[0]);
        }
        $lastInsertedId = $this->driver->lastInsertId();

        if ($lastInsertedId) {
            //TODO: this need to be refactored to its own mapper
            $sql = "INSERT INTO `user_log` ( `action`, `log_time`, `userId`) VALUES ('register', NOW(), '{$lastInsertedId}');";
            $result = $this->driver->prepare($sql)->execute();
            if (!$result) {
                throw new \Exception($this->driver->errorInfo()[0]);
            }
        }
        $this->driver->commit();

        return $lastInsertedId;
    }

    /**
     * Updates User model.
     *
     * @param User $userModel
     * @return void
     * @throws \Exception
     */
    public function update(User $userModel)
    {
        $data = $userModel->getArrayCopy();
        $userId = array_shift($data);
        $values = array();
        foreach ($data as $attribute => $value) {
            $attribute = ':' . $attribute;
            $values[$attribute] = $value;
        }

        $this->driver->beginTransaction();
        $sql = "UPDATE `{$this->tableName}` SET  `email` = :email, `password` = :password WHERE `userId` = '{$userId}'";

        $result = $this->driver->prepare($sql)->execute($values);
        if (!$result) {
            throw new \Exception($this->driver->errorInfo());
        }

        $sql = "UPDATE `user_log` SET `action` = 'updated', `log_time` = NOW() WHERE `userId` = '{$userId}'";
        $result = $this->driver->prepare($sql)->execute();
        if (!$result) {
            throw new \Exception($this->driver->errorInfo()[0]);
        }
        $this->driver->commit();
    }

    /**
     * Deletes a single User entity.
     *
     * @param User $userModel
     * @return bool
     * @throws \Exception
     */
    public function delete(User $userModel)
    {
        $this->driver->beginTransaction();
        $sql = "DELETE FROM `{$this->tableName}` WHERE `userId` = {$userModel->getUserId()}";
        $result = $this->driver->prepare($sql)->execute();
        if (!$result) {
            throw new \Exception($this->driver->errorInfo());
        }

        $sql = "DELETE FROM `user_log` WHERE `userId` = {$userModel->getUserId()}";
        $result = $this->driver->prepare($sql)->execute();
        if (!$result) {
            throw new \Exception($this->driver->errorInfo()[0]);
        }
        $this->driver->commit();
    }
}
