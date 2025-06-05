<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuotationsTable extends Migration
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
            'quotation_number' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'comment' => 'Unique quotation identifier'
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'Related repair order'
            ],
            'service_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Labor/service charges'
            ],
            'parts_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Total cost of parts needed'
            ],
            'additional_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Any additional charges'
            ],
            'discount_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Discount applied'
            ],
            'discount_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
                'comment' => 'Discount percentage'
            ],
            'tax_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
                'comment' => 'Tax percentage (PPN etc)'
            ],
            'tax_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0,
                'comment' => 'Calculated tax amount'
            ],
            'total_cost' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'comment' => 'Final total after discount and tax'
            ],
            'estimated_duration' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Estimated repair duration (e.g., 2-3 days)'
            ],
            'warranty_period' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Warranty period offered'
            ],
            'terms_conditions' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Terms and conditions'
            ],
            'internal_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Internal notes not visible to customer'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'sent', 'approved', 'rejected', 'expired'],
                'default' => 'draft',
                'comment' => 'Quotation status'
            ],
            'valid_until' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Quotation expiry date'
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When quotation was sent to customer'
            ],
            'responded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When customer responded'
            ],
            'customer_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Customer feedback/notes'
            ],
            'approved_by_customer' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'comment' => 'Customer approval status'
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'User who created quotation'
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
        $this->forge->addUniqueKey('quotation_number');
        $this->forge->addForeignKey('order_id', 'repair_orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id');
        $this->forge->addKey(['status', 'valid_until']);
        $this->forge->createTable('quotations');
    }

    public function down()
    {
        $this->forge->dropTable('quotations');
    }
}
