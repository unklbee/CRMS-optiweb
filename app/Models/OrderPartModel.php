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

    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getOrderParts($orderId): array
    {
        return $this->select('order_parts.*, parts.name as part_name, parts.part_number')
            ->join('parts', 'parts.id = order_parts.part_id')
            ->where('order_id', $orderId)
            ->findAll();
    }
}