<?php
// app/Models/ServiceModel.php
namespace App\Models;

use CodeIgniter\Model;

class ServiceModel extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'category_id', 'name', 'description', 'base_price',
        'estimated_duration', 'status'
    ];

    protected $validationRules = [
        'category_id' => 'required|integer',
        'name' => 'required|min_length[2]|max_length[100]',
        'base_price' => 'required|decimal',
        'estimated_duration' => 'required|integer'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getServicesWithCategory(): array
    {
        return $this->select('services.*, service_categories.name as category_name')
            ->join('service_categories', 'service_categories.id = services.category_id')
            ->where('services.status', 'active')
            ->findAll();
    }

    public function getServicesByCategory($categoryId): array
    {
        return $this->where('category_id', $categoryId)
            ->where('status', 'active')
            ->findAll();
    }

    public function getActiveServices(): array
    {
        return $this->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}