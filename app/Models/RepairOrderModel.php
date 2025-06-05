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
        'estimated_completion', 'completed_at', 'notes', 'diagnosis_notes', 'issues_found', 'recommended_actions', 'estimated_hours',
        'diagnosis_date', 'diagnosed_by', 'diagnosis_status', 'customer_contacted'
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

    /**
     * Get orders that need diagnosis
     */
    public function getOrdersNeedingDiagnosis($limit = null): array
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
            ->where('repair_orders.status', 'received')
            ->orWhere('repair_orders.diagnosis_status', 'pending')
            ->orWhere('repair_orders.diagnosis_status', 'in_progress')
            ->orderBy('repair_orders.priority', 'DESC')
            ->orderBy('repair_orders.created_at', 'ASC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->findAll();
    }

    /**
     * Update diagnosis information
     */
    // REPLACE method updateDiagnosis() di RepairOrderModel dengan yang ini:

    /**
     * Update diagnosis information
     */
    public function updateDiagnosis($orderId, $diagnosisData): bool
    {
        // Prepare data for update, removing any empty values
        $updateData = [];

        if (!empty($diagnosisData['diagnosis_notes'])) {
            $updateData['diagnosis_notes'] = $diagnosisData['diagnosis_notes'];
        }

        if (isset($diagnosisData['issues_found'])) {
            $updateData['issues_found'] = is_array($diagnosisData['issues_found'])
                ? json_encode($diagnosisData['issues_found'])
                : $diagnosisData['issues_found'];
        }

        if (!empty($diagnosisData['recommended_actions'])) {
            $updateData['recommended_actions'] = $diagnosisData['recommended_actions'];
        }

        if (isset($diagnosisData['estimated_hours'])) {
            $updateData['estimated_hours'] = $diagnosisData['estimated_hours'];
        }

        if (isset($diagnosisData['estimated_cost'])) {
            $updateData['estimated_cost'] = $diagnosisData['estimated_cost'];
        }

        // Always set these fields
        $updateData['diagnosis_date'] = date('Y-m-d H:i:s');
        $updateData['diagnosed_by'] = session()->get('user_id');
        $updateData['diagnosis_status'] = 'completed';
        $updateData['updated_at'] = date('Y-m-d H:i:s');

        // Make sure we have at least some data to update
        if (empty($updateData)) {
            return false;
        }

        return $this->update($orderId, $updateData);
    }

    /**
     * Get diagnosis summary for dashboard
     */
    public function getDiagnosisSummary(): array
    {
        return [
            'pending_diagnosis' => $this->where('diagnosis_status', 'pending')->countAllResults(),
            'in_progress_diagnosis' => $this->where('diagnosis_status', 'in_progress')->countAllResults(),
            'completed_diagnosis' => $this->where('diagnosis_status', 'completed')->countAllResults(),
            'awaiting_approval' => $this->where('status', 'waiting_approval')->countAllResults()
        ];
    }

    /**
     * Get detailed diagnosis info for an order
     */
    public function getDiagnosisDetails($orderId): array
    {
        $order = $this->select('
            repair_orders.*,
            customers.full_name as customer_name,
            customers.phone as customer_phone,
            customers.email as customer_email,
            device_types.name as device_type_name,
            diagnosed_user.full_name as diagnosed_by_name
        ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users as diagnosed_user', 'diagnosed_user.id = repair_orders.diagnosed_by', 'left')
            ->where('repair_orders.id', $orderId)
            ->first();

        if ($order && $order['issues_found']) {
            $order['issues_found'] = json_decode($order['issues_found'], true);
        }

        return $order ?: [];
    }
}
