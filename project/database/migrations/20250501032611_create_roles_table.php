<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateRolesTable extends AbstractMigration
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
        if ($this->hasTable('roles')) {
            return;
        }

        $table = $this->table('roles');
        $table->addColumn('name', 'string', ['limit' => 50, 'null' => false])
              ->addColumn('description', 'string', ['limit' => 255, 'null' => true]);
        $table->addTimestamps(); 
        $table->addIndex(['name'], ['unique' => true, 'name' => 'idx_roles_name']);
        $table->create();
    }
}
