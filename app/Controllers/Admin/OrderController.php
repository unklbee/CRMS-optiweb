<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RepairOrderModel;
use App\Models\CustomerModel;
use App\Models\DeviceTypeModel;
use App\Models\UserModel;
use App\Models\OrderStatusHistoryModel;
use App\Models\OrderPartModel;
use App\Models\PartModel;
use App\Models\StockMovementModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class OrderController extends BaseController
{
    protected RepairOrderModel $orderModel;
    protected CustomerModel $customerModel;
    protected DeviceTypeModel $deviceTypeModel;
    protected UserModel $userModel;
    protected OrderPartModel $orderPartModel;
    protected PartModel $partModel;
    protected StockMovementModel $stockMovementModel;

    public function __construct()
    {
        $this->orderModel = new RepairOrderModel();
        $this->customerModel = new CustomerModel();
        $this->deviceTypeModel = new DeviceTypeModel();
        $this->userModel = new UserModel();
        $this->orderPartModel = new OrderPartModel();
        $this->partModel = new PartModel();
        $this->stockMovementModel = new StockMovementModel();
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

        // Get parts used in this order
        $orderParts = $this->orderPartModel->getOrderParts($id);

        // Get stock movements for this order
        $stockMovements = $this->stockMovementModel->getMovementsByReference('order', $id);

        $data = [
            'title' => 'Order Details',
            'order' => $order,
            'order_parts' => $orderParts,
            'stock_movements' => $stockMovements,
            'technicians' => $this->userModel->getTechnicians(),
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
            'order_number' => generate_order_number(),
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
        $order = $this->orderModel->find($id);

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
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        // Log status change
        $historyModel = new OrderStatusHistoryModel();
        $historyModel->insert([
            'order_id' => $id,
            'old_status' => $order['status'],
            'new_status' => $newStatus,
            'notes' => $notes,
            'changed_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $updateData = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Set completion time if status is completed
        if ($newStatus === 'completed') {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        if ($this->orderModel->update($id, $updateData)) {
            return redirect()->to("/admin/orders/{$id}")->with('success', 'Status updated successfully');
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
     * Manage parts for an order
     */
    public function manageParts($id): string
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Get current parts in order
        $orderParts = $this->orderPartModel->getOrderParts($id);

        // Get available parts
        $availableParts = $this->partModel->where('status', 'active')
            ->where('stock_quantity >', 0)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Manage Order Parts',
            'order' => $order,
            'order_parts' => $orderParts,
            'available_parts' => $availableParts
        ];

        return view('admin/orders/manage_parts', $data);
    }

    /**
     * Add part to order
     */
    public function addPart($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'part_id' => 'required|integer',
            'quantity' => 'required|integer|greater_than[0]',
            'unit_price' => 'required|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $partId = $this->request->getPost('part_id');
        $quantity = (int)$this->request->getPost('quantity');
        $unitPrice = (float)$this->request->getPost('unit_price');
        $notes = $this->request->getPost('notes');

        $part = $this->partModel->find($partId);
        if (!$part) {
            return redirect()->back()->with('error', 'Part not found');
        }

        // Check stock availability
        if ($part['stock_quantity'] < $quantity) {
            return redirect()->back()->with('error', "Insufficient stock. Available: {$part['stock_quantity']}, Requested: {$quantity}");
        }

        // Add part to order using the fixed method
        $orderPartData = [
            'order_id' => $id,
            'part_id' => $partId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s') // Explicitly set created_at
        ];

        // Use the fixed addPartToOrder method
        $insertResult = $this->orderPartModel->addPartToOrder($orderPartData);

        if ($insertResult) {
            // Update part stock and record movement
            $newStock = $part['stock_quantity'] - $quantity;

            $updateSuccess = $this->partModel->update($partId, [
                'stock_quantity' => $newStock,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($updateSuccess) {
                // Record stock movement
                $this->stockMovementModel->recordMovement([
                    'part_id' => $partId,
                    'movement_type' => 'use',
                    'quantity_before' => $part['stock_quantity'],
                    'quantity_change' => $quantity,
                    'quantity_after' => $newStock,
                    'reference_type' => 'order',
                    'reference_id' => $id,
                    'unit_cost' => $unitPrice,
                    'notes' => $notes ?: "Used in order #{$order['order_number']}",
                    'created_by' => session()->get('user_id')
                ]);

                return redirect()->back()->with('success', 'Part added to order successfully');
            } else {
                // Rollback: remove the order part if stock update failed
                $this->orderPartModel->delete($insertResult);
                return redirect()->back()->with('error', 'Failed to update stock. Part not added to order.');
            }
        }

        return redirect()->back()->with('error', 'Failed to add part to order');
    }

    /**
     * Remove part from order
     */
    public function removePart($orderId, $orderPartId): RedirectResponse
    {
        $order = $this->orderModel->find($orderId);
        $orderPart = $this->orderPartModel->find($orderPartId);

        if (!$order || !$orderPart) {
            throw new PageNotFoundException('Order or part not found');
        }

        // Return stock
        $part = $this->partModel->find($orderPart['part_id']);
        if ($part) {
            $newStock = $part['stock_quantity'] + $orderPart['quantity'];
            $this->partModel->update($part['id'], [
                'stock_quantity' => $newStock,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Record stock movement
            $this->stockMovementModel->recordMovement([
                'part_id' => $part['id'],
                'movement_type' => 'return',
                'quantity_before' => $part['stock_quantity'],
                'quantity_change' => $orderPart['quantity'],
                'quantity_after' => $newStock,
                'reference_type' => 'order',
                'reference_id' => $orderId,
                'unit_cost' => $orderPart['unit_price'],
                'notes' => "Returned from order #{$order['order_number']}",
                'created_by' => session()->get('user_id')
            ]);
        }

        // Remove from order
        if ($this->orderPartModel->delete($orderPartId)) {
            return redirect()->back()->with('success', 'Part removed from order and stock restored');
        }

        return redirect()->back()->with('error', 'Failed to remove part from order');
    }
}