<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRoleUserTable extends AbstractMigration
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
        if ($this->hasTable('role_user')) {
            return;
        }
    
        $table = $this->table('role_user');
        $table->addColumn('role_id', 'integer')
              ->addColumn('user_id', 'integer')
              ->addIndex(['role_id', 'user_id'], ['unique' => true])
              ->addForeignKey('role_id', 'roles', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->addForeignKey('user_id', 'users', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE'])
              ->create();
    }
}
