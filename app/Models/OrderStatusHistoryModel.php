<?php
// app/Models/OrderStatusHistoryModel.php
namespace App\Models;

use CodeIgniter\Model;

class OrderStatusHistoryModel extends Model
{
    protected $table = 'order_status_history';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'old_status', 'new_status', 'notes', 'changed_by'
    ];

    // FIX: Hanya gunakan created_at, tidak ada updated_at
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = ''; // Kosongkan karena tidak ada updated_at di tabel

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
            'changed_by' => $changedBy ?: session()->get('user_id'),
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

    /**
     * Get status history with timeline formatting
     */
    public function getStatusTimeline($orderId): array
    {
        $history = $this->getOrderHistory($orderId);

        $timeline = [];
        foreach ($history as $entry) {
            $timeline[] = [
                'id' => $entry['id'],
                'old_status' => $entry['old_status'],
                'new_status' => $entry['new_status'],
                'notes' => $entry['notes'],
                'changed_by' => $entry['changed_by_name'],
                'created_at' => $entry['created_at'],
                'time_ago' => time_ago($entry['created_at']),
                'formatted_date' => date('M d, Y H:i', strtotime($entry['created_at']))
            ];
        }

        return $timeline;
    }

    /**
     * Get status change statistics
     */
    public function getStatusStats($days = 30): array
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->select('
                new_status as status,
                COUNT(*) as count
            ')
            ->where('created_at >=', $startDate)
            ->groupBy('new_status')
            ->orderBy('count', 'DESC')
            ->findAll();
    }
}