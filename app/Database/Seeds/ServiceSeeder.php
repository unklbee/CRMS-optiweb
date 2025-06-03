<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Hardware Repair (category_id: 1)
            [
                'category_id' => 1,
                'name' => 'Screen Replacement',
                'description' => 'Professional screen replacement for laptops and smartphones',
                'base_price' => 500000,
                'estimated_duration' => 120,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 1,
                'name' => 'Keyboard Repair',
                'description' => 'Laptop keyboard repair and replacement',
                'base_price' => 150000,
                'estimated_duration' => 60,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 1,
                'name' => 'Battery Replacement',
                'description' => 'Replace old or damaged batteries',
                'base_price' => 300000,
                'estimated_duration' => 45,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 1,
                'name' => 'Motherboard Repair',
                'description' => 'Complex motherboard diagnosis and repair',
                'base_price' => 800000,
                'estimated_duration' => 240,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Software Installation (category_id: 2)
            [
                'category_id' => 2,
                'name' => 'Windows Installation',
                'description' => 'Fresh Windows OS installation with drivers',
                'base_price' => 100000,
                'estimated_duration' => 90,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 2,
                'name' => 'Software Setup',
                'description' => 'Install essential software applications',
                'base_price' => 75000,
                'estimated_duration' => 60,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 2,
                'name' => 'Driver Installation',
                'description' => 'Install and update device drivers',
                'base_price' => 50000,
                'estimated_duration' => 30,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Data Recovery (category_id: 3)
            [
                'category_id' => 3,
                'name' => 'HDD Data Recovery',
                'description' => 'Recover data from damaged hard drives',
                'base_price' => 400000,
                'estimated_duration' => 480,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 3,
                'name' => 'SSD Data Recovery',
                'description' => 'Specialized SSD data recovery service',
                'base_price' => 600000,
                'estimated_duration' => 360,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 3,
                'name' => 'Phone Data Recovery',
                'description' => 'Recover data from smartphones',
                'base_price' => 350000,
                'estimated_duration' => 180,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Virus Removal (category_id: 4)
            [
                'category_id' => 4,
                'name' => 'Complete Virus Cleaning',
                'description' => 'Comprehensive virus and malware removal',
                'base_price' => 125000,
                'estimated_duration' => 90,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 4,
                'name' => 'Ransomware Removal',
                'description' => 'Specialized ransomware removal service',
                'base_price' => 200000,
                'estimated_duration' => 120,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Performance Optimization (category_id: 5)
            [
                'category_id' => 5,
                'name' => 'System Cleanup',
                'description' => 'Clean and optimize system performance',
                'base_price' => 100000,
                'estimated_duration' => 75,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 5,
                'name' => 'SSD Upgrade',
                'description' => 'Upgrade to SSD for better performance',
                'base_price' => 250000,
                'estimated_duration' => 60,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 5,
                'name' => 'RAM Upgrade',
                'description' => 'Increase system memory capacity',
                'base_price' => 200000,
                'estimated_duration' => 30,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Network Setup (category_id: 6)
            [
                'category_id' => 6,
                'name' => 'WiFi Setup',
                'description' => 'Configure wireless network connection',
                'base_price' => 75000,
                'estimated_duration' => 45,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'category_id' => 6,
                'name' => 'Network Troubleshooting',
                'description' => 'Diagnose and fix network issues',
                'base_price' => 150000,
                'estimated_duration' => 90,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('services')->insertBatch($data);
    }
}
