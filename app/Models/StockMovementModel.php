<?php
// app/Models/StockMovementModel.php
namespace App\Models;

use CodeIgniter\Model;

class StockMovementModel extends Model
{
    protected $table = 'stock_movements';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'part_id', 'movement_type', 'quantity_before', 'quantity_change',
        'quantity_after', 'reference_type', 'reference_id', 'unit_cost',
        'total_cost', 'notes', 'created_by'
    ];

    // FIX: Hanya gunakan created_at, tidak ada updated_at
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = ''; // Kosongkan karena tidak ada updated_at di tabel

    /**
     * Get movement history for a specific part
     */
    public function getPartMovementHistory($partId, $limit = null): array
    {
        $query = $this->select('stock_movements.*, users.full_name as created_by_name, parts.name as part_name')
            ->join('users', 'users.id = stock_movements.created_by', 'left')
            ->join('parts', 'parts.id = stock_movements.part_id')
            ->where('stock_movements.part_id', $partId)
            ->orderBy('stock_movements.created_at', 'DESC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->findAll();
    }

    /**
     * Get movements by reference (e.g., all movements for an order)
     */
    public function getMovementsByReference($referenceType, $referenceId): array
    {
        return $this->select('stock_movements.*, users.full_name as created_by_name, parts.name as part_name, parts.part_number')
            ->join('users', 'users.id = stock_movements.created_by', 'left')
            ->join('parts', 'parts.id = stock_movements.part_id')
            ->where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Record stock movement
     */
    public function recordMovement(array $data): bool|int|string
    {
        // Validate required fields
        $required = ['part_id', 'movement_type', 'quantity_before', 'quantity_change', 'quantity_after', 'created_by'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Missing required field: {$field}");
            }
        }

        // Set defaults
        $data['unit_cost'] = $data['unit_cost'] ?? 0;
        $data['total_cost'] = $data['total_cost'] ?? ($data['unit_cost'] * abs($data['quantity_change']));
        $data['reference_type'] = $data['reference_type'] ?? 'manual';
        $data['created_at'] = date('Y-m-d H:i:s');

        return $this->insert($data);
    }

    /**
     * Get stock summary for all parts
     */
    public function getStockSummary(): array
    {
        return $this->select('
                parts.id,
                parts.name,
                parts.part_number,
                parts.stock_quantity,
                parts.min_stock,
                SUM(CASE WHEN stock_movements.movement_type IN ("add", "return") THEN stock_movements.quantity_change ELSE 0 END) as total_added,
                SUM(CASE WHEN stock_movements.movement_type IN ("subtract", "use") THEN stock_movements.quantity_change ELSE 0 END) as total_used,
                COUNT(stock_movements.id) as total_movements
            ')
            ->join('parts', 'parts.id = stock_movements.part_id', 'right')
            ->groupBy('parts.id')
            ->findAll();
    }

    /**
     * Get recent movements across all parts
     */
    public function getRecentMovements($limit = 50): array
    {
        return $this->select('stock_movements.*, users.full_name as created_by_name, parts.name as part_name, parts.part_number')
            ->join('users', 'users.id = stock_movements.created_by', 'left')
            ->join('parts', 'parts.id = stock_movements.part_id')
            ->orderBy('stock_movements.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get movement statistics for dashboard
     */
    public function getMovementStats($days = 30): array
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->select('
                movement_type,
                COUNT(*) as count,
                SUM(ABS(quantity_change)) as total_quantity,
                SUM(total_cost) as total_cost
            ')
            ->where('created_at >=', $startDate)
            ->groupBy('movement_type')
            ->findAll();
    }

    /**
     * Get parts usage frequency
     */
    public function getPartUsageFrequency($limit = 10): array
    {
        return $this->select('
                parts.id,
                parts.name,
                parts.part_number,
                COUNT(stock_movements.id) as usage_count,
                SUM(CASE WHEN stock_movements.movement_type IN ("subtract", "use") THEN ABS(stock_movements.quantity_change) ELSE 0 END) as total_used
            ')
            ->join('parts', 'parts.id = stock_movements.part_id')
            ->where('stock_movements.movement_type IN', ['subtract', 'use'])
            ->groupBy('parts.id')
            ->orderBy('usage_count', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get daily movement summary
     */
    public function getDailyMovementSummary($days = 7): array
    {
        $startDate = date('Y-m-d', strtotime("-{$days} days"));

        return $this->select('
                DATE(created_at) as date,
                movement_type,
                COUNT(*) as count,
                SUM(ABS(quantity_change)) as total_quantity
            ')
            ->where('DATE(created_at) >=', $startDate)
            ->groupBy(['DATE(created_at)', 'movement_type'])
            ->orderBy('date', 'DESC')
            ->findAll();
    }
}