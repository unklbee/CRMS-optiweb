<?php
namespace App\Models;

use CodeIgniter\Model;

class CmsSettingModel extends Model
{
    protected $table = 'cms_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'setting_key', 'setting_value', 'setting_type', 'description'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getSetting($key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        if (!$setting) return $default;

        // Parse JSON settings
        if ($setting['setting_type'] === 'json') {
            return json_decode($setting['setting_value'], true);
        }

        // Parse boolean settings
        if ($setting['setting_type'] === 'boolean') {
            return filter_var($setting['setting_value'], FILTER_VALIDATE_BOOLEAN);
        }

        return $setting['setting_value'];
    }

    public function setSetting($key, $value, $type = 'text')
    {
        // Convert value based on type
        if ($type === 'json') {
            $value = json_encode($value);
        } elseif ($type === 'boolean') {
            $value = $value ? '1' : '0';
        }

        $existing = $this->where('setting_key', $key)->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'setting_value' => $value,
                'setting_type' => $type
            ]);
        } else {
            return $this->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_type' => $type
            ]);
        }
    }

    public function getAllSettings()
    {
        $settings = $this->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $value = $setting['setting_value'];

            // Parse based on type
            if ($setting['setting_type'] === 'json') {
                $value = json_decode($value, true);
            } elseif ($setting['setting_type'] === 'boolean') {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
            }

            $result[$setting['setting_key']] = $value;
        }

        return $result;
    }
}