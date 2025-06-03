<?php
namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Hardware Repair',
                'description' => 'Professional hardware repair services for all devices',
                'icon' => 'fas fa-wrench',
                'status' => 'active'
            ],
            [
                'name' => 'Software Installation',
                'description' => 'Operating system and software installation services',
                'icon' => 'fas fa-download',
                'status' => 'active'
            ],
            [
                'name' => 'Data Recovery',
                'description' => 'Recover lost or corrupted data from storage devices',
                'icon' => 'fas fa-database',
                'status' => 'active'
            ],
            [
                'name' => 'Virus Removal',
                'description' => 'Complete virus and malware removal services',
                'icon' => 'fas fa-shield-alt',
                'status' => 'active'
            ],
            [
                'name' => 'Performance Optimization',
                'description' => 'Optimize system performance and speed',
                'icon' => 'fas fa-tachometer-alt',
                'status' => 'active'
            ],
            [
                'name' => 'Network Setup',
                'description' => 'Network configuration and troubleshooting',
                'icon' => 'fas fa-network-wired',
                'status' => 'active'
            ]
        ];

        $this->db->table('service_categories')->insertBatch($data);
    }
}