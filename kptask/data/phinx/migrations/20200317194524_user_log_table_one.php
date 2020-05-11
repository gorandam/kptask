<?php

use Phinx\Migration\AbstractMigration;

/**
 * Class UserLogTableOne
 */
class UserLogTableOne extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
//    public function change()
//    {
//
//    }

    /**
    * Migrate Up.
    */
     public function up()
     {
         $sql = "CREATE TABLE `user_log` (
   `userLogId` INT NOT NULL AUTO_INCREMENT,
   `action` VARCHAR(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
   `log_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
   `userId` int(10) unsigned NOT NULL,
   PRIMARY KEY (`userLogId`))
   ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
 ";
         $this->query($sql);
//
         // insert default user_log
         $sql = "INSERT INTO `user_log` (
 `action`, `log_time`, `userId`
 ) VALUES (
 'register', NOW(), 1)";
         $this->query($sql);
     }
     /**
      * Migrate Down.
      */
     public function down()
     {
          $this->query("DROP TABLE `user_log`");
     }
}
