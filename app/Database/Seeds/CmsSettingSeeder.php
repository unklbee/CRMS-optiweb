<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CmsSettingSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'setting_key' => 'site_name',
                'setting_value' => 'TechFix Computer Repair',
                'setting_type' => 'text',
                'description' => 'Website name',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'site_description',
                'setting_value' => 'Professional computer repair services with expert technicians and quality parts',
                'setting_type' => 'textarea',
                'description' => 'Website description',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'contact_email',
                'setting_value' => 'info@techfix.com',
                'setting_type' => 'text',
                'description' => 'Contact email address',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'contact_phone',
                'setting_value' => '+62-21-5555-0123',
                'setting_type' => 'text',
                'description' => 'Contact phone number',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'address',
                'setting_value' => 'Jl. Teknologi Raya No. 123, Jakarta Selatan 12345',
                'setting_type' => 'textarea',
                'description' => 'Business address',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'business_hours',
                'setting_value' => json_encode([
                    'monday' => '09:00-18:00',
                    'tuesday' => '09:00-18:00',
                    'wednesday' => '09:00-18:00',
                    'thursday' => '09:00-18:00',
                    'friday' => '09:00-18:00',
                    'saturday' => '09:00-15:00',
                    'sunday' => 'closed'
                ]),
                'setting_type' => 'json',
                'description' => 'Business operating hours',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'hero_title',
                'setting_value' => 'Professional Computer Repair Services',
                'setting_type' => 'text',
                'description' => 'Homepage hero title',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'hero_subtitle',
                'setting_value' => 'Fast, reliable, and affordable repair solutions for all your devices with expert technicians and quality parts',
                'setting_type' => 'textarea',
                'description' => 'Homepage hero subtitle',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('cms_settings')->insertBatch($data);
    }
}