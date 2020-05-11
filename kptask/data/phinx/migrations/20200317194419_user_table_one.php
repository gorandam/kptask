<?php

use Phinx\Migration\AbstractMigration;

/**
 * Class UserTableOne
 */
class UserTableOne extends AbstractMigration
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
        $sql = "
        CREATE TABLE `user` (
  `userId` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `email_UNIQUE` (`email`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci";

        $this->query($sql);

        // insert default user
        $pass = '$2y$10$GGArVO/7.xPDg6D5Kl6GHeELUg2Dnod68ynkFaZ7R2Vfx/K1oZ96O'; // testtest
        $sql = "INSERT INTO `user` (
`email`, `password`
) VALUES (
'test@example.com', '{$pass}')";
        $this->query($sql);
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->query("DROP TABLE `user`");
    }


}
