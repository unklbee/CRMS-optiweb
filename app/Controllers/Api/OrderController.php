<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\RepairOrderModel;
use CodeIgniter\HTTP\ResponseInterface;

class OrderController extends BaseController
{
    protected RepairOrderModel $orderModel;

    public function __construct()
    {
        $this->orderModel = new RepairOrderModel();
    }

    public function create()
    {
        // Same logic as Frontend OrderController::store but return JSON
        $rules = [
            'customer_name' => 'required|min_length[2]|max_length[100]',
            'customer_phone' => 'required|valid_phone',
            'customer_email' => 'permit_empty|valid_email',
            'device_type_id' => 'required|integer',
            'device_brand' => 'required|min_length[2]',
            'device_model' => 'required|min_length[2]',
            'problem_description' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Process order creation (same logic as above)
        // Return JSON response

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Order created successfully',
            'order_number' => $orderNumber
        ]);
    }

    public function show($orderNumber)
    {
        $order = $this->orderModel->getOrderByNumber($orderNumber);

        if (!$order) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Order not found'
            ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->response->setJSON([
            'success' => true,
            'order' => $order
        ]);
    }
}