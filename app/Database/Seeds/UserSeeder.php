<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'username' => 'admin',
                'email' => 'admin@repairshop.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'full_name' => 'System Administrator',
                'phone' => '+62-21-1234567',
                'role' => 'admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'john',
                'email' => 'john@repairshop.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'full_name' => 'John Doe',
                'phone' => '+62-21-1234567',
                'role' => 'admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}