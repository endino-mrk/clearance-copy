<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateFinesTable extends AbstractMigration
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
        if ($this->hasTable('fines')) {
            return;
        }

        $table = $this->table('fines'); // Phinx automatically adds an 'id' primary key column
        $table->addColumn('fine_category_id', 'integer', ['null' => false])
              ->addColumn('resident_id', 'integer', ['null' => false])
              ->addColumn('room_id', 'integer', ['null' => false])
              ->addColumn('fine_amount', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => false])
              ->addColumn('fine_date', 'datetime', ['null' => false])
              ->addColumn('fine_status', 'string', ['limit' => 255, 'null' => false])
              ->addColumn('payment_date', 'datetime', ['null' => true])
              ->addColumn('payment_method', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('payment_status', 'string', ['limit' => 255, 'null' => true])
              ->addColumn('payment_amount', 'decimal', ['precision' => 10, 'scale' => 2, 'null' => true]);

        $table->addTimestamps();

        // Add indexes for commonly queried columns
        $table->addIndex(['resident_id'], ['name' => 'idx_fines_resident_id']);
        $table->addIndex(['room_id'], ['name' => 'idx_fines_room_id']);
        $table->addIndex(['fine_status'], ['name' => 'idx_fines_status']);
        $table->addIndex(['payment_status'], ['name' => 'idx_fines_payment_status']);

        // Add foreign keys if tables exist
        if ($this->hasTable('residents')) {
            $table->addForeignKey('resident_id', 'residents', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        }
        
        if ($this->hasTable('rooms')) {
            $table->addForeignKey('room_id', 'rooms', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        }
        
        if ($this->hasTable('fine_categories')) {
            $table->addForeignKey('fine_category_id', 'fine_categories', 'id', ['delete' => 'CASCADE', 'update' => 'CASCADE']);
        }
        $table->create();
    }
}
