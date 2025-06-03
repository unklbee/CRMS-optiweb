<?php
// app/Models/PartModel.php
namespace App\Models;

use CodeIgniter\Model;

class PartModel extends Model
{
    protected $table = 'parts';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'part_number', 'name', 'description', 'category', 'brand',
        'cost_price', 'selling_price', 'stock_quantity', 'min_stock',
        'location', 'status'
    ];

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

    public function getLowStockParts(): array
    {
        return $this->where('stock_quantity <=', 'min_stock', false)
            ->where('status', 'active')
            ->findAll();
    }

    public function updateStock($partId, $quantity, $operation = 'subtract'): bool
    {
        $part = $this->find($partId);
        if (!$part) return false;

        $newQuantity = $operation === 'add'
            ? $part['stock_quantity'] + $quantity
            : $part['stock_quantity'] - $quantity;

        return $this->update($partId, ['stock_quantity' => max(0, $newQuantity)]);
    }
}