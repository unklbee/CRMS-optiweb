<?php
// app/Models/OrderPartModel.php
namespace App\Models;

use CodeIgniter\Model;

class OrderPartModel extends Model
{
    protected $table = 'order_parts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'part_id', 'quantity', 'unit_price', 'total_price', 'notes'
    ];

    // FIX: Disable timestamps atau hanya gunakan created_at
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = ''; // Kosongkan karena tidak ada updated_at di tabel

    public function getOrderParts($orderId): array
    {
        return $this->select('order_parts.*, parts.name as part_name, parts.part_number')
            ->join('parts', 'parts.id = order_parts.part_id')
            ->where('order_id', $orderId)
            ->findAll();
    }

    /**
     * Add part to order with proper error handling
     */
    public function addPartToOrder($data): bool|int|string
    {
        // Ensure created_at is set
        if (!isset($data['created_at'])) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }

        return $this->insert($data);
    }

    /**
     * Get order parts with stock info
     */
    public function getOrderPartsWithStock($orderId): array
    {
        return $this->select('
                order_parts.*, 
                parts.name as part_name, 
                parts.part_number,
                parts.stock_quantity as current_stock,
                parts.cost_price,
                parts.selling_price
            ')
            ->join('parts', 'parts.id = order_parts.part_id')
            ->where('order_id', $orderId)
            ->findAll();
    }

    /**
     * Calculate total parts cost for an order
     */
    public function getTotalPartsCost($orderId): float
    {
        $result = $this->selectSum('total_price', 'total')
            ->where('order_id', $orderId)
            ->first();

        return (float)($result['total'] ?? 0);
    }
}