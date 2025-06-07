<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuotationModel;
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
    protected QuotationModel $quotationModel;

    public function __construct()
    {
        $this->orderModel = new RepairOrderModel();
        $this->customerModel = new CustomerModel();
        $this->deviceTypeModel = new DeviceTypeModel();
        $this->userModel = new UserModel();
        $this->orderPartModel = new OrderPartModel();
        $this->partModel = new PartModel();
        $this->stockMovementModel = new StockMovementModel();
        $this->quotationModel = new QuotationModel();
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
                ->orLike('repair_orders.device_brand', $search)
                ->orLike('repair_orders.device_model', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('repair_orders.status', $status);
        }

        $builder->orderBy('repair_orders.created_at', 'DESC');

        $orders = $builder->paginate($perPage);
        $pager = $this->orderModel->pager;

        $data = [
            'title' => 'Repair Orders',
            'orders' => $orders,
            'pager' => $pager,
            'search' => $search,
            'current_status' => $status,
            'statuses' => [
                'received' => 'Received',
                'diagnosed' => 'Diagnosed',
                'waiting_approval' => 'Waiting Approval',
                'approved' => 'Approved',
                'in_progress' => 'In Progress',
                'waiting_parts' => 'Waiting Parts',
                'completed' => 'Completed',
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
                customers.address as customer_address,
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

        // Get order parts
        $orderParts = $this->orderPartModel->getOrderParts($id);

        // Get status history
        $historyModel = new OrderStatusHistoryModel();
        $statusHistory = $historyModel->getOrderHistory($id);

        // Get quotations
        $quotations = $this->quotationModel->where('order_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Order Details - #' . $order['order_number'],
            'order' => $order,
            'parts' => $orderParts,
            'status_history' => $statusHistory,
            'quotations' => $quotations,
            'technicians' => $this->userModel->getTechnicians()
        ];

        return view('admin/orders/show', $data);
    }

    public function create(): string
    {
        $data = [
            'title' => 'Create New Order',
            'customers' => $this->customerModel->findAll(),
            'device_types' => $this->deviceTypeModel->getActiveTypes(),
            'technicians' => $this->userModel->getTechnicians()
        ];

        return view('admin/orders/create', $data);
    }

    public function store(): RedirectResponse
    {
        $rules = [
            'customer_id' => 'required|integer',
            'device_type_id' => 'required|integer',
            'device_brand' => 'required|min_length[2]',
            'device_model' => 'required|min_length[2]',
            'problem_description' => 'required|min_length[10]',
            'priority' => 'required|in_list[low,medium,high,urgent]'
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
            'technician_id' => $this->request->getPost('technician_id'),
            'priority' => $this->request->getPost('priority'),
            'status' => 'received',
            'diagnosis_status' => 'pending',
            'notes' => $this->request->getPost('notes')
        ];

        if ($orderId = $this->orderModel->insert($data)) {
            // Log initial status
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $orderId,
                null,
                'received',
                'Order created',
                session()->get('user_id')
            );

            return redirect()->to("/admin/orders/{$orderId}")
                ->with('success', 'Order created successfully');
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
            'title' => 'Edit Order - #' . $order['order_number'],
            'order' => $order,
            'customers' => $this->customerModel->findAll(),
            'device_types' => $this->deviceTypeModel->findAll(),
            'technicians' => $this->userModel->getTechnicians()
        ];

        return view('admin/orders/edit', $data);
    }

    public function update($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'customer_id' => 'required|integer',
            'device_type_id' => 'required|integer',
            'device_brand' => 'required|min_length[2]',
            'device_model' => 'required|min_length[2]',
            'problem_description' => 'required|min_length[10]',
            'priority' => 'required|in_list[low,medium,high,urgent]'
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
            'technician_id' => $this->request->getPost('technician_id'),
            'priority' => $this->request->getPost('priority'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->orderModel->update($id, $data)) {
            return redirect()->to("/admin/orders/{$id}")
                ->with('success', 'Order updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update order');
    }

    public function delete($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if order can be deleted
        if (in_array($order['status'], ['in_progress', 'completed'])) {
            return redirect()->back()->with('error', 'Cannot delete order in current status');
        }

        if ($this->orderModel->delete($id)) {
            return redirect()->to('/admin/orders')
                ->with('success', 'Order deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete order');
    }

    public function updateStatus($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        $validStatuses = [
            'received', 'diagnosed', 'waiting_approval', 'approved',
            'in_progress', 'waiting_parts', 'completed', 'cancelled'
        ];

        if (!in_array($newStatus, $validStatuses)) {
            return redirect()->back()->with('error', 'Invalid status');
        }

        $updateData = ['status' => $newStatus];

        // Handle completion
        if ($newStatus === 'completed') {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
            $updateData['final_cost'] = $this->request->getPost('final_cost');
        }

        if ($this->orderModel->update($id, $updateData)) {
            // Log status change
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $id,
                $order['status'],
                $newStatus,
                $notes,
                session()->get('user_id')
            );

            return redirect()->to("/admin/orders/{$id}")
                ->with('success', 'Order status updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update order status');
    }

    public function assignTechnician($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $technicianId = $this->request->getPost('technician_id');

        if ($this->orderModel->update($id, ['technician_id' => $technicianId])) {
            // Log assignment
            $historyModel = new OrderStatusHistoryModel();
            $technician = $this->userModel->find($technicianId);
            $historyModel->addStatusChange(
                $id,
                $order['status'],
                $order['status'],
                'Assigned to technician: ' . $technician['full_name'],
                session()->get('user_id')
            );

            return redirect()->to("/admin/orders/{$id}")
                ->with('success', 'Technician assigned successfully');
        }

        return redirect()->back()->with('error', 'Failed to assign technician');
    }

    /**
     * IMPROVED: Update status with better validation
     */
    public function saveStatus($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        // IMPROVED: Validate workflow
        $errors = $this->validateQuotationWorkflow($id, $newStatus);
        if (!empty($errors)) {
            return redirect()->back()->with('error', implode('. ', $errors));
        }

        // Log status change
        $historyModel = new OrderStatusHistoryModel();
        $historyModel->addStatusChange(
            $id,
            $order['status'],
            $newStatus,
            $notes,
            session()->get('user_id')
        );

        $updateData = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Set completion time if status is completed
        if ($newStatus === 'completed' && !$order['completed_at']) {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        // IMPROVED: Handle quotation-related status changes
        if ($newStatus === 'in_progress') {
            $quotation = $this->quotationModel->getQuotationByOrder($id);
            if ($quotation && $quotation['status'] === 'approved') {
                // Set final cost from approved quotation
                $updateData['final_cost'] = $quotation['total_cost'];
            }
        }

        if ($this->orderModel->update($id, $updateData)) {
            // IMPROVED: Send notifications based on status
            $this->sendStatusUpdateNotification($order, $newStatus, $notes);

            return redirect()->to("/admin/orders/{$id}")->with('success', 'Status updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update status');
    }

    /**
     * NEW: Send status update notifications
     */
    private function sendStatusUpdateNotification($order, $newStatus, $notes): bool
    {
        // Get customer email
        $customerData = $this->customerModel->find($order['customer_id']);
        if (!$customerData || !$customerData['email']) {
            return false;
        }

        // Determine if this status change should trigger notification
        $notifyStatuses = ['diagnosed', 'waiting_approval', 'in_progress', 'waiting_parts', 'completed', 'delivered'];

        if (!in_array($newStatus, $notifyStatuses)) {
            return false;
        }

        $email = \Config\Services::email();

        $subject = "Order Status Update - {$order['order_number']}";
        $message = view('emails/order_status_update', [
            'order' => $order,
            'new_status' => $newStatus,
            'notes' => $notes,
            'customer' => $customerData
        ]);

        $email->setTo($customerData['email']);
        $email->setSubject($subject);
        $email->setMessage($message);

        return $email->send();
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

    /**
     * Generate and display order receipt
     */
    public function receipt($id): string
    {
        $order = $this->orderModel->select('
            repair_orders.*,
            customers.full_name as customer_name,
            customers.phone as customer_phone,
            customers.email as customer_email,
            customers.address as customer_address,
            device_types.name as device_type_name,
            users.full_name as received_by
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
            'title' => 'Order Receipt',
            'order' => $order,
            'print_mode' => $this->request->getGet('print') === '1',
            'shop_info' => [
                'name' => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone' => get_site_setting('contact_phone', ''),
                'email' => get_site_setting('contact_email', ''),
            ]
        ];

        return view('admin/orders/receipt', $data);
    }

    /**
     * Send receipt via email
     */
    public function emailReceipt($id): RedirectResponse
    {
        $order = $this->orderModel->select('
            repair_orders.*,
            customers.full_name as customer_name,
            customers.phone as customer_phone,
            customers.email as customer_email,
            device_types.name as device_type_name
        ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->where('repair_orders.id', $id)
            ->first();

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        if (!$order['customer_email']) {
            return redirect()->back()->with('error', 'Customer email not available');
        }

        // Simple email sending using CodeIgniter's email library
        $email = \Config\Services::email();

        $subject = "Service Receipt - Order #{$order['order_number']}";
        $message = view('emails/order_receipt', ['order' => $order]);

        $email->setTo($order['customer_email']);
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            return redirect()->back()->with('success', 'Receipt sent to customer email successfully');
        }

        return redirect()->back()->with('error', 'Failed to send receipt email');
    }

    /**
     * Generate delivery receipt when order is completed
     */
    public function deliveryReceipt($id)
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

        // Only allow delivery receipt for completed orders
        if (!in_array($order['status'], ['completed', 'delivered'])) {
            return redirect()->back()->with('error', 'Delivery receipt only available for completed orders');
        }

        $data = [
            'title' => 'Delivery Receipt',
            'order' => $order,
            'print_mode' => $this->request->getGet('print') === '1',
            'receipt_type' => 'delivery',
            'shop_info' => [
                'name' => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone' => get_site_setting('contact_phone', ''),
                'email' => get_site_setting('contact_email', ''),
            ]
        ];

        return view('admin/orders/receipt', $data);
    }

    /**
     * IMPROVED: Create quotation with better validation
     */
    public function createQuotation($id): string
    {
        $order = $this->orderModel->getDiagnosisDetails($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // IMPROVED: Check if order can have quotation
        if (!in_array($order['status'], ['diagnosed', 'waiting_approval'])) {
            return redirect()->to("/admin/orders/{$id}")
                ->with('error', 'Order must be diagnosed before creating quotation. Current status: ' . ucfirst($order['status']));
        }

        // IMPROVED: Check diagnosis completion
        if (empty($order['diagnosis_notes']) && empty($order['recommended_actions'])) {
            return redirect()->to("/admin/orders/{$id}/diagnosis")
                ->with('error', 'Please complete diagnosis before creating quotation');
        }

        // Check if quotation already exists
        $existingQuotation = $this->quotationModel->getQuotationByOrder($id);

        // IMPROVED: Get order parts for auto-calculation
        $orderParts = $this->orderPartModel->getOrderParts($id);
        $autoPartsTotal = array_sum(array_column($orderParts, 'total_price'));

        $data = [
            'title' => 'Create Quotation',
            'order' => $order,
            'existing_quotation' => $existingQuotation,
            'default_tax_rate' => get_site_setting('tax_rate', 0),
            'default_warranty' => get_site_setting('default_warranty', '30 days'),
            'service_rates' => $this->getServiceRates($order['device_type_id']),
            'order_parts' => $orderParts,
            'auto_parts_total' => $autoPartsTotal // NEW: Pass auto-calculated total
        ];

        return view('admin/orders/create_quotation', $data);
    }

    /**
     * IMPROVED: Save quotation with better validation and workflow
     */
    public function saveQuotation($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // IMPROVED: Validate order status
        if (!in_array($order['status'], ['diagnosed', 'waiting_approval'])) {
            return redirect()->back()->with('error', 'Cannot create quotation for order in current status: ' . ucfirst($order['status']));
        }

        $rules = [
            'service_cost' => 'required|decimal|greater_than[0]',
            'parts_cost' => 'permit_empty|decimal',
            'estimated_duration' => 'required|min_length[3]',
            'valid_until' => 'required|valid_date'
            // REMOVE 'total_cost' dari rules karena akan dihitung otomatis
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // IMPROVED: Validate valid_until date
        $validUntil = $this->request->getPost('valid_until');
        if (strtotime($validUntil) <= time()) {
            return redirect()->back()->withInput()->with('error', 'Valid until date must be in the future');
        }

        // Manual calculate totals
        $serviceCost = (float)$this->request->getPost('service_cost');
        $partsCost = (float)($this->request->getPost('parts_cost') ?: 0);
        $additionalCost = (float)($this->request->getPost('additional_cost') ?: 0);
        $discountAmount = (float)($this->request->getPost('discount_amount') ?: 0);
        $discountPercentage = (float)($this->request->getPost('discount_percentage') ?: 0);
        $taxPercentage = (float)($this->request->getPost('tax_percentage') ?: 0);

        // Calculate subtotal
        $subtotal = $serviceCost + $partsCost + $additionalCost;

        // Apply percentage discount if set
        if ($discountPercentage > 0) {
            $discountAmount = ($subtotal * $discountPercentage) / 100;
        }

        // Subtotal after discount
        $afterDiscount = $subtotal - $discountAmount;

        // Calculate tax
        $taxAmount = ($afterDiscount * $taxPercentage) / 100;

        // Final total
        $totalCost = $afterDiscount + $taxAmount;

        // Check if quotation already exists
        $existingQuotation = $this->quotationModel->getQuotationByOrder($id);

        $quotationData = [
            'order_id' => $id,
            'service_cost' => $serviceCost,
            'parts_cost' => $partsCost,
            'additional_cost' => $additionalCost,
            'discount_amount' => $discountAmount,
            'discount_percentage' => $discountPercentage,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,  // TAMBAHKAN INI
            'total_cost' => $totalCost,   // TAMBAHKAN INI
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'warranty_period' => $this->request->getPost('warranty_period'),
            'terms_conditions' => $this->request->getPost('terms_conditions'),
            'internal_notes' => $this->request->getPost('internal_notes'),
            'valid_until' => $validUntil,
            'created_by' => session()->get('user_id'),
            'status' => 'draft' // Always start as draft
        ];

        if ($existingQuotation) {
            // Update existing quotation
            $quotationId = $existingQuotation['id'];
            $result = $this->quotationModel->update($quotationId, $quotationData);
            $message = 'Quotation updated successfully';
        } else {
            // Create new quotation
            $quotationId = $this->quotationModel->insert($quotationData);
            $result = $quotationId ? true : false;
            $message = 'Quotation created successfully';
        }

        if ($result) {
            // IMPROVED: Only update order status if not already waiting_approval
            if ($order['status'] !== 'waiting_approval') {
                $this->orderModel->update($id, [
                    'estimated_cost' => $quotationData['service_cost'] + $quotationData['parts_cost'] + $quotationData['additional_cost'],
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            // Check if should send quotation immediately
            if ($this->request->getPost('send_immediately')) {
                // Mark as sent and send email
                $this->quotationModel->markAsSent($quotationId);

                $quotationDetails = $this->quotationModel->getQuotationWithOrderDetails($quotationId);
                if ($quotationDetails['customer_email']) {
                    $email = \Config\Services::email();
                    $subject = "Repair Quotation - Order #{$quotationDetails['order_number']}";
                    $emailMessage = view('emails/quotation', ['quotation' => $quotationDetails]);

                    $email->setTo($quotationDetails['customer_email']);
                    $email->setSubject($subject);
                    $email->setMessage($emailMessage);

                    if ($email->send()) {
                        // Update order status to waiting_approval
                        $this->orderModel->update($id, [
                            'status' => 'waiting_approval',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        // Log status change
                        $historyModel = new OrderStatusHistoryModel();
                        $historyModel->addStatusChange(
                            $id,
                            $order['status'],
                            'waiting_approval',
                            'Quotation created and sent to customer for approval',
                            session()->get('user_id')
                        );

                        $message .= ' and sent to customer';
                    } else {
                        $message .= ' but failed to send email';
                    }
                } else {
                    $message .= ' but customer email not available';
                }
            }

            return redirect()->to("/admin/orders/{$id}")
                ->with('success', $message);
        }

        return redirect()->back()->with('error', 'Failed to save quotation');
    }

    /**
     * Show quotation
     */
    public function showQuotation($orderId): string
    {
        // 1. Cari quotation yang terkait dengan order tersebut
        $existingQuotation = $this->quotationModel->getQuotationByOrder($orderId);

        if (! $existingQuotation) {
            throw new PageNotFoundException('Quotation not found for this order');
        }

        // 2. Sekarang panggil detail lengkap berdasarkan quotation_id
        $quotationWithDetails = $this->quotationModel->getQuotationWithOrderDetails($existingQuotation['id']);

        if (! $quotationWithDetails) {
            throw new PageNotFoundException('Quotation details not found');
        }

        $data = [
            'title'       => 'Quotation Details',
            'quotation'   => $quotationWithDetails,
            'order_parts' => $this->orderPartModel->getOrderParts($quotationWithDetails['order_id']),
            'print_mode'  => $this->request->getGet('print') === '1',
            'shop_info'   => [
                'name'    => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone'   => get_site_setting('contact_phone', ''),
                'email'   => get_site_setting('contact_email', ''),
            ],
        ];

        return view('admin/orders/quotation_pdf', $data);
    }


    /**
     * Show quotation by quotation ID
     */
    public function viewQuotation($quotationId): string
    {
        $quotation = $this->quotationModel->getQuotationWithOrderDetails($quotationId);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        $data = [
            'title' => 'Quotation - ' . $quotation['quotation_number'],
            'quotation' => $quotation,
            'order_parts' => $this->orderPartModel->getOrderParts($quotation['order_id']),
            'print_mode' => $this->request->getGet('print') === '1',
            'shop_info' => [
                'name' => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone' => get_site_setting('contact_phone', ''),
                'email' => get_site_setting('contact_email', ''),
            ]
        ];

        return view('admin/orders/quotation_pdf', $data);
    }

    /**
     * Send quotation to customer
     */
    public function sendQuotation($orderId, $quotationId): RedirectResponse
    {
        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Get complete quotation data with order details
        $quotationWithDetails = $this->quotationModel->getQuotationWithOrderDetails($quotationId);

        if (!$quotationWithDetails['customer_email']) {
            return redirect()->back()->with('error', 'Customer email not available');
        }

        // Send email using CodeIgniter's email library
        $email = \Config\Services::email();

        $subject = "Repair Quotation - Order #{$quotationWithDetails['order_number']}";
        $message = view('emails/quotation', ['quotation' => $quotationWithDetails]);

        $email->setTo($quotationWithDetails['customer_email']);
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            // Mark quotation as sent
            $this->quotationModel->markAsSent($quotationId);

            // Update order status to waiting_approval if not already
            if ($quotationWithDetails['order_status'] !== 'waiting_approval') {
                $this->orderModel->update($orderId, [
                    'status' => 'waiting_approval',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Log status change
                $historyModel = new OrderStatusHistoryModel();
                $historyModel->addStatusChange(
                    $orderId,
                    $quotationWithDetails['order_status'],
                    'waiting_approval',
                    'Quotation sent to customer for approval',
                    session()->get('user_id')
                );
            }

            return redirect()->back()->with('success', 'Quotation sent to customer successfully');
        }

        return redirect()->back()->with('error', 'Failed to send quotation email');
    }

    /**
     * Edit existing quotation
     */
    public function editQuotation($orderId): string
    {
        $order = $this->orderModel->getDiagnosisDetails($orderId);
        $existingQuotation = $this->quotationModel->getQuotationByOrder($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        if (!$existingQuotation) {
            return redirect()->to("/admin/orders/{$orderId}/create-quotation")
                ->with('error', 'No quotation found. Create a new one.');
        }

        $data = [
            'title' => 'Edit Quotation',
            'order' => $order,
            'existing_quotation' => $existingQuotation,
            'is_edit_mode' => true,
            'default_tax_rate' => get_site_setting('tax_rate', 0),
            'default_warranty' => get_site_setting('default_warranty', '30 days'),
            'service_rates' => $this->getServiceRates($order['device_type_id']),
            'order_parts' => $this->orderPartModel->getOrderParts($orderId)
        ];

        return view('admin/orders/create_quotation', $data);
    }

    /**
     * Create quotation revision
     */
    public function reviseQuotation($orderId)
    {
        $order = $this->orderModel->find($orderId);
        $existingQuotation = $this->quotationModel->getQuotationByOrder($orderId);

        if (!$order || !$existingQuotation) {
            throw new PageNotFoundException('Order or quotation not found');
        }

        // Validate input
        $rules = [
            'service_cost' => 'required|decimal',
            'parts_cost' => 'permit_empty|decimal',
            'total_cost' => 'required|decimal',
            'estimated_duration' => 'required',
            'valid_until' => 'required|valid_date',
            'revision_reason' => 'required|min_length[10]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare revision data
        $revisionData = [
            'order_id' => $orderId,
            'service_cost' => $this->request->getPost('service_cost'),
            'parts_cost' => $this->request->getPost('parts_cost') ?: 0,
            'additional_cost' => $this->request->getPost('additional_cost') ?: 0,
            'discount_amount' => $this->request->getPost('discount_amount') ?: 0,
            'discount_percentage' => $this->request->getPost('discount_percentage') ?: 0,
            'tax_percentage' => $this->request->getPost('tax_percentage') ?: 0,
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'warranty_period' => $this->request->getPost('warranty_period'),
            'terms_conditions' => $this->request->getPost('terms_conditions'),
            'internal_notes' => $this->request->getPost('internal_notes') . "\n\nRevision Reason: " . $this->request->getPost('revision_reason'),
            'valid_until' => $this->request->getPost('valid_until'),
            'created_by' => session()->get('user_id')
        ];

        // Create revision
        $newQuotationId = $this->quotationModel->createRevision($existingQuotation['id'], $revisionData);

        if ($newQuotationId) {
            // Log status change
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $orderId,
                $order['status'],
                'waiting_approval',
                'Quotation revised: ' . $this->request->getPost('revision_reason'),
                session()->get('user_id')
            );

            return redirect()->to("/admin/orders/{$orderId}/quotation/{$newQuotationId}")
                ->with('success', 'Quotation revision created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create quotation revision');
    }

    /**
     * Customer approve quotation (public endpoint)
     */
    public function approveQuotation($quotationId)
    {
        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        $customerNotes = $this->request->getPost('customer_notes');

        if ($this->quotationModel->approveQuotation($quotationId, $customerNotes)) {
            // Update order status to approved/in_progress
            $this->orderModel->update($quotation['order_id'], [
                'status' => 'in_progress',
                'final_cost' => $quotation['total_cost'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Log status change
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $quotation['order_id'],
                'waiting_approval',
                'in_progress',
                'Customer approved quotation. Repair work can begin.',
                null // No user ID for customer actions
            );

            return redirect()->to("/track-order?order=" . $quotation['order_number'])
                ->with('success', 'Quotation approved successfully. Repair work will begin soon.');
        }

        return redirect()->back()->with('error', 'Failed to approve quotation');
    }

    /**
     * IMPROVED: Get service rates with fallback
     */
    private function getServiceRates($deviceTypeId): array
    {
        // You can store this in database or config
        $serviceRates = [
            1 => [ // Laptop
                'diagnosis' => 50000,
                'software_repair' => 100000,
                'hardware_repair' => 150000,
                'screen_replacement' => 200000,
                'keyboard_replacement' => 80000,
                'battery_replacement' => 120000,
                'motherboard_repair' => 300000,
                'data_recovery' => 150000
            ],
            2 => [ // Desktop
                'diagnosis' => 50000,
                'software_repair' => 80000,
                'hardware_repair' => 120000,
                'component_replacement' => 100000,
                'system_upgrade' => 150000,
                'virus_removal' => 75000,
                'data_recovery' => 150000,
                'custom_build' => 200000
            ],
            3 => [ // Phone
                'diagnosis' => 30000,
                'software_repair' => 80000,
                'screen_replacement' => 250000,
                'battery_replacement' => 150000,
                'charging_port_repair' => 100000,
                'camera_repair' => 120000,
                'speaker_repair' => 80000,
                'water_damage_repair' => 200000
            ],
            4 => [ // Tablet
                'diagnosis' => 40000,
                'software_repair' => 90000,
                'screen_replacement' => 300000,
                'battery_replacement' => 180000,
                'charging_port_repair' => 120000,
                'button_repair' => 80000
            ]
        ];

        // Fallback rates for unknown device types
        $defaultRates = [
            'diagnosis' => 50000,
            'basic_repair' => 100000,
            'advanced_repair' => 200000,
            'component_replacement' => 150000,
            'software_service' => 80000,
            'emergency_service' => 300000
        ];

        return $serviceRates[$deviceTypeId] ?? $defaultRates;
    }

    /**
     * NEW: Method to validate quotation workflow
     */
    private function validateQuotationWorkflow($orderId, $newStatus): array
    {
        $order = $this->orderModel->find($orderId);
        $errors = [];

        if (!$order) {
            $errors[] = 'Order not found';
            return $errors;
        }

        // Validate status transitions
        $validTransitions = [
            'received' => ['diagnosed'],
            'diagnosed' => ['waiting_approval', 'in_progress'], // Allow direct to in_progress
            'waiting_approval' => ['in_progress', 'diagnosed'], // Can go back to diagnosed
            'in_progress' => ['waiting_parts', 'completed'],
            'waiting_parts' => ['in_progress'],
            'completed' => ['delivered'],
            'delivered' => [],
            'cancelled' => []
        ];

        $currentStatus = $order['status'];
        $allowedStatuses = $validTransitions[$currentStatus] ?? [];

        if (!in_array($newStatus, $allowedStatuses)) {
            $errors[] = "Cannot change status from '{$currentStatus}' to '{$newStatus}'";
        }

        // Specific validations for quotation-related statuses
        if ($newStatus === 'waiting_approval') {
            $quotation = $this->quotationModel->getQuotationByOrder($orderId);
            if (!$quotation) {
                $errors[] = 'Cannot set status to waiting_approval without a quotation';
            } elseif ($quotation['status'] !== 'sent') {
                $errors[] = 'Quotation must be sent before waiting for approval';
            }
        }

        if ($newStatus === 'in_progress') {
            $quotation = $this->quotationModel->getQuotationByOrder($orderId);
            if ($quotation && $quotation['status'] === 'sent') {
                $errors[] = 'Cannot start work while quotation is still pending customer approval';
            }
        }

        return $errors;
    }
}