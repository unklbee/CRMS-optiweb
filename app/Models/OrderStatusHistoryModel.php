<?php
namespace App\Models;

use CodeIgniter\Model;

class OrderStatusHistoryModel extends Model
{
    protected $table = 'order_status_history';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'old_status', 'new_status', 'notes', 'changed_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getOrderHistory($orderId): array
    {
        return $this->select('order_status_history.*, users.full_name as changed_by_name')
            ->join('users', 'users.id = order_status_history.changed_by')
            ->where('order_id', $orderId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function addStatusChange($orderId, $oldStatus, $newStatus, $notes = null, $changedBy = null): bool|int|string
    {
        return $this->insert([
            'order_id' => $orderId,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'notes' => $notes,
            'changed_by' => $changedBy,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getLatestStatusChange($orderId): object|array|null
    {
        return $this->where('order_id', $orderId)
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function getStatusChangesCount($orderId): int|string
    {
        return $this->where('order_id', $orderId)->countAllResults();
    }

}