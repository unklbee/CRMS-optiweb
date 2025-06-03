<?php
// app/Models/CustomerModel.php
namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'full_name', 'email', 'phone', 'address', 'notes'
    ];

    protected $validationRules = [
        'full_name' => 'required|min_length[2]|max_length[100]',
        'phone' => 'required|min_length[10]|max_length[20]',
        'email' => 'permit_empty|valid_email'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getWithUser($id): array|object|null
    {
        return $this->select('customers.*, users.username')
            ->join('users', 'users.id = customers.user_id', 'left')
            ->find($id);
    }

    public function searchCustomers($keyword): array
    {
        return $this->like('full_name', $keyword)
            ->orLike('email', $keyword)
            ->orLike('phone', $keyword)
            ->findAll();
    }
}