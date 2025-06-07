<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RepairOrderModel;
use App\Models\CustomerModel;
use App\Models\DeviceTypeModel;
use App\Models\UserModel;
use App\Models\OrderStatusHistoryModel;
use App\Models\QuotationModel;
use App\Models\PartModel;
use App\Models\OrderPartModel;
use App\Models\StockMovementModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class OrderController extends BaseController
{
    protected RepairOrderModel $orderModel;
    protected CustomerModel $customerModel;
    protected DeviceTypeModel $deviceTypeModel;
    protected UserModel $userModel;
    protected OrderStatusHistoryModel $historyModel;
    protected QuotationModel $quotationModel;
    protected PartModel $partModel;
    protected OrderPartModel $orderPartModel;
    protected StockMovementModel $stockMovementModel;

    public function __construct()
    {
        $this->orderModel = new RepairOrderModel();
        $this->customerModel = new CustomerModel();
        $this->deviceTypeModel = new DeviceTypeModel();
        $this->userModel = new UserModel();
        $this->historyModel = new OrderStatusHistoryModel();
        $this->quotationModel = new QuotationModel();
        $this->partModel = new PartModel();
        $this->orderPartModel = new OrderPartModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    // ============================================================================
    // CRUD OPERATIONS
    // ============================================================================

    /**
     * Display list of orders with filtering and pagination
     */
    public function index(): string
    {
        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');
        $priority = $this->request->getGet('priority');
        $technician = $this->request->getGet('technician');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $builder = $this->orderModel->select('
                repair_orders.*,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                customers.email as customer_email,
                device_types.name as device_type_name,
                users.full_name as technician_name
            ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = repair_orders.technician_id', 'left');

        // Apply filters
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

        if ($priority) {
            $builder->where('repair_orders.priority', $priority);
        }

        if ($technician) {
            $builder->where('repair_orders.technician_id', $technician);
        }

        if ($dateFrom) {
            $builder->where('DATE(repair_orders.created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(repair_orders.created_at) <=', $dateTo);
        }

        $orders = $builder->orderBy('repair_orders.created_at', 'DESC')
            ->paginate($perPage);

        $data = [
            'title' => 'Order Management',
            'orders' => $orders,
            'pager' => $this->orderModel->pager,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'priority' => $priority,
                'technician' => $technician,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ],
            // Individual filter variables for backward compatibility with views
            'search' => $search,
            'current_status' => $status,
            'current_priority' => $priority,
            'current_technician' => $technician,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'statuses' => $this->getStatusOptions(),
            'priorities' => $this->getPriorityOptions(),
            'technicians' => $this->userModel->getTechnicians(),
            'status_counts' => $this->getStatusCounts()
        ];

        return view('admin/orders/index', $data);
    }

    /**
     * Show order details
     */
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
        $statusHistory = $this->historyModel->getOrderHistory($id);

        // Get quotations
        $quotations = $this->quotationModel->where('order_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get available parts for adding
        $availableParts = $this->partModel->where('status', 'active')
            ->where('stock_quantity >', 0)
            ->findAll();

        // Get available technicians
        $technicians = $this->userModel->getTechnicians();

        $data = [
            'title' => 'Order Details - #' . $order['order_number'],
            'order' => $order,
            'order_parts' => $orderParts,
            'status_history' => $statusHistory,
            'quotations' => $quotations,
            'available_parts' => $availableParts,
            'technicians' => $technicians,
            'statuses' => $this->getStatusOptions(),
            'priorities' => $this->getPriorityOptions()
        ];

        return view('admin/orders/show', $data);
    }

    /**
     * Show create order form
     */
    public function create(): string
    {
        $data = [
            'title' => 'Create New Order',
            'customers' => $this->customerModel->orderBy('full_name', 'ASC')->findAll(),
            'device_types' => $this->deviceTypeModel->where('status', 'active')->findAll(),
            'technicians' => $this->userModel->getTechnicians(),
            'priorities' => $this->getPriorityOptions()
        ];

        return view('admin/orders/create', $data);
    }

    /**
     * Store new order
     */
    public function store(): RedirectResponse
    {
        $rules = [
            'customer_id' => 'required|integer|is_not_unique[customers.id]',
            'device_type_id' => 'required|integer|is_not_unique[device_types.id]',
            'device_brand' => 'required|min_length[2]|max_length[100]',
            'device_model' => 'required|min_length[2]|max_length[100]',
            'device_serial' => 'permit_empty|max_length[100]',
            'problem_description' => 'required|min_length[10]',
            'accessories' => 'permit_empty',
            'technician_id' => 'permit_empty|integer|is_not_unique[users.id]',
            'priority' => 'required|in_list[low,normal,high,urgent]', // Changed medium to normal
            'notes' => 'permit_empty'
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Order validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Debug: Log the POST data
        log_message('debug', 'Order creation POST data: ' . json_encode($this->request->getPost()));

        $data = [
            'customer_id' => $this->request->getPost('customer_id'),
            'device_type_id' => $this->request->getPost('device_type_id'),
            'device_brand' => $this->request->getPost('device_brand'),
            'device_model' => $this->request->getPost('device_model'),
            'device_serial' => $this->request->getPost('device_serial'),
            'problem_description' => $this->request->getPost('problem_description'),
            'accessories' => $this->request->getPost('accessories'),
            'technician_id' => $this->request->getPost('technician_id') ?: null,
            'priority' => $this->request->getPost('priority'),
            'status' => 'received',
            'notes' => $this->request->getPost('notes')
        ];

        // Debug: Log the data being inserted
        log_message('debug', 'Order data to insert: ' . json_encode($data));

        try {
            // Use insert with return ID
            $orderId = $this->orderModel->insert($data, true);

            if ($orderId) {
                log_message('info', "Order created successfully with ID: {$orderId}");

                // Log initial status
                $this->historyModel->addStatusChange(
                    $orderId,
                    null,
                    'received',
                    'Order created',
                    session()->get('user_id')
                );

                // Send notification to customer
                $this->sendOrderCreatedNotification($orderId);

                return redirect()->to("/admin/orders/{$orderId}")
                    ->with('success', 'Order created successfully');
            } else {
                log_message('error', 'Order insert returned false/null');
                log_message('error', 'Database errors: ' . json_encode($this->orderModel->errors()));

                return redirect()->back()->withInput()
                    ->with('error', 'Failed to create order. Please check all required fields.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Exception during order creation: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()->withInput()
                ->with('error', 'Database error: ' . $e->getMessage());
        }
    }

    /**
     * Show edit order form
     */
    public function edit($id): string
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $data = [
            'title' => 'Edit Order - #' . $order['order_number'],
            'order' => $order,
            'customers' => $this->customerModel->orderBy('full_name', 'ASC')->findAll(),
            'device_types' => $this->deviceTypeModel->where('status', 'active')->findAll(),
            'technicians' => $this->userModel->getTechnicians(),
            'priorities' => $this->getPriorityOptions()
        ];

        return view('admin/orders/edit', $data);
    }

    /**
     * Update order
     */
    public function update($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Cannot edit completed or cancelled orders
        if (in_array($order['status'], ['completed', 'cancelled', 'delivered'])) {
            return redirect()->back()->with('error', 'Cannot edit order in current status');
        }

        $rules = [
            'customer_id' => 'required|integer|is_not_unique[customers.id]',
            'device_type_id' => 'required|integer|is_not_unique[device_types.id]',
            'device_brand' => 'required|min_length[2]|max_length[100]',
            'device_model' => 'required|min_length[2]|max_length[100]',
            'device_serial' => 'permit_empty|max_length[100]',
            'problem_description' => 'required|min_length[10]',
            'accessories' => 'permit_empty',
            'technician_id' => 'permit_empty|integer|is_not_unique[users.id]',
            'priority' => 'required|in_list[low,medium,high,urgent]',
            'notes' => 'permit_empty'
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
            'technician_id' => $this->request->getPost('technician_id') ?: null,
            'priority' => $this->request->getPost('priority'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->orderModel->update($id, $data)) {
            // Log technician assignment if changed
            if ($order['technician_id'] != $data['technician_id']) {
                $technician = $this->userModel->find($data['technician_id']);
                $note = $technician ? "Assigned to: {$technician['full_name']}" : "Technician unassigned";

                $this->historyModel->addStatusChange(
                    $id,
                    $order['status'],
                    $order['status'],
                    $note,
                    session()->get('user_id')
                );
            }

            return redirect()->to("/admin/orders/{$id}")
                ->with('success', 'Order updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update order');
    }

    /**
     * Delete order
     */
    public function delete($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if order can be deleted
        if (in_array($order['status'], ['in_progress', 'completed', 'delivered'])) {
            return redirect()->back()->with('error', 'Cannot delete order in current status');
        }

        // Return parts to stock
        $this->returnOrderParts($id);

        if ($this->orderModel->delete($id)) {
            return redirect()->to('/admin/orders')
                ->with('success', 'Order deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete order');
    }

    // ============================================================================
    // STATUS MANAGEMENT
    // ============================================================================

    /**
     * Update order status with validation
     */
    public function updateStatus($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes') ?? '';

        // Validate status transition
        $errors = $this->validateStatusTransition($order['status'], $newStatus);
        if (!empty($errors)) {
            return redirect()->back()->with('error', implode('. ', $errors));
        }

        // Validate quotation workflow if needed
        $quotationErrors = $this->validateQuotationWorkflow($id, $newStatus);
        if (!empty($quotationErrors)) {
            return redirect()->back()->with('error', implode('. ', $quotationErrors));
        }

        // Prepare update data
        $updateData = [
            'status' => $newStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle specific status changes
        $this->handleStatusSpecificUpdates($order, $newStatus, $updateData);

        // Update order
        if ($this->orderModel->update($id, $updateData)) {
            // Log status change
            $this->historyModel->addStatusChange(
                $id,
                $order['status'],
                $newStatus,
                $notes,
                session()->get('user_id')
            );

            // Send notifications
            $this->sendStatusUpdateNotification($order, $newStatus, $notes);

            // Handle additional status-specific actions
            $this->handlePostStatusUpdateActions($order, $newStatus);

            return redirect()->to("/admin/orders/{$id}")
                ->with('success', 'Status updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update status');
    }

    /**
     * Bulk status update
     */
    public function bulkUpdateStatus(): RedirectResponse
    {
        $orderIds = $this->request->getPost('order_ids');
        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes') ?? '';

        if (empty($orderIds) || !is_array($orderIds)) {
            return redirect()->back()->with('error', 'Please select orders to update');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($orderIds as $orderId) {
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                $errorCount++;
                continue;
            }

            // Validate transition
            $errors = $this->validateStatusTransition($order['status'], $newStatus);
            if (!empty($errors)) {
                $errorCount++;
                continue;
            }

            // Update order
            if ($this->orderModel->update($orderId, ['status' => $newStatus, 'updated_at' => date('Y-m-d H:i:s')])) {
                // Log status change
                $this->historyModel->addStatusChange(
                    $orderId,
                    $order['status'],
                    $newStatus,
                    $notes . ' (Bulk update)',
                    session()->get('user_id')
                );

                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $message = "Updated {$successCount} orders successfully";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} failed";
        }

        return redirect()->back()->with('success', $message);
    }

    // ============================================================================
    // DIAGNOSIS INTEGRATION (Redirect to DiagnosisController)
    // ============================================================================

    /**
     * Redirect to diagnosis - handled by DiagnosisController
     */
    public function redirectToDiagnosis($orderId): RedirectResponse
    {
        return redirect()->to("/admin/diagnosis/{$orderId}");
    }

    /**
     * Redirect to start diagnosis - handled by DiagnosisController
     */
    public function redirectToStartDiagnosis($orderId): RedirectResponse
    {
        return redirect()->to("/admin/diagnosis/{$orderId}/start");
    }

    // ============================================================================
    // PARTS MANAGEMENT
    // ============================================================================

    /**
     * Manage parts for order
     */
    public function manageParts($id): string
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $orderParts = $this->orderPartModel->getOrderParts($id);
        $availableParts = $this->partModel->where('status', 'active')
            ->where('stock_quantity >', 0)
            ->findAll();

        $data = [
            'title' => 'Manage Parts - Order #' . $order['order_number'],
            'order' => $order,
            'order_parts' => $orderParts,
            'available_parts' => $availableParts
        ];

        return view('admin/orders/manage_parts', $data);
    }

    /**
     * Add part to order
     */
    public function addPart($orderId): RedirectResponse
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'part_id' => 'required|integer|is_not_unique[parts.id]',
            'quantity' => 'required|integer|greater_than[0]',
            'unit_price' => 'required|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $partId = $this->request->getPost('part_id');
        $quantity = (int)$this->request->getPost('quantity');
        $unitPrice = (float)$this->request->getPost('unit_price');

        // Check stock availability
        $part = $this->partModel->find($partId);
        if (!$part || $part['stock_quantity'] < $quantity) {
            return redirect()->back()->with('error', 'Insufficient stock for this part');
        }

        // Add part to order
        $orderPartData = [
            'order_id' => $orderId,
            'part_id' => $partId,
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_price' => $quantity * $unitPrice
        ];

        if ($insertResult = $this->orderPartModel->insert($orderPartData, true)) {
            // Update stock
            $newStock = $part['stock_quantity'] - $quantity;
            if ($this->partModel->update($partId, ['stock_quantity' => $newStock, 'updated_at' => date('Y-m-d H:i:s')])) {
                // Record stock movement
                $this->stockMovementModel->recordMovement([
                    'part_id' => $partId,
                    'movement_type' => 'used',
                    'quantity_before' => $part['stock_quantity'],
                    'quantity_change' => -$quantity,
                    'quantity_after' => $newStock,
                    'reference_type' => 'order',
                    'reference_id' => $orderId,
                    'unit_cost' => $unitPrice,
                    'notes' => "Used in order #{$order['order_number']}",
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

    // ============================================================================
    // QUOTATION INTEGRATION (Redirect to QuotationController)
    // ============================================================================

    /**
     * Redirect to quotation creation - handled by QuotationController
     */
    public function redirectToCreateQuotation($orderId): RedirectResponse
    {
        return redirect()->to("/admin/orders/{$orderId}/create-quotation");
    }

    /**
     * Redirect to quotation view - handled by QuotationController
     */
    public function redirectToViewQuotation($orderId): RedirectResponse
    {
        return redirect()->to("/admin/orders/{$orderId}/quotation");
    }

    // ============================================================================
    // REPORTING & ANALYTICS
    // ============================================================================

    /**
     * Generate order receipt
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

        $orderParts = $this->orderPartModel->getOrderParts($id);

        $data = [
            'title' => 'Receipt - Order #' . $order['order_number'],
            'order' => $order,
            'order_parts' => $orderParts,
            'print_mode' => true
        ];

        return view('admin/orders/receipt', $data);
    }

    /**
     * Export orders to CSV
     */
    public function exportCsv(): ResponseInterface
    {
        $status = $this->request->getGet('status');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $builder = $this->orderModel->select('
                repair_orders.order_number,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                customers.email as customer_email,
                device_types.name as device_type,
                repair_orders.device_brand,
                repair_orders.device_model,
                repair_orders.status,
                repair_orders.priority,
                repair_orders.final_cost,
                users.full_name as technician_name,
                repair_orders.created_at,
                repair_orders.completed_at
            ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = repair_orders.technician_id', 'left');

        if ($status) {
            $builder->where('repair_orders.status', $status);
        }

        if ($dateFrom) {
            $builder->where('DATE(repair_orders.created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(repair_orders.created_at) <=', $dateTo);
        }

        $orders = $builder->orderBy('repair_orders.created_at', 'DESC')->findAll();

        // Generate CSV content
        $csvContent = "Order Number,Customer Name,Phone,Email,Device Type,Brand,Model,Status,Priority,Final Cost,Technician,Created Date,Completed Date\n";

        foreach ($orders as $order) {
            $csvContent .= sprintf('"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $order['order_number'],
                $order['customer_name'],
                $order['customer_phone'],
                $order['customer_email'] ?? '',
                $order['device_type'],
                $order['device_brand'],
                $order['device_model'],
                $order['status'],
                $order['priority'],
                $order['final_cost'] ?? '0',
                $order['technician_name'] ?? '',
                $order['created_at'],
                $order['completed_at'] ?? ''
            );
        }

        $filename = 'orders_export_' . date('Y-m-d_H-i-s') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csvContent);
    }

    /**
     * Get order statistics
     */
    public function getStatistics(): array
    {
        $today = date('Y-m-d');
        $thisMonth = date('Y-m');
        $thisYear = date('Y');

        return [
            'total_orders' => $this->orderModel->countAllResults(),
            'today_orders' => $this->orderModel->where('DATE(created_at)', $today)->countAllResults(),
            'month_orders' => $this->orderModel->where('DATE_FORMAT(created_at, "%Y-%m")', $thisMonth)->countAllResults(),
            'year_orders' => $this->orderModel->where('DATE_FORMAT(created_at, "%Y")', $thisYear)->countAllResults(),
            'pending_orders' => $this->orderModel->whereIn('status', ['received', 'diagnosed', 'waiting_approval'])->countAllResults(),
            'in_progress_orders' => $this->orderModel->where('status', 'in_progress')->countAllResults(),
            'completed_orders' => $this->orderModel->where('status', 'completed')->countAllResults(),
            'revenue_this_month' => $this->orderModel->selectSum('final_cost')
                    ->where('status', 'completed')
                    ->where('DATE_FORMAT(completed_at, "%Y-%m")', $thisMonth)
                    ->get()->getRow()->final_cost ?? 0
        ];
    }

    // ============================================================================
    // AJAX ENDPOINTS
    // ============================================================================

    /**
     * Get order data for AJAX requests
     */
    public function ajaxGetOrder($id): ResponseInterface
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            return $this->response->setJSON(['error' => 'Order not found'])->setStatusCode(404);
        }

        return $this->response->setJSON($order);
    }

    /**
     * Search orders for autocomplete
     */
    public function ajaxSearchOrders(): ResponseInterface
    {
        $term = $this->request->getGet('term');
        $limit = $this->request->getGet('limit') ?? 10;

        if (!$term) {
            return $this->response->setJSON([]);
        }

        $orders = $this->orderModel->select('
                repair_orders.id,
                repair_orders.order_number,
                customers.full_name as customer_name,
                repair_orders.device_brand,
                repair_orders.device_model,
                repair_orders.status
            ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->groupStart()
            ->like('repair_orders.order_number', $term)
            ->orLike('customers.full_name', $term)
            ->orLike('repair_orders.device_brand', $term)
            ->orLike('repair_orders.device_model', $term)
            ->groupEnd()
            ->limit($limit)
            ->findAll();

        return $this->response->setJSON($orders);
    }

    /**
     * Update order priority via AJAX
     */
    public function ajaxUpdatePriority($id): ResponseInterface
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            return $this->response->setJSON(['error' => 'Order not found'])->setStatusCode(404);
        }

        $priority = $this->request->getJSON(true)['priority'] ?? null;

        if (!in_array($priority, ['low', 'normal', 'high', 'urgent'])) { // Changed medium to normal
            return $this->response->setJSON(['error' => 'Invalid priority'])->setStatusCode(400);
        }

        if ($this->orderModel->update($id, ['priority' => $priority])) {
            // Log priority change
            $this->historyModel->addStatusChange(
                $id,
                $order['status'],
                $order['status'],
                "Priority changed to: {$priority}",
                session()->get('user_id')
            );

            return $this->response->setJSON(['success' => true, 'priority' => $priority]);
        }

        return $this->response->setJSON(['error' => 'Failed to update priority'])->setStatusCode(500);
    }

    // ============================================================================
    // HELPER METHODS
    // ============================================================================

    /**
     * Validate status transition
     */
    private function validateStatusTransition($currentStatus, $newStatus): array
    {
        $errors = [];

        $allowedTransitions = [
            'received' => ['diagnosed', 'cancelled'],
            'diagnosed' => ['waiting_approval', 'in_progress', 'cancelled'],
            'waiting_approval' => ['approved', 'cancelled'],
            'approved' => ['in_progress', 'cancelled'],
            'in_progress' => ['waiting_parts', 'completed', 'cancelled'],
            'waiting_parts' => ['in_progress', 'cancelled'],
            'completed' => ['delivered'],
            'delivered' => [],
            'cancelled' => []
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            $errors[] = "Invalid current status: {$currentStatus}";
        } elseif (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            $errors[] = "Cannot change status from {$currentStatus} to {$newStatus}";
        }

        return $errors;
    }

    /**
     * Validate quotation workflow - simplified version
     */
    private function validateQuotationWorkflow($orderId, $newStatus): array
    {
        $errors = [];

        // Only check if quotation exists and status for critical transitions
        $quotation = $this->quotationModel->where('order_id', $orderId)->first();

        switch ($newStatus) {
            case 'approved':
                // Must have approved quotation
                if (!$quotation || $quotation['status'] !== 'approved') {
                    $errors[] = 'Cannot set to approved: Quotation must be approved by customer first';
                }
                break;

            case 'in_progress':
                // For orders that need quotation, must be approved first
                if ($quotation && $quotation['status'] !== 'approved') {
                    $errors[] = 'Cannot start work: Quotation must be approved first';
                }
                break;
        }

        return $errors;
    }

    /**
     * Handle status-specific updates
     */
    private function handleStatusSpecificUpdates($order, $newStatus, &$updateData): void
    {
        switch ($newStatus) {
            case 'completed':
                if (!$order['completed_at']) {
                    $updateData['completed_at'] = date('Y-m-d H:i:s');
                }

                if (!$order['final_cost']) {
                    $quotation = $this->quotationModel->where('order_id', $order['id'])->first();
                    if ($quotation && $quotation['status'] === 'approved') {
                        $updateData['final_cost'] = $quotation['total_cost'];
                    }
                }
                break;

            case 'in_progress':
                if (!$order['work_started_at']) {
                    $updateData['work_started_at'] = date('Y-m-d H:i:s');
                }

                $quotation = $this->quotationModel->where('order_id', $order['id'])->first();
                if ($quotation && $quotation['status'] === 'approved' && !$order['final_cost']) {
                    $updateData['final_cost'] = $quotation['total_cost'];
                }
                break;

            case 'cancelled':
                $updateData['cancelled_at'] = date('Y-m-d H:i:s');
                $updateData['cancellation_reason'] = $this->request->getPost('notes');
                break;
        }
    }

    /**
     * Handle post-status update actions
     */
    private function handlePostStatusUpdateActions($order, $newStatus): void
    {
        switch ($newStatus) {
            case 'cancelled':
                $this->returnOrderParts($order['id']);
                break;

            case 'completed':
                // Could trigger automatic invoice generation
                // $this->generateInvoice($order['id']);
                break;
        }
    }

    /**
     * Return all parts to stock
     */
    private function returnOrderParts($orderId): bool
    {
        try {
            $orderParts = $this->orderPartModel->getOrderParts($orderId);

            foreach ($orderParts as $orderPart) {
                $part = $this->partModel->find($orderPart['part_id']);
                if ($part) {
                    $newStock = $part['stock_quantity'] + $orderPart['quantity'];

                    $this->partModel->update($part['id'], [
                        'stock_quantity' => $newStock,
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);

                    $this->stockMovementModel->recordMovement([
                        'part_id' => $part['id'],
                        'movement_type' => 'return',
                        'quantity_before' => $part['stock_quantity'],
                        'quantity_change' => $orderPart['quantity'],
                        'quantity_after' => $newStock,
                        'reference_type' => 'order_cancelled',
                        'reference_id' => $orderId,
                        'unit_cost' => $orderPart['unit_price'],
                        'notes' => "Returned due to order cancellation",
                        'created_by' => session()->get('user_id')
                    ]);
                }
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to return parts for cancelled order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send order created notification
     */
    private function sendOrderCreatedNotification($orderId): bool
    {
        try {
            $order = $this->orderModel->select('
                    repair_orders.*,
                    customers.full_name as customer_name,
                    customers.email as customer_email
                ')
                ->join('customers', 'customers.id = repair_orders.customer_id')
                ->where('repair_orders.id', $orderId)
                ->first();

            if (!$order || !$order['customer_email']) {
                return false;
            }

            $email = \Config\Services::email();

            $subject = "Order Confirmation - #{$order['order_number']}";
            $message = view('emails/order_created', [
                'order' => $order
            ]);

            $email->setTo($order['customer_email']);
            $email->setSubject($subject);
            $email->setMessage($message);

            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send order created notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send status update notification
     */
    private function sendStatusUpdateNotification($order, $newStatus, $notes): bool
    {
        try {
            $customerData = $this->customerModel->find($order['customer_id']);
            if (!$customerData || !$customerData['email']) {
                return false;
            }

            $statusMessages = [
                'diagnosed' => 'Your device has been diagnosed. A quotation will be sent shortly.',
                'waiting_approval' => 'Please review and approve the quotation to proceed with the repair.',
                'approved' => 'Thank you for approving the quotation. Work will begin shortly.',
                'in_progress' => 'Your device repair is now in progress.',
                'waiting_parts' => 'We are waiting for parts to arrive to complete your repair.',
                'completed' => 'Great news! Your device repair has been completed and is ready for pickup.',
                'delivered' => 'Your device has been delivered. Thank you for choosing our service!',
                'cancelled' => 'Your repair order has been cancelled.'
            ];

            if (!isset($statusMessages[$newStatus])) {
                return false;
            }

            $email = \Config\Services::email();

            $subject = "Order Update - #{$order['order_number']}";
            $message = view('emails/order_status_update', [
                'order' => $order,
                'customer' => $customerData,
                'status_message' => $statusMessages[$newStatus],
                'new_status' => $newStatus,
                'notes' => $notes
            ]);

            $email->setTo($customerData['email']);
            $email->setSubject($subject);
            $email->setMessage($message);

            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send status notification: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send quotation to customer - delegated to QuotationController
     */
    private function sendQuotationToCustomer($orderId, $quotationId): bool
    {
        // This should be handled by QuotationController
        // Just return true for now as the actual sending is handled elsewhere
        return true;
    }

    /**
     * Generate quotation number - moved to QuotationController
     */
    private function generateQuotationNumber(): string
    {
        // This is now handled by QuotationController
        // Keeping for backward compatibility
        $prefix = 'QT';
        $date = date('Ymd');
        $count = $this->quotationModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults() + 1;

        return $prefix . $date . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get status options
     */
    private function getStatusOptions(): array
    {
        return [
            'received' => 'Received',
            'diagnosed' => 'Diagnosed',
            'waiting_approval' => 'Waiting Approval',
            'approved' => 'Approved',
            'in_progress' => 'In Progress',
            'waiting_parts' => 'Waiting Parts',
            'completed' => 'Completed',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];
    }

    /**
     * Get priority options
     */
    private function getPriorityOptions(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal', // Changed from medium to normal
            'high' => 'High',
            'urgent' => 'Urgent'
        ];
    }

    /**
     * Get status counts for dashboard
     */
    private function getStatusCounts(): array
    {
        $statuses = array_keys($this->getStatusOptions());
        $counts = [];

        foreach ($statuses as $status) {
            $counts[$status] = $this->orderModel->where('status', $status)->countAllResults();
        }

        return $counts;
    }

    /**
     * Get orders by status with pagination
     */
    public function getOrdersByStatus($status, $page = 1, $perPage = 10): array
    {
        $validStatuses = array_keys($this->getStatusOptions());

        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException('Invalid status');
        }

        return $this->orderModel->select('
                repair_orders.*,
                customers.full_name as customer_name,
                device_types.name as device_type_name,
                users.full_name as technician_name
            ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = repair_orders.technician_id', 'left')
            ->where('repair_orders.status', $status)
            ->orderBy('repair_orders.created_at', 'DESC')
            ->paginate($perPage, 'default', $page);
    }

    // ============================================================================
    // ASSIGNMENT METHODS
    // ============================================================================

    /**
     * Assign technician to order
     */
    public function assignTechnician($id): RedirectResponse
    {
        $order = $this->orderModel->find($id);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $technicianId = $this->request->getPost('technician_id');

        if (!$technicianId) {
            return redirect()->back()->with('error', 'Please select a technician');
        }

        // Validate technician exists
        $technician = $this->userModel->find($technicianId);
        if (!$technician || $technician['role'] !== 'technician') {
            return redirect()->back()->with('error', 'Invalid technician selected');
        }

        if ($this->orderModel->update($id, ['technician_id' => $technicianId])) {
            // Log assignment
            $this->historyModel->addStatusChange(
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
     * Bulk assign technician
     */
    public function bulkAssignTechnician(): RedirectResponse
    {
        $orderIds = $this->request->getPost('order_ids');
        $technicianId = $this->request->getPost('technician_id');

        if (empty($orderIds) || !is_array($orderIds)) {
            return redirect()->back()->with('error', 'Please select orders to assign');
        }

        if (!$technicianId) {
            return redirect()->back()->with('error', 'Please select a technician');
        }

        $technician = $this->userModel->find($technicianId);
        if (!$technician || $technician['role'] !== 'technician') {
            return redirect()->back()->with('error', 'Invalid technician selected');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($orderIds as $orderId) {
            $order = $this->orderModel->find($orderId);
            if (!$order) {
                $errorCount++;
                continue;
            }

            if ($this->orderModel->update($orderId, ['technician_id' => $technicianId])) {
                // Log assignment
                $this->historyModel->addStatusChange(
                    $orderId,
                    $order['status'],
                    $order['status'],
                    'Assigned to technician: ' . $technician['full_name'] . ' (Bulk assignment)',
                    session()->get('user_id')
                );

                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $message = "Assigned {$successCount} orders successfully";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} failed";
        }

        return redirect()->back()->with('success', $message);
    }
}