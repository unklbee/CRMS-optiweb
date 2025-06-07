<?php

namespace App\Models;

use CodeIgniter\Model;

class DiagnosisTemplateModel extends Model
{
    protected $table = 'diagnosis_templates';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'device_type_id', 'title', 'common_issues', 'recommended_actions', 'estimated_hours'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'device_type_id' => 'required|integer',
        'title' => 'required|min_length[3]|max_length[255]',
        'common_issues' => 'required',
        'recommended_actions' => 'required|min_length[10]'
    ];

    /**
     * Get templates with device type information
     */
    public function getTemplatesWithDeviceTypes(): array
    {
        return $this->select('
                diagnosis_templates.*,
                device_types.name as device_type_name
            ')
            ->join('device_types', 'device_types.id = diagnosis_templates.device_type_id')
            ->orderBy('device_types.name', 'ASC')
            ->orderBy('diagnosis_templates.title', 'ASC')
            ->findAll();
    }

    /**
     * Get templates by device type
     */
    public function getByDeviceType($deviceTypeId): array
    {
        $templates = $this->where('device_type_id', $deviceTypeId)
            ->orderBy('title', 'ASC')
            ->findAll();

        // Decode JSON fields
        foreach ($templates as &$template) {
            if (!empty($template['common_issues'])) {
                $template['common_issues'] = json_decode($template['common_issues'], true);
            }
        }

        return $templates;
    }

    /**
     * Save template with JSON encoding
     */
    public function saveTemplate($data): bool
    {
        // Encode common_issues if it's an array
        if (isset($data['common_issues']) && is_array($data['common_issues'])) {
            $data['common_issues'] = json_encode($data['common_issues']);
        }

        return $this->save($data);
    }

    /**
     * Update template with JSON encoding
     */
    public function updateTemplate($id, $data): bool
    {
        // Encode common_issues if it's an array
        if (isset($data['common_issues']) && is_array($data['common_issues'])) {
            $data['common_issues'] = json_encode($data['common_issues']);
        }

        return $this->update($id, $data);
    }
}