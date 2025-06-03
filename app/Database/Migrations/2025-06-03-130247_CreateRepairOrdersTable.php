<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRepairOrdersTable extends Migration
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
            'order_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
            ],
            'customer_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'device_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'device_brand' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'device_model' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'device_serial' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'problem_description' => [
                'type' => 'TEXT',
            ],
            'accessories' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'technician_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'priority' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'normal', 'high', 'urgent'],
                'default' => 'normal',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['received', 'diagnosed', 'waiting_approval', 'in_progress', 'waiting_parts', 'completed', 'delivered', 'cancelled'],
                'default' => 'received',
            ],
            'estimated_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'final_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
            ],
            'estimated_completion' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('order_number');
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('device_type_id', 'device_types', 'id');
        $this->forge->addForeignKey('technician_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('repair_orders');
    }

    public function down()
    {
        $this->forge->dropTable('repair_orders');
    }
}