<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStockMovementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'part_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'movement_type' => [
                'type' => 'ENUM',
                'constraint' => ['add', 'subtract', 'set', 'use', 'return', 'damage', 'loss'],
                'comment' => 'Type of stock movement'
            ],
            'quantity_before' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Stock quantity before movement'
            ],
            'quantity_change' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'Amount of change (positive or negative)'
            ],
            'quantity_after' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Stock quantity after movement'
            ],
            'reference_type' => [
                'type' => 'ENUM',
                'constraint' => ['manual', 'order', 'adjustment', 'initial', 'return', 'damage'],
                'default' => 'manual',
                'comment' => 'What triggered this movement'
            ],
            'reference_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID of related record (order_id, adjustment_id, etc.)'
            ],
            'unit_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Cost per unit at time of movement'
            ],
            'total_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Total cost of movement'
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Reason or notes for movement'
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'User who made the movement'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('part_id', 'parts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id');
        $this->forge->addKey(['part_id', 'created_at']);
        $this->forge->addKey(['reference_type', 'reference_id']);
        $this->forge->createTable('stock_movements');
    }

    public function down()
    {
        $this->forge->dropTable('stock_movements');
    }
}