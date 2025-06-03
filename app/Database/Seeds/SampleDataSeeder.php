<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SampleDataSeeder extends Seeder
{
    public function run()
    {
        // Sample customers
        $customers = [
            [
                'full_name' => 'Ahmad Wijaya',
                'email' => 'ahmad@example.com',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 45, Jakarta',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'full_name' => 'Siti Nurhaliza',
                'email' => 'siti@example.com',
                'phone' => '081234567891',
                'address' => 'Jl. Sudirman No. 123, Jakarta',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'full_name' => 'Budi Santoso',
                'email' => 'budi@example.com',
                'phone' => '081234567892',
                'address' => 'Jl. Gatot Subroto No. 67, Jakarta',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('customers')->insertBatch($customers);

        // Sample repair orders
        $orders = [
            [
                'order_number'         => 'ORD20250603001',
                'customer_id'          => 1, // merujuk ke customers.id = 1
                'device_type_id'       => 1, // merujuk ke device_types.id = 1 (Laptop)
                'device_brand'         => 'HP',
                'device_model'         => 'Pavilion 15',
                'device_serial'        => 'HP123456789',
                'problem_description'  => 'Layar pecah dan tidak menampilkan gambar. Perlu penggantian layar.',
                'accessories'          => 'Charger, Mouse',
                'technician_id'        => 1,              // merujuk ke users.id = 2 (John Doe)
                'priority'             => 'normal',
                'status'               => 'in_progress',
                'estimated_cost'       => 650000.00,
                'final_cost'           => 0.00,           // nanti di‐update setelah kerja selesai
                'estimated_completion' => date('Y-m-d H:i:s', strtotime('+2 days')),
                'completed_at'         => null,           // belum selesai
                'notes'                => null,
                'created_at'           => date('Y-m-d H:i:s'),       // misalnya 2025-06-03 10:00:00
                'updated_at'           => date('Y-m-d H:i:s'),       // misalnya 2025-06-03 10:00:00
            ],
            [
                'order_number'         => 'ORD20250603002',
                'customer_id'          => 1, // merujuk ke customers.id = 2
                'device_type_id'       => 1, // merujuk ke device_types.id = 2 (Desktop)
                'device_brand'         => 'Dell',
                'device_model'         => 'OptiPlex 7080',
                'device_serial'        => null,  // serial tidak diketahui
                'problem_description'  => 'Kinerja komputer sangat lambat dan sering blue screen. Perlu pembersihan virus dan optimasi sistem.',
                'accessories'          => 'Keyboard, Mouse, Kabel VGA',
                'technician_id'        => 1,              // merujuk ke users.id = 3 (Jane Smith)
                'priority'             => 'high',
                'status'               => 'completed',
                'estimated_cost'       => 225000.00,
                'final_cost'           => 225000.00,      // biaya akhir sama dengan estimasi
                'estimated_completion' => null,
                'completed_at'         => date('Y-m-d H:i:s', strtotime('-1 day')), // selesai kemarin
                'notes'                => 'Sudah melakukan scanning antivirus, membersihkan malware, dan optimasi registry.',
                'created_at'           => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at'           => date('Y-m-d H:i:s', strtotime('-1 day')),
            ],
            [
                'order_number'         => 'ORD20250603003',
                'customer_id'          => 1, // merujuk ke customers.id = 3
                'device_type_id'       => 1, // merujuk ke device_types.id = 3 (Smartphone)
                'device_brand'         => 'Samsung',
                'device_model'         => 'Galaxy S21',
                'device_serial'        => 'SM-G991BXYZ',
                'problem_description'  => 'Layar tiba‐tiba mati setelah terjatuh. Telepon masih bergetar saat ada panggilan masuk.',
                'accessories'          => null,
                'technician_id'        => 1,           // belum ditugaskan teknisi
                'priority'             => 'urgent',
                'status'               => 'received',
                'estimated_cost'       => 0.00,           // akan diisi setelah diagnosa
                'final_cost'           => 0.00,
                'estimated_completion' => null,
                'completed_at'         => null,
                'notes'                => null,
                'created_at'           => date('Y-m-d H:i:s', strtotime('-1 hour')),
                'updated_at'           => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
        ];
        $this->db->table('repair_orders')->insertBatch($orders);
    }
}