<?php
// app/Models/RepairOrderModel.php
namespace App\Models;

use CodeIgniter\Model;

class RepairOrderModel extends Model
{
    protected $table = 'repair_orders';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_number', 'customer_id', 'device_type_id', 'device_brand',
        'device_model', 'device_serial', 'problem_description', 'accessories',
        'technician_id', 'priority', 'status', 'estimated_cost', 'final_cost',
        'estimated_completion', 'completed_at', 'notes'
    ];

    protected $validationRules = [
        'customer_id' => 'required|integer',
        'device_type_id' => 'required|integer',
        'problem_description' => 'required|min_length[10]'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateOrderNumber'];

    protected function generateOrderNumber(array $data)
    {
        if (!isset($data['data']['order_number'])) {
            $data['data']['order_number'] = 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    public function getOrdersWithDetails($limit = null, $offset = null): array
    {
        $query = $this->select('
                repair_orders.*,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                device_types.name as device_type_name,
                users.full_name as technician_name
            ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = repair_orders.technician_id', 'left')
            ->orderBy('repair_orders.created_at', 'DESC');

        if ($limit) {
            $query->limit($limit, $offset);
        }

        return $query->findAll();
    }

    public function getOrderByNumber($orderNumber)
    {
        return $this->select('
                repair_orders.*,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                customers.email as customer_email,
                device_types.name as device_type_name,
                users.full_name as technician_name
            ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = repair_orders.technician_id', 'left')
            ->where('repair_orders.order_number', $orderNumber)
            ->first();
    }

    public function getOrdersByStatus($status)
    {
        return $this->getOrdersWithDetails()
            ->where('repair_orders.status', $status);
    }

    public function getTechnicianWorkload($technicianId)
    {
        return $this->where('technician_id', $technicianId)
            ->whereIn('status', ['received', 'diagnosed', 'in_progress', 'waiting_parts'])
            ->countAllResults();
    }
}
