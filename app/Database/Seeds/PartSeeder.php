<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PartSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'part_number' => 'LCD001',
                'name' => 'Laptop LCD Screen 15.6"',
                'description' => 'Compatible with various laptop models',
                'category' => 'Display',
                'brand' => 'Generic',
                'cost_price' => 400000,
                'selling_price' => 600000,
                'stock_quantity' => 15,
                'min_stock' => 5,
                'location' => 'A1-01',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'part_number' => 'KB001',
                'name' => 'Laptop Keyboard US Layout',
                'description' => 'Standard US QWERTY keyboard',
                'category' => 'Input',
                'brand' => 'Generic',
                'cost_price' => 80000,
                'selling_price' => 120000,
                'stock_quantity' => 25,
                'min_stock' => 10,
                'location' => 'A1-02',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'part_number' => 'BAT001',
                'name' => 'Laptop Battery 6-Cell',
                'description' => 'High capacity lithium-ion battery',
                'category' => 'Power',
                'brand' => 'Generic',
                'cost_price' => 200000,
                'selling_price' => 300000,
                'stock_quantity' => 20,
                'min_stock' => 8,
                'location' => 'A1-03',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'part_number' => 'RAM001',
                'name' => 'DDR4 8GB RAM',
                'description' => '8GB DDR4 2666MHz memory module',
                'category' => 'Memory',
                'brand' => 'Kingston',
                'cost_price' => 450000,
                'selling_price' => 600000,
                'stock_quantity' => 30,
                'min_stock' => 10,
                'location' => 'A2-01',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'part_number' => 'SSD001',
                'name' => 'SSD 256GB SATA',
                'description' => '256GB SATA SSD drive',
                'category' => 'Storage',
                'brand' => 'Samsung',
                'cost_price' => 550000,
                'selling_price' => 750000,
                'stock_quantity' => 18,
                'min_stock' => 8,
                'location' => 'A2-02',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'part_number' => 'CHG001',
                'name' => 'Universal Laptop Charger',
                'description' => 'Multi-tip universal charger',
                'category' => 'Power',
                'brand' => 'Generic',
                'cost_price' => 150000,
                'selling_price' => 250000,
                'stock_quantity' => 12,
                'min_stock' => 5,
                'location' => 'A1-04',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'part_number' => 'FAN001',
                'name' => 'Laptop Cooling Fan',
                'description' => 'CPU cooling fan assembly',
                'category' => 'Cooling',
                'brand' => 'Generic',
                'cost_price' => 100000,
                'selling_price' => 180000,
                'stock_quantity' => 8,
                'min_stock' => 5,
                'location' => 'A1-05',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'part_number' => 'HDD001',
                'name' => 'HDD 1TB 2.5"',
                'description' => '1TB 7200RPM hard drive',
                'category' => 'Storage',
                'brand' => 'Seagate',
                'cost_price' => 400000,
                'selling_price' => 600000,
                'stock_quantity' => 3,
                'min_stock' => 5,
                'location' => 'A2-03',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('parts')->insertBatch($data);
    }
}