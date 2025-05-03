<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        if ($this->hasTable('users')) {
            return;
        }
        $table = $this->table('users'); 
        $table->addColumn('first_name', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('last_name', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('middle_name', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('phone_number', 'string', ['limit' => 50, 'null' => true])
              ->addColumn('email', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('password', 'string', ['limit' => 255, 'null' => false]);
        $table->addTimestamps();

        // Add indexes
        $table->addIndex(['email'], ['unique' => true, 'name' => 'idx_users_email']);
        $table->create();
    }
}
