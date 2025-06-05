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

        $orderId = $this->orderModel->insert($data);

        if ($orderId) {
            // Log initial status using existing OrderStatusHistoryModel
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $orderId,
                null,
                'received',
                'Order created and device received for inspection',
                session()->get('user_id')
            );

            // Check if user wants to print receipt immediately
            if ($this->request->getPost('print_receipt')) {
                return redirect()->to("/admin/orders/{$orderId}/receipt?print=1")
                    ->with('success', 'Order created successfully. Receipt is ready for printing.');
            }

            // Check if user wants to view receipt
            if ($this->request->getPost('view_receipt')) {
                return redirect()->to("/admin/orders/{$orderId}/receipt")
                    ->with('success', 'Order created successfully');
            }

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
        // PERBAIKAN: Tambahkan join dengan tabel customers untuk mendapatkan customer_name
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
     * Show diagnosis form
     */
    public function diagnosis($id): string
    {
        $order = $this->orderModel->getDiagnosisDetails($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if order can be diagnosed
        if (!in_array($order['status'], ['received', 'diagnosed'])) {
            return redirect()->to("/admin/orders/{$id}")
                ->with('error', 'This order cannot be diagnosed at current status');
        }

        $data = [
            'title' => 'Order Diagnosis',
            'order' => $order,
            'technicians' => $this->userModel->getTechnicians(),
            'common_issues' => $this->getCommonIssues($order['device_type_id']),
            'available_parts' => $this->partModel->where('status', 'active')->findAll()
        ];

        return view('admin/orders/diagnosis', $data);
    }

    /**
     * Save diagnosis results
     */
    public function saveDiagnosis($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'diagnosis_notes' => 'required|min_length[10]',
            'recommended_actions' => 'required|min_length[10]',
            'estimated_hours' => 'permit_empty|decimal',
            'estimated_cost' => 'permit_empty|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare issues found array
        $issuesFound = [];
        $issueInputs = $this->request->getPost('issues') ?? [];

        foreach ($issueInputs as $issue) {
            if (!empty($issue['description'])) {
                $issuesFound[] = [
                    'description' => $issue['description'],
                    'severity' => $issue['severity'] ?? 'medium',
                    'repair_needed' => $issue['repair_needed'] ?? true,
                    'estimated_cost' => $issue['estimated_cost'] ?? 0
                ];
            }
        }

        $diagnosisData = [
            'diagnosis_notes' => $this->request->getPost('diagnosis_notes'),
            'issues_found' => $issuesFound,
            'recommended_actions' => $this->request->getPost('recommended_actions'),
            'estimated_hours' => $this->request->getPost('estimated_hours'),
            'estimated_cost' => $this->request->getPost('estimated_cost')
        ];

        if ($this->orderModel->updateDiagnosis($id, $diagnosisData)) {
            // Update order status to diagnosed
            $this->orderModel->update($id, [
                'status' => 'diagnosed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Log status change
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $id,
                $order['status'],
                'diagnosed',
                'Diagnosis completed: ' . $this->request->getPost('diagnosis_notes'),
                session()->get('user_id')
            );

            // Check if should contact customer
            if ($this->request->getPost('contact_customer')) {
                return redirect()->to("/admin/orders/{$id}/contact-customer")
                    ->with('success', 'Diagnosis saved successfully. Prepare customer contact.');
            }

            return redirect()->to("/admin/orders/{$id}")
                ->with('success', 'Diagnosis completed successfully');
        }

        return redirect()->back()->with('error', 'Failed to save diagnosis');
    }

    /**
     * Get common issues for device type
     */
    private function getCommonIssues($deviceTypeId): array
    {
        // You can store this in database or config
        $commonIssues = [
            1 => [ // Laptop
                'Screen not working/cracked',
                'Keyboard not responding',
                'Battery not charging',
                'Overheating issues',
                'Hard drive failure',
                'RAM issues',
                'Motherboard problems',
                'Power adapter issues'
            ],
            2 => [ // Desktop
                'Won\'t turn on',
                'Blue screen errors',
                'Slow performance',
                'Hard drive clicking',
                'Graphics card issues',
                'Memory problems',
                'CPU overheating',
                'Power supply failure'
            ],
            3 => [ // Phone
                'Screen cracked/not responding',
                'Battery drains quickly',
                'Charging port issues',
                'Camera not working',
                'Speaker/microphone problems',
                'Water damage',
                'Software issues',
                'Home button not working'
            ]
        ];

        return $commonIssues[$deviceTypeId] ?? [
            'Device not powering on',
            'Performance issues',
            'Hardware malfunction',
            'Software problems',
            'Physical damage'
        ];
    }

    /**
     * Show diagnosis list/queue
     */
    public function diagnosisQueue(): string
    {
        $orders = $this->orderModel->getOrdersNeedingDiagnosis();
        $summary = $this->orderModel->getDiagnosisSummary();

        $data = [
            'title' => 'Diagnosis Queue',
            'orders' => $orders,
            'summary' => $summary,
            'technicians' => $this->userModel->getTechnicians()
        ];

        return view('admin/orders/diagnosis_queue', $data);
    }

    /**
     * Start diagnosis process
     */
    public function startDiagnosis($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if order can be diagnosed
        if (!in_array($order['status'], ['received', 'diagnosed'])) {
            return redirect()->to("/admin/orders/{$id}")
                ->with('error', 'This order cannot be diagnosed at current status');
        }

        // Update diagnosis status to in_progress
        $updateData = [
            'diagnosis_status' => 'in_progress',
            'diagnosed_by' => session()->get('user_id')
        ];

        if ($this->orderModel->update($id, $updateData)) {
            // Log the action in status history
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $id,
                $order['status'],
                $order['status'], // Keep same status, just starting diagnosis
                'Diagnosis started by technician',
                session()->get('user_id')
            );

            return redirect()->to("/admin/orders/{$id}/diagnosis")
                ->with('success', 'Diagnosis started. You can now input your findings.');
        }

        return redirect()->back()->with('error', 'Failed to start diagnosis');
    }

    /**
     * Create quotation from diagnosis
     */
    public function createQuotation($id): string
    {
        $order = $this->orderModel->getDiagnosisDetails($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if order has been diagnosed
        if ($order['diagnosis_status'] !== 'completed') {
            return redirect()->to("/admin/orders/{$id}")
                ->with('error', 'Order must be diagnosed before creating quotation');
        }

        // Check if quotation already exists
        $existingQuotation = $this->quotationModel->getQuotationByOrder($id);

        $data = [
            'title' => 'Create Quotation',
            'order' => $order,
            'existing_quotation' => $existingQuotation,
            'default_tax_rate' => get_site_setting('tax_rate', 0),
            'default_warranty' => get_site_setting('default_warranty', '30 days'),
            'service_rates' => $this->getServiceRates($order['device_type_id']),
            'order_parts' => $this->orderPartModel->getOrderParts($id)
        ];

        return view('admin/orders/create_quotation', $data);
    }

    /**
     * Save quotation
     */
    public function saveQuotation($id)
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'service_cost' => 'required|decimal',
            'parts_cost' => 'permit_empty|decimal',
            'total_cost' => 'required|decimal',
            'estimated_duration' => 'required',
            'valid_until' => 'required|valid_date'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if quotation already exists
        $existingQuotation = $this->quotationModel->getQuotationByOrder($id);

        $quotationData = [
            'order_id' => $id,
            'service_cost' => $this->request->getPost('service_cost'),
            'parts_cost' => $this->request->getPost('parts_cost') ?: 0,
            'additional_cost' => $this->request->getPost('additional_cost') ?: 0,
            'discount_amount' => $this->request->getPost('discount_amount') ?: 0,
            'discount_percentage' => $this->request->getPost('discount_percentage') ?: 0,
            'tax_percentage' => $this->request->getPost('tax_percentage') ?: 0,
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'warranty_period' => $this->request->getPost('warranty_period'),
            'terms_conditions' => $this->request->getPost('terms_conditions'),
            'internal_notes' => $this->request->getPost('internal_notes'),
            'valid_until' => $this->request->getPost('valid_until'),
            'created_by' => session()->get('user_id')
        ];

        if ($existingQuotation) {
            // Update existing quotation
            $quotationId = $existingQuotation['id'];
            $result = $this->quotationModel->update($quotationId, $quotationData);
        } else {
            // Create new quotation
            $quotationId = $this->quotationModel->insert($quotationData);
            $result = $quotationId ? true : false;
        }

        if ($result) {
            // Update order status to waiting_approval
            $this->orderModel->update($id, [
                'status' => 'waiting_approval',
                'estimated_cost' => $quotationData['service_cost'] + $quotationData['parts_cost'] + $quotationData['additional_cost'],
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Log status change
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $id,
                $order['status'],
                'waiting_approval',
                'Quotation created and waiting for customer approval',
                session()->get('user_id')
            );

            // Check if should send quotation immediately
            if ($this->request->getPost('send_immediately')) {
                return redirect()->to("/admin/orders/{$id}/quotation/{$quotationId}/send")
                    ->with('success', 'Quotation created successfully. Prepare to send to customer.');
            }

            return redirect()->to("/admin/orders/{$id}/quotation")
                ->with('success', 'Quotation created successfully');
        }

        return redirect()->back()->with('error', 'Failed to save quotation');
    }

    /**
     * Show quotation
     */
    public function showQuotation($id): string
    {
        $order = $this->orderModel->find($id);
        $quotation = $this->quotationModel->getQuotationByOrder($id);

        if (!$order || !$quotation) {
            throw new PageNotFoundException('Order or quotation not found');
        }

        $data = [
            'title' => 'Quotation Details',
            'order' => $order,
            'quotation' => $quotation,
            'order_parts' => $this->orderPartModel->getOrderParts($id),
            'shop_info' => [
                'name' => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone' => get_site_setting('contact_phone', ''),
                'email' => get_site_setting('contact_email', ''),
            ]
        ];

        return view('admin/orders/quotation', $data);
    }

    /**
     * Send quotation to customer
     */
    public function sendQuotation($orderId, $quotationId)
    {
        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Get complete quotation data with order details
        $quotationWithDetails = $this->quotationModel->getQuotationByOrder($orderId);

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

            return redirect()->back()->with('success', 'Quotation sent to customer successfully');
        }

        return redirect()->back()->with('error', 'Failed to send quotation email');
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
     * Get service rates for device type
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
                'battery_replacement' => 120000
            ],
            2 => [ // Desktop
                'diagnosis' => 50000,
                'software_repair' => 80000,
                'hardware_repair' => 120000,
                'component_replacement' => 100000,
                'system_upgrade' => 150000
            ],
            3 => [ // Phone
                'diagnosis' => 30000,
                'software_repair' => 80000,
                'screen_replacement' => 250000,
                'battery_replacement' => 150000,
                'charging_port_repair' => 100000
            ]
        ];

        return $serviceRates[$deviceTypeId] ?? [
            'diagnosis' => 50000,
            'basic_repair' => 100000,
            'advanced_repair' => 200000
        ];
    }
}