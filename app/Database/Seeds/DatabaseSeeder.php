<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('UserSeeder');
        $this->call('DeviceTypeSeeder');
        $this->call('ServiceCategorySeeder');
        $this->call('ServiceSeeder');
        $this->call('PartSeeder');
        $this->call('CmsSettingSeeder');
        $this->call('CmsPageSeeder');
        $this->call('SampleDataSeeder');
        $this->call('DiagnosisTemplatesSeeder');
    }
}