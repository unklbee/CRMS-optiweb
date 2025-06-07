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
                'constraint' => ['received', 'diagnosed', 'waiting_approval', 'approved', 'in_progress', 'waiting_parts', 'completed', 'delivered', 'cancelled'],
                'default' => 'received',
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

            // NEW: Diagnosis-specific fields
            'diagnosis_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed'],
                'default' => 'pending',
            ],
            'diagnosis_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'issues_found' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'recommended_actions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'estimated_hours' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Estimated hours for repair work (for time tracking, not cost)'
            ],
            'diagnosis_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'diagnosed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'customer_contacted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'comment' => 'Whether customer has been contacted about diagnosis'
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

        // Indexes for better performance
        $this->forge->addKey('status');
        $this->forge->addKey('diagnosis_status');
        $this->forge->addKey('priority');
        $this->forge->addKey('technician_id');
        $this->forge->addKey('diagnosed_by');
        $this->forge->addKey('created_at');
        $this->forge->addKey('diagnosis_date');

        // Foreign keys
        $this->forge->addForeignKey('customer_id', 'customers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('device_type_id', 'device_types', 'id');
        $this->forge->addForeignKey('technician_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('diagnosed_by', 'users', 'id', 'SET NULL', 'CASCADE');

        $this->forge->createTable('repair_orders');
    }

    public function down()
    {
        $this->forge->dropTable('repair_orders');
    }
}