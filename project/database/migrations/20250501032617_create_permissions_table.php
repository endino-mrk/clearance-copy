<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePermissionsTable extends AbstractMigration
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
        if ($this->hasTable('permissions')) {
            return;
        }

        $table = $this->table('permissions');
        $table->addColumn('name', 'string', ['limit' => 100, 'null' => false]) // e.g., 'edit-user', 'delete-post'
              ->addColumn('description', 'string', ['limit' => 255, 'null' => true]);
        $table->addTimestamps();
        $table->addIndex(['name'], ['unique' => true, 'name' => 'idx_permissions_name']);

        $table->create();
    }
}
