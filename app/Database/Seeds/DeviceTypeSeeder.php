<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DeviceTypeSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Laptop', 'icon' => 'fas fa-laptop', 'status' => 'active'],
            ['name' => 'Desktop PC', 'icon' => 'fas fa-desktop', 'status' => 'active'],
            ['name' => 'Smartphone', 'icon' => 'fas fa-mobile-alt', 'status' => 'active'],
            ['name' => 'Tablet', 'icon' => 'fas fa-tablet-alt', 'status' => 'active'],
            ['name' => 'Printer', 'icon' => 'fas fa-print', 'status' => 'active'],
            ['name' => 'Monitor', 'icon' => 'fas fa-tv', 'status' => 'active'],
            ['name' => 'Gaming Console', 'icon' => 'fas fa-gamepad', 'status' => 'active'],
        ];

        $this->db->table('device_types')->insertBatch($data);
    }
}