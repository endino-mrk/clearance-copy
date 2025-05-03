<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreatePermissionRoleTable extends AbstractMigration
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
        if ($this->hasTable('permission_role')) {
            return;
        }

        $table = $this->table('permission_role', ['id' => false, 'primary_key' => ['permission_id', 'role_id']]);

        $table->addColumn('permission_id', 'integer', ['null' => false])
              ->addColumn('role_id', 'integer', ['null' => false]);
        $table->addForeignKey('permission_id', 'permissions', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION', 'constraint' => 'fk_permission_role_permission'])
              ->addForeignKey('role_id', 'roles', 'id', ['delete'=> 'CASCADE', 'update'=> 'NO_ACTION', 'constraint' => 'fk_permission_role_role']);

        $table->addIndex(['permission_id']);
        $table->addIndex(['role_id']);

        $table->create();
    }
}
