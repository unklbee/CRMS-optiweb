<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CmsSettingModel;

class SettingController extends BaseController
{
    protected CmsSettingModel $settingModel;

    public function __construct()
    {
        $this->settingModel = new CmsSettingModel();
    }

    public function index(): string
    {
        $settings = $this->settingModel->orderBy('setting_key', 'ASC')->findAll();

        // Group settings by category for better organization
        $groupedSettings = [
            'general' => [],
            'contact' => [],
            'business' => [],
            'seo' => [],
            'email' => [],
            'other' => []
        ];

        foreach ($settings as $setting) {
            $key = $setting['setting_key'];

            if (in_array($key, ['site_name', 'site_description', 'site_logo', 'favicon'])) {
                $groupedSettings['general'][] = $setting;
            } elseif (in_array($key, ['contact_email', 'contact_phone', 'address'])) {
                $groupedSettings['contact'][] = $setting;
            } elseif (in_array($key, ['business_hours', 'timezone', 'currency', 'tax_rate'])) {
                $groupedSettings['business'][] = $setting;
            } elseif (in_array($key, ['meta_keywords', 'google_analytics', 'google_tag_manager'])) {
                $groupedSettings['seo'][] = $setting;
            } elseif (strpos($key, 'email_') === 0 || strpos($key, 'smtp_') === 0) {
                $groupedSettings['email'][] = $setting;
            } else {
                $groupedSettings['other'][] = $setting;
            }
        }

        $data = [
            'title' => 'Website Settings',
            'settings' => $settings,
            'grouped_settings' => $groupedSettings
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $postData = $this->request->getPost();
        $updatedCount = 0;

        foreach ($postData as $key => $value) {
            if ($key === 'csrf_token_name' || $key === csrf_token()) {
                continue;
            }

            // Determine setting type based on value
            $type = 'text';
            if (is_array($value)) {
                $type = 'json';
                $value = json_encode($value);
            } elseif (in_array(strtolower($value), ['true', 'false', '1', '0']) && strlen($value) <= 5) {
                $type = 'boolean';
            } elseif (is_numeric($value)) {
                $type = 'number';
            } elseif (strlen($value) > 255) {
                $type = 'textarea';
            }

            if ($this->settingModel->setSetting($key, $value, $type)) {
                $updatedCount++;
            }
        }

        if ($updatedCount > 0) {
            return redirect()->to('/admin/settings')->with('success', "Updated {$updatedCount} settings successfully");
        }

        return redirect()->back()->with('error', 'No settings were updated');
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Setting'
        ];

        return view('admin/settings/create', $data);
    }

    public function store()
    {
        $rules = [
            'setting_key' => 'required|min_length[2]|max_length[100]|is_unique[cms_settings.setting_key]',
            'setting_value' => 'permit_empty',
            'setting_type' => 'required|in_list[text,textarea,number,boolean,json]',
            'description' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'setting_key' => $this->request->getPost('setting_key'),
            'setting_value' => $this->request->getPost('setting_value'),
            'setting_type' => $this->request->getPost('setting_type'),
            'description' => $this->request->getPost('description'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->settingModel->insert($data)) {
            return redirect()->to('/admin/settings')->with('success', 'Setting created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create setting');
    }

    public function edit($id)
    {
        $setting = $this->settingModel->find($id);

        if (!$setting) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Setting not found');
        }

        $data = [
            'title' => 'Edit Setting',
            'setting' => $setting
        ];

        return view('admin/settings/edit', $data);
    }

    public function updateSingle($id)
    {
        $setting = $this->settingModel->find($id);

        if (!$setting) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Setting not found');
        }

        $rules = [
            'setting_value' => 'permit_empty',
            'setting_type' => 'required|in_list[text,textarea,number,boolean,json]',
            'description' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'setting_value' => $this->request->getPost('setting_value'),
            'setting_type' => $this->request->getPost('setting_type'),
            'description' => $this->request->getPost('description'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->settingModel->update($id, $data)) {
            return redirect()->to('/admin/settings')->with('success', 'Setting updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update setting');
    }

    public function delete($id)
    {
        $setting = $this->settingModel->find($id);

        if (!$setting) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Setting not found');
        }

        // Prevent deletion of core settings
        $coreSettings = [
            'site_name', 'site_description', 'contact_email', 'contact_phone',
            'address', 'business_hours'
        ];

        if (in_array($setting['setting_key'], $coreSettings)) {
            return redirect()->back()->with('error', 'Cannot delete core system settings');
        }

        if ($this->settingModel->delete($id)) {
            return redirect()->to('/admin/settings')->with('success', 'Setting deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete setting');
    }

    public function backup()
    {
        $settings = $this->settingModel->findAll();

        $backup = [
            'backup_date' => date('Y-m-d H:i:s'),
            'settings' => $settings
        ];

        $filename = 'settings_backup_' . date('Y-m-d_H-i-s') . '.json';

        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo json_encode($backup, JSON_PRETTY_PRINT);
        exit;
    }

    public function restore()
    {
        $data = [
            'title' => 'Restore Settings'
        ];

        return view('admin/settings/restore', $data);
    }

    public function processRestore()
    {
        $validationRule = [
            'backup_file' => [
                'label' => 'Backup File',
                'rules' => 'uploaded[backup_file]|ext_in[backup_file,json]'
            ]
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $file = $this->request->getFile('backup_file');

        if ($file->isValid() && !$file->hasMoved()) {
            $jsonContent = file_get_contents($file->getTempName());
            $backup = json_decode($jsonContent, true);

            if (!$backup || !isset($backup['settings'])) {
                return redirect()->back()->with('error', 'Invalid backup file format');
            }

            $restoredCount = 0;
            foreach ($backup['settings'] as $setting) {
                $existing = $this->settingModel->where('setting_key', $setting['setting_key'])->first();

                if ($existing) {
                    // Update existing setting
                    $this->settingModel->update($existing['id'], [
                        'setting_value' => $setting['setting_value'],
                        'setting_type' => $setting['setting_type'],
                        'description' => $setting['description'],
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                } else {
                    // Create new setting
                    $this->settingModel->insert([
                        'setting_key' => $setting['setting_key'],
                        'setting_value' => $setting['setting_value'],
                        'setting_type' => $setting['setting_type'],
                        'description' => $setting['description'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }
                $restoredCount++;
            }

            return redirect()->to('/admin/settings')->with('success', "Restored {$restoredCount} settings successfully");
        }

        return redirect()->back()->with('error', 'Failed to process backup file');
    }

    public function cache()
    {
        // Clear settings cache if you implement caching
        $data = [
            'title' => 'Cache Management'
        ];

        return view('admin/settings/cache', $data);
    }

    public function clearCache()
    {
        // Implementation for clearing various caches
        $cacheCleared = [];

        // Clear CodeIgniter cache
        if (is_dir(WRITEPATH . 'cache')) {
            $this->clearDirectory(WRITEPATH . 'cache');
            $cacheCleared[] = 'Application cache';
        }

        // Clear session cache if needed
        if (is_dir(WRITEPATH . 'session')) {
            $this->clearDirectory(WRITEPATH . 'session');
            $cacheCleared[] = 'Session cache';
        }

        if (!empty($cacheCleared)) {
            $message = 'Cleared: ' . implode(', ', $cacheCleared);
            return redirect()->to('/admin/settings')->with('success', $message);
        }

        return redirect()->to('/admin/settings')->with('info', 'No cache files found to clear');
    }

    private function clearDirectory($path)
    {
        if (!is_dir($path)) {
            return;
        }

        $files = glob($path . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            } elseif (is_dir($file)) {
                $this->clearDirectory($file);
                rmdir($file);
            }
        }
    }
}