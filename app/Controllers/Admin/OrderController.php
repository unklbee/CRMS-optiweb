<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RepairOrderModel;
use App\Models\CustomerModel;
use App\Models\DeviceTypeModel;
use App\Models\UserModel;
use App\Models\OrderStatusHistoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class OrderController extends BaseController
{
    protected RepairOrderModel $orderModel;
    protected CustomerModel $customerModel;
    protected DeviceTypeModel $deviceTypeModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->orderModel = new RepairOrderModel();
        $this->customerModel = new CustomerModel();
        $this->deviceTypeModel = new DeviceTypeModel();
        $this->userModel = new UserModel();
    }

    public function index(): string
    {
        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $builder = $this->orderModel->select('
                repair_orders.*,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                device_types.name as device_type_name,
                users.full_name as technician_name
            ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = repair_orders.technician_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('repair_orders.order_number', $search)
                ->orLike('customers.full_name', $search)
                ->orLike('customers.phone', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('repair_orders.status', $status);
        }

        $orders = $builder->orderBy('repair_orders.created_at', 'DESC')
            ->paginate($perPage);

        $data = [
            'title' => 'Repair Orders',
            'orders' => $orders,
            'pager' => $this->orderModel->pager,
            'search' => $search,
            'status' => $status,
            'statuses' => [
                'received' => 'Received',
                'diagnosed' => 'Diagnosed',
                'waiting_approval' => 'Waiting Approval',
                'in_progress' => 'In Progress',
                'waiting_parts' => 'Waiting Parts',
                'completed' => 'Completed',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled'
            ]
        ];

        return view('admin/orders/index', $data);
    }

    public function show($id): string
    {
        $order = $this->orderModel->select('
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
            ->where('repair_orders.id', $id)
            ->first();

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Get status history if OrderStatusHistoryModel exists
        $statusHistory = [];
        if (class_exists('\App\Models\OrderStatusHistoryModel')) {
            $historyModel = new OrderStatusHistoryModel();
            $statusHistory = $historyModel->getOrderHistory($id);
        }

        $data = [
            'title' => 'Order Details',
            'order' => $order,
            'technicians' => $this->userModel->getTechnicians(),
            'status_history' => $statusHistory,
            'statuses' => [
                'received' => 'Received',
                'diagnosed' => 'Diagnosed',
                'waiting_approval' => 'Waiting Approval',
                'in_progress' => 'In Progress',
                'waiting_parts' => 'Waiting Parts',
                'completed' => 'Completed',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled'
            ]
        ];

        return view('admin/orders/show', $data);
    }

    public function new(): string
    {
        $data = [
            'title' => 'Create New Order',
            'customers' => $this->customerModel->findAll(),
            'device_types' => $this->deviceTypeModel->getActiveTypes(),
            'technicians' => $this->userModel->getTechnicians()
        ];

        return view('admin/orders/new', $data);
    }

    public function create()
    {
        $rules = [
            'customer_id' => 'required|integer',
            'device_type_id' => 'required|integer',
            'device_brand' => 'required',
            'device_model' => 'required',
            'problem_description' => 'required|min_length[10]',
            'priority' => 'required|in_list[low,normal,high,urgent]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'order_number' => $this->generateOrderNumber(),
            'customer_id' => $this->request->getPost('customer_id'),
            'device_type_id' => $this->request->getPost('device_type_id'),
            'device_brand' => $this->request->getPost('device_brand'),
            'device_model' => $this->request->getPost('device_model'),
            'device_serial' => $this->request->getPost('device_serial'),
            'problem_description' => $this->request->getPost('problem_description'),
            'accessories' => $this->request->getPost('accessories'),
            'priority' => $this->request->getPost('priority'),
            'technician_id' => $this->request->getPost('technician_id'),
            'notes' => $this->request->getPost('notes'),
            'status' => 'received',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->orderModel->insert($data)) {
            return redirect()->to('/admin/orders')->with('success', 'Order created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create order');
    }

    public function edit($id): string
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $data = [
            'title' => 'Edit Order',
            'order' => $order,
            'customers' => $this->customerModel->findAll(),
            'device_types' => $this->deviceTypeModel->getActiveTypes(),
            'technicians' => $this->userModel->getTechnicians()
        ];

        return view('admin/orders/edit', $data);
    }

    public function update($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'customer_id' => 'required|integer',
            'device_type_id' => 'required|integer',
            'device_brand' => 'required',
            'device_model' => 'required',
            'problem_description' => 'required|min_length[10]',
            'priority' => 'required|in_list[low,normal,high,urgent]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'customer_id' => $this->request->getPost('customer_id'),
            'device_type_id' => $this->request->getPost('device_type_id'),
            'device_brand' => $this->request->getPost('device_brand'),
            'device_model' => $this->request->getPost('device_model'),
            'device_serial' => $this->request->getPost('device_serial'),
            'problem_description' => $this->request->getPost('problem_description'),
            'accessories' => $this->request->getPost('accessories'),
            'priority' => $this->request->getPost('priority'),
            'technician_id' => $this->request->getPost('technician_id'),
            'estimated_cost' => $this->request->getPost('estimated_cost'),
            'final_cost' => $this->request->getPost('final_cost'),
            'notes' => $this->request->getPost('notes'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->orderModel->update($id, $data)) {
            return redirect()->to('/admin/orders')->with('success', 'Order updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update order');
    }

    public function updateStatus($id): string
    {
        $order = $this->orderModel->select('
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
            ->where('repair_orders.id', $id)
            ->first();

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $data = [
            'title' => 'Update Order Status',
            'order' => $order,
            'statuses' => [
                'received' => 'Received',
                'diagnosed' => 'Diagnosed',
                'waiting_approval' => 'Waiting Approval',
                'in_progress' => 'In Progress',
                'waiting_parts' => 'Waiting Parts',
                'completed' => 'Completed',
                'delivered' => 'Delivered',
                'cancelled' => 'Cancelled'
            ]
        ];

        return view('admin/orders/update_status', $data);
    }

    public function saveStatus($id): RedirectResponse
    {
        $order = $this->orderModel->select('
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
            ->where('repair_orders.id', $id)
            ->first();

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'status' => 'required|in_list[received,diagnosed,waiting_approval,in_progress,waiting_parts,completed,delivered,cancelled]',
            'notes' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');
        $oldStatus = $order['status'];

        // Only update if status actually changed
        if ($newStatus === $oldStatus) {
            return redirect()->to("/admin/orders/{$id}")
                ->with('info', 'Status was not changed');
        }

        // Log status change if OrderStatusHistoryModel exists
        if (class_exists('\App\Models\OrderStatusHistoryModel')) {
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $id,
                $oldStatus,
                $newStatus,
                $notes,
                session()->get('user_id')
            );
        }

        $updateData = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Set completion time if status is completed or delivered
        if (in_array($newStatus, ['completed', 'delivered']) && empty($order['completed_at'])) {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        if ($this->orderModel->update($id, $updateData)) {
            // Send notification if customer has email
            $this->sendStatusNotification($order, $newStatus);

            return redirect()->to("/admin/orders/{$id}")
                ->with('success', 'Order status updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update status');
    }

    public function delete($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        if ($this->orderModel->delete($id)) {
            return redirect()->to('/admin/orders')->with('success', 'Order deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete order');
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $exists = $this->orderModel->where('order_number', $orderNumber)->first();
        } while ($exists);

        return $orderNumber;
    }

    /**
     * Send status notification to customer
     */
    private function sendStatusNotification($order, $newStatus): void
    {
        try {
            // Only send if customer has email
            if (!empty($order['customer_email'])) {
                // You can implement email notification here
                // Example using NotificationService if it exists
                if (class_exists('\App\Libraries\NotificationService')) {
                    $notificationService = new \App\Libraries\NotificationService();
                    $notificationService->sendOrderStatusUpdate($order, $newStatus);
                }
            }
        } catch (\Exception $e) {
            // Log error but don't fail the status update
            log_message('error', 'Failed to send status notification: ' . $e->getMessage());
        }
    }
}