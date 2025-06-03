<?php
// app/Models/OrderServiceModel.php
namespace App\Models;

use CodeIgniter\Model;

class OrderServiceModel extends Model
{
    protected $table = 'order_services';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 'service_id', 'quantity', 'price', 'notes'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';

    public function getOrderServices($orderId)
    {
        return $this->select('order_services.*, services.name as service_name')
            ->join('services', 'services.id = order_services.service_id')
            ->where('order_id', $orderId)
            ->findAll();
    }
}