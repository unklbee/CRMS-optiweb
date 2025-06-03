<?php
// app/Models/ServiceCategoryModel.php
namespace App\Models;

use CodeIgniter\Model;

class ServiceCategoryModel extends Model
{
    protected $table = 'service_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name', 'description', 'icon', 'status'
    ];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[100]'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getActiveCategories(): array
    {
        return $this->where('status', 'active')->findAll();
    }

    public function getAllCategories(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    public function getCategoryById($id): array|object|null
    {
        return $this->find($id);
    }

    public function getCategoryWithServicesCount(): array
    {
        return $this->select('service_categories.*, COUNT(services.id) as services_count')
            ->join('services', 'services.category_id = service_categories.id', 'left')
            ->groupBy('service_categories.id')
            ->orderBy('service_categories.name', 'ASC')
            ->findAll();
    }

    public function searchCategories($keyword): array
    {
        return $this->like('name', $keyword)
            ->orLike('description', $keyword)
            ->findAll();
    }

    public function activateCategory($id): bool
    {
        return $this->update($id, [
            'status' => 'active',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function deactivateCategory($id): bool
    {
        return $this->update($id, [
            'status' => 'inactive',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}