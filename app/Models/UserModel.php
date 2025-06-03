<?php
// app/Models/UserModel.php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username', 'email', 'password', 'full_name',
        'phone', 'role', 'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected $validationRules = [
        'part_number' => 'required|min_length[2]|max_length[50]|is_unique[parts.part_number,id,{id}]',
        'name' => 'required|min_length[2]|max_length[100]',
        'cost_price' => 'required|decimal',
        'selling_price' => 'required|decimal',
        'stock_quantity' => 'required|integer',
        'min_stock' => 'required|integer'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getTechnicians(): array
    {
        return $this->where('role', 'technician')
            ->where('status', 'active')
            ->findAll();
    }
}