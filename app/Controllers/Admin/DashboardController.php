<?php
// app/Controllers/Admin/DashboardController.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RepairOrderModel;
use App\Models\CustomerModel;
use App\Models\PartModel;

class DashboardController extends BaseController
{
    public function index(): string
    {
        $orderModel = new RepairOrderModel();
        $customerModel = new CustomerModel();
        $partModel = new PartModel();

        $data = [
            'stats' => [
                'total_orders' => $orderModel->countAll(),
                'pending_orders' => $orderModel->where('status', 'received')->countAllResults(),
                'in_progress_orders' => $orderModel->where('status', 'in_progress')->countAllResults(),
                'completed_orders' => $orderModel->where('status', 'completed')->countAllResults(),
                'total_customers' => $customerModel->countAll(),
                'low_stock_parts' => $partModel->where('stock_quantity <=', 'min_stock', false)->countAllResults()
            ],
            'recent_orders' => $orderModel->getOrdersWithDetails(5),
            'low_stock_parts' => $partModel->select('name, stock_quantity, min_stock')
                ->where('stock_quantity <=', 'min_stock', false)
                ->limit(5)
                ->findAll()
        ];

        return view('admin/dashboard', $data);
    }
}
