<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDiagnosisTemplatesTable extends Migration
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
            'device_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'common_issues' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Array of common issues for this device type'
            ],
            'recommended_actions' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Default recommended actions for diagnosis'
            ],
            'estimated_hours' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Typical time needed for this type of diagnosis'
            ],
            'is_active' => [
                'type' => 'BOOLEAN',
                'default' => true,
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

        // Indexes
        $this->forge->addKey('device_type_id');
        $this->forge->addKey('title');
        $this->forge->addKey('is_active');

        // Foreign key
        $this->forge->addForeignKey('device_type_id', 'device_types', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('diagnosis_templates');
    }

    public function down()
    {
        $this->forge->dropTable('diagnosis_templates');
    }
}