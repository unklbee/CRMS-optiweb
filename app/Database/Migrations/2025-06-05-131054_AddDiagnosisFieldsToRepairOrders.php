<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDiagnosisFieldsToRepairOrders extends Migration
{
    public function up()
    {
        $fields = [
            'diagnosis_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Detailed diagnosis findings by technician'
            ],
            'issues_found' => [
                'type' => 'TEXT', // Changed from JSON to TEXT for better compatibility
                'null' => true,
                'comment' => 'JSON string of issues found during diagnosis'
            ],
            'recommended_actions' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Recommended repair actions'
            ],
            'estimated_hours' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'default' => null,
                'comment' => 'Estimated repair time in hours'
            ],
            'diagnosis_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When diagnosis was completed'
            ],
            'diagnosed_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID who performed diagnosis'
            ],
            'diagnosis_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed'],
                'default' => 'pending',
                'null' => false, // Make sure it's not null
                'comment' => 'Status of diagnosis process'
            ],
            'customer_contacted' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false, // Make sure it's not null
                'comment' => 'Whether customer has been contacted with diagnosis'
            ]
        ];

        $this->forge->addColumn('repair_orders', $fields);

        // Update existing orders to have default diagnosis_status
        $this->db->query("UPDATE repair_orders SET diagnosis_status = 'pending' WHERE diagnosis_status IS NULL");
        $this->db->query("UPDATE repair_orders SET customer_contacted = 0 WHERE customer_contacted IS NULL");
    }

    public function down()
    {
        $fields = [
            'diagnosis_notes',
            'issues_found',
            'recommended_actions',
            'estimated_hours',
            'diagnosis_date',
            'diagnosed_by',
            'diagnosis_status',
            'customer_contacted'
        ];

        $this->forge->dropColumn('repair_orders', $fields);
    }
}
