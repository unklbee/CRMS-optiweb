<?php
// app/Models/DeviceTypeModel.php
namespace App\Models;

use CodeIgniter\Model;

class DeviceTypeModel extends Model
{
    protected $table = 'device_types';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'icon', 'status'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getActiveTypes(): array
    {
        return $this->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getAllTypes(): array
    {
        return $this->orderBy('name', 'ASC')->findAll();
    }

    public function getTypeById($id): array|object|null
    {
        return $this->find($id);
    }

    public function searchTypes($keyword): array
    {
        return $this->like('name', $keyword)
            ->findAll();
    }

    public function activateType($id): bool
    {
        return $this->update($id, ['status' => 'active']);
    }

    public function deactivateType($id): bool
    {
        return $this->update($id, ['status' => 'inactive']);
    }
}