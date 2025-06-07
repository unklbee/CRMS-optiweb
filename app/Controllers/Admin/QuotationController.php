<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuotationModel;
use App\Models\RepairOrderModel;
use App\Models\OrderStatusHistoryModel;
use App\Models\OrderPartModel;
use App\Models\CustomerModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;

class QuotationController extends BaseController
{
    protected QuotationModel $quotationModel;
    protected RepairOrderModel $orderModel;
    protected OrderStatusHistoryModel $historyModel;
    protected OrderPartModel $orderPartModel;
    protected CustomerModel $customerModel;

    public function __construct()
    {
        $this->quotationModel = new QuotationModel();
        $this->orderModel = new RepairOrderModel();
        $this->historyModel = new OrderStatusHistoryModel();
        $this->orderPartModel = new OrderPartModel();
        $this->customerModel = new CustomerModel();
    }

    // ============================================================================
    // MAIN QUOTATION MANAGEMENT
    // ============================================================================

    /**
     * List all quotations with filtering
     */
    public function index(): string
    {
        $perPage = 20;
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $builder = $this->quotationModel->select('
                quotations.*,
                repair_orders.order_number,
                repair_orders.status as order_status,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                device_types.name as device_type_name,
                users.full_name as created_by_name
            ')
            ->join('repair_orders', 'repair_orders.id = quotations.order_id')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = quotations.created_by', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('quotations.quotation_number', $search)
                ->orLike('repair_orders.order_number', $search)
                ->orLike('customers.full_name', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('quotations.status', $status);
        }

        $quotations = $builder->orderBy('quotations.created_at', 'DESC')
            ->paginate($perPage);

        $stats = $this->quotationModel->getQuotationStats();

        $data = [
            'title' => 'Quotation Management',
            'quotations' => $quotations,
            'pager' => $this->quotationModel->pager,
            'search' => $search,
            'status' => $status,
            'stats' => $stats,
            'statuses' => [
                'draft' => 'Draft',
                'sent' => 'Sent',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'expired' => 'Expired',
                'superseded' => 'Superseded'
            ]
        ];

        return view('admin/quotations/index', $data);
    }

    /**
     * Show quotation details (admin view)
     */
    public function show($id): string
    {
        $quotation = $this->quotationModel->getQuotationWithOrderDetails($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Get order parts
        $orderParts = $this->orderPartModel->getOrderParts($quotation['order_id']);

        $data = [
            'title' => 'Quotation Details - ' . $quotation['quotation_number'],
            'quotation' => $quotation,
            'order_parts' => $orderParts,
            'print_mode' => $this->request->getGet('print') === '1'
        ];

        return view('admin/quotations/show', $data);
    }

    // ============================================================================
    // ORDER-BASED QUOTATION MANAGEMENT
    // ============================================================================

    /**
     * Create quotation form for specific order
     */
    public function create($orderId): string
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
            ->where('repair_orders.id', $orderId)
            ->first();

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if quotation already exists
        $existingQuotation = $this->quotationModel->where('order_id', $orderId)->first();
        if ($existingQuotation) {
            return redirect()->to("/admin/quotations/{$existingQuotation['id']}")
                ->with('info', 'Quotation already exists for this order');
        }

        // Get order parts for quotation calculation
        $orderParts = $this->orderPartModel->getOrderParts($orderId);

        $partsTotal = 0;
        foreach ($orderParts as $part) {
            $partsTotal += $part['total_price'];
        }

        $data = [
            'title' => 'Create Quotation - Order #' . $order['order_number'],
            'order' => $order,
            'order_parts' => $orderParts,
            'parts_total' => $partsTotal
        ];

        return view('admin/quotations/create', $data);
    }

    /**
     * Store new quotation for order
     */
    public function store($orderId): RedirectResponse
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'labor_cost' => 'required|decimal',
            'notes' => 'permit_empty',
            'valid_days' => 'permit_empty|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Calculate parts total
        $orderParts = $this->orderPartModel->getOrderParts($orderId);
        $partsTotal = 0;
        foreach ($orderParts as $part) {
            $partsTotal += $part['total_price'];
        }

        $laborCost = (float)$this->request->getPost('labor_cost');
        $totalCost = $partsTotal + $laborCost;
        $validDays = (int)($this->request->getPost('valid_days') ?? 7);

        $quotationData = [
            'order_id' => $orderId,
            'quotation_number' => $this->generateQuotationNumber(),
            'parts_cost' => $partsTotal,
            'labor_cost' => $laborCost,
            'total_cost' => $totalCost,
            'notes' => $this->request->getPost('notes'),
            'status' => 'draft',
            'valid_until' => date('Y-m-d', strtotime("+{$validDays} days")),
            'created_by' => session()->get('user_id')
        ];

        if ($quotationId = $this->quotationModel->insert($quotationData, true)) {
            return redirect()->to("/admin/quotations/{$quotationId}")
                ->with('success', 'Quotation created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create quotation');
    }

    /**
     * Show quotation for specific order
     */
    public function showOrderQuotation($orderId): string
    {
        $quotation = $this->quotationModel->where('order_id', $orderId)->first();

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found for this order');
        }

        return redirect()->to("/admin/quotations/{$quotation['id']}");
    }

    /**
     * Edit quotation for specific order
     */
    public function editOrderQuotation($orderId): string
    {
        $quotation = $this->quotationModel->where('order_id', $orderId)->first();

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found for this order');
        }

        return redirect()->to("/admin/quotations/{$quotation['id']}/edit");
    }

    /**
     * Edit quotation
     */
    public function edit($id): string
    {
        $quotation = $this->quotationModel->getQuotationWithOrderDetails($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Cannot edit approved or rejected quotations
        if (in_array($quotation['status'], ['approved', 'rejected'])) {
            return redirect()->to("/admin/quotations/{$id}")
                ->with('error', 'Cannot edit quotation in current status');
        }

        $orderParts = $this->orderPartModel->getOrderParts($quotation['order_id']);

        $data = [
            'title' => 'Edit Quotation - ' . $quotation['quotation_number'],
            'quotation' => $quotation,
            'order_parts' => $orderParts
        ];

        return view('admin/quotations/edit', $data);
    }

    /**
     * Update quotation
     */
    public function update($id): RedirectResponse
    {
        $quotation = $this->quotationModel->find($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Cannot edit approved or rejected quotations
        if (in_array($quotation['status'], ['approved', 'rejected'])) {
            return redirect()->back()->with('error', 'Cannot edit quotation in current status');
        }

        $rules = [
            'labor_cost' => 'required|decimal',
            'notes' => 'permit_empty',
            'valid_days' => 'permit_empty|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Recalculate totals
        $orderParts = $this->orderPartModel->getOrderParts($quotation['order_id']);
        $partsTotal = 0;
        foreach ($orderParts as $part) {
            $partsTotal += $part['total_price'];
        }

        $laborCost = (float)$this->request->getPost('labor_cost');
        $totalCost = $partsTotal + $laborCost;

        $updateData = [
            'parts_cost' => $partsTotal,
            'labor_cost' => $laborCost,
            'total_cost' => $totalCost,
            'notes' => $this->request->getPost('notes')
        ];

        // Update valid until if provided
        $validDays = $this->request->getPost('valid_days');
        if ($validDays) {
            $updateData['valid_until'] = date('Y-m-d', strtotime("+{$validDays} days"));
        }

        if ($this->quotationModel->update($id, $updateData)) {
            return redirect()->to("/admin/quotations/{$id}")
                ->with('success', 'Quotation updated successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to update quotation');
    }

    /**
     * Delete quotation
     */
    public function delete($id): RedirectResponse
    {
        $quotation = $this->quotationModel->find($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Cannot delete approved quotations
        if ($quotation['status'] === 'approved') {
            return redirect()->back()->with('error', 'Cannot delete approved quotation');
        }

        if ($this->quotationModel->delete($id)) {
            return redirect()->to('/admin/quotations')
                ->with('success', 'Quotation deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete quotation');
    }

    // ============================================================================
    // QUOTATION ACTIONS
    // ============================================================================

    /**
     * Send quotation to customer
     */
    public function send($id): RedirectResponse
    {
        $quotation = $this->quotationModel->getQuotationWithOrderDetails($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        if ($quotation['status'] !== 'draft') {
            return redirect()->back()->with('error', 'Only draft quotations can be sent');
        }

        if (!$quotation['customer_email']) {
            return redirect()->back()->with('error', 'Customer email not available');
        }

        // Send email
        if ($this->sendQuotationEmail($quotation)) {
            // Update status to sent
            $this->quotationModel->update($id, [
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s')
            ]);

            // Update order status
            $this->orderModel->update($quotation['order_id'], [
                'status' => 'waiting_approval'
            ]);

            // Log status change
            $this->historyModel->addStatusChange(
                $quotation['order_id'],
                $quotation['order_status'],
                'waiting_approval',
                'Quotation sent to customer for approval',
                session()->get('user_id')
            );

            return redirect()->back()->with('success', 'Quotation sent to customer successfully');
        }

        return redirect()->back()->with('error', 'Failed to send quotation email');
    }

    /**
     * Download quotation PDF
     */
    public function downloadPdf($id): ResponseInterface
    {
        $quotation = $this->quotationModel->getQuotationWithOrderDetails($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        $orderParts = $this->orderPartModel->getOrderParts($quotation['order_id']);

        // Generate PDF (using TCPDF or similar)
        $html = view('admin/quotations/pdf', [
            'quotation' => $quotation,
            'order_parts' => $orderParts,
            'shop_info' => [
                'name' => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone' => get_site_setting('contact_phone', ''),
                'email' => get_site_setting('contact_email', ''),
            ]
        ]);

        // For now, return HTML view - implement PDF generation as needed
        return $this->response
            ->setHeader('Content-Type', 'text/html')
            ->setBody($html);
    }

    /**
     * Duplicate quotation
     */
    public function duplicate($id): RedirectResponse
    {
        $quotation = $this->quotationModel->find($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Create duplicate data
        $duplicateData = $quotation;
        unset($duplicateData['id']);
        $duplicateData['status'] = 'draft';
        $duplicateData['quotation_number'] = $this->generateQuotationNumber();
        $duplicateData['sent_at'] = null;
        $duplicateData['responded_at'] = null;
        $duplicateData['customer_notes'] = null;
        $duplicateData['approved_by_customer'] = false;
        $duplicateData['created_by'] = session()->get('user_id');
        $duplicateData['created_at'] = date('Y-m-d H:i:s');
        $duplicateData['updated_at'] = date('Y-m-d H:i:s');

        $newQuotationId = $this->quotationModel->insert($duplicateData, true);

        if ($newQuotationId) {
            return redirect()->to("/admin/quotations/{$newQuotationId}")
                ->with('success', 'Quotation duplicated successfully');
        }

        return redirect()->back()->with('error', 'Failed to duplicate quotation');
    }

    /**
     * Send reminder to customer
     */
    public function sendReminder($id): RedirectResponse
    {
        $quotation = $this->quotationModel->getQuotationWithOrderDetails($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        if ($quotation['status'] !== 'sent') {
            return redirect()->back()->with('error', 'Can only send reminders for sent quotations');
        }

        if (!$quotation['customer_email']) {
            return redirect()->back()->with('error', 'Customer email not available');
        }

        // Send reminder email
        if ($this->sendReminderEmail($quotation)) {
            return redirect()->back()->with('success', 'Reminder sent to customer successfully');
        }

        return redirect()->back()->with('error', 'Failed to send reminder email');
    }

    /**
     * Mark quotation as expired
     */
    public function markExpired($id): RedirectResponse
    {
        $quotation = $this->quotationModel->find($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        if ($this->quotationModel->update($id, ['status' => 'expired'])) {
            // Log status change
            $this->historyModel->addStatusChange(
                $quotation['order_id'],
                'waiting_approval',
                'received',
                'Quotation expired - customer did not respond within valid period',
                session()->get('user_id')
            );

            // Update order status back to received
            $this->orderModel->update($quotation['order_id'], [
                'status' => 'received',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            return redirect()->back()->with('success', 'Quotation marked as expired');
        }

        return redirect()->back()->with('error', 'Failed to mark quotation as expired');
    }

    /**
     * Revise quotation (create new version)
     */
    public function reviseQuotation($orderId): RedirectResponse
    {
        $existingQuotation = $this->quotationModel->where('order_id', $orderId)->first();

        if (!$existingQuotation) {
            throw new PageNotFoundException('Original quotation not found');
        }

        // Mark existing quotation as superseded
        $this->quotationModel->update($existingQuotation['id'], ['status' => 'superseded']);

        // Redirect to create new quotation
        return redirect()->to("/admin/orders/{$orderId}/create-quotation")
            ->with('info', 'Previous quotation superseded. Create new revision.');
    }

    // ============================================================================
    // SPECIALIZED VIEWS
    // ============================================================================

    /**
     * Show pending quotations
     */
    public function pending(): string
    {
        $quotations = $this->quotationModel->select('
                quotations.*,
                repair_orders.order_number,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                device_types.name as device_type_name
            ')
            ->join('repair_orders', 'repair_orders.id = quotations.order_id')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->where('quotations.status', 'sent')
            ->where('quotations.valid_until >=', date('Y-m-d'))
            ->orderBy('quotations.sent_at', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Pending Quotations',
            'quotations' => $quotations,
            'page_type' => 'pending'
        ];

        return view('admin/quotations/pending', $data);
    }

    /**
     * Show expired quotations
     */
    public function expired(): string
    {
        $quotations = $this->quotationModel->select('
                quotations.*,
                repair_orders.order_number,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                device_types.name as device_type_name
            ')
            ->join('repair_orders', 'repair_orders.id = quotations.order_id')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->where('quotations.status', 'sent')
            ->where('quotations.valid_until <', date('Y-m-d'))
            ->orderBy('quotations.valid_until', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Expired Quotations',
            'quotations' => $quotations,
            'page_type' => 'expired'
        ];

        return view('admin/quotations/expired', $data);
    }

    /**
     * Show quotation analytics
     */
    public function analytics(): string
    {
        $stats = $this->quotationModel->getQuotationStats();

        // Calculate conversion rates
        $totalSent = $stats['sent'];
        $totalApproved = $stats['approved'];
        $totalRejected = $stats['rejected'];

        $conversionRate = $totalSent > 0 ? ($totalApproved / $totalSent) * 100 : 0;
        $rejectionRate = $totalSent > 0 ? ($totalRejected / $totalSent) * 100 : 0;

        // Get average quotation value
        $avgQuotationValue = $this->quotationModel->selectAvg('total_cost', 'avg_value')
            ->where('status !=', 'draft')
            ->first()['avg_value'] ?? 0;

        // Get monthly data for charts
        $monthlyData = $this->quotationModel->select('
                MONTH(created_at) as month,
                YEAR(created_at) as year,
                COUNT(*) as total_quotations,
                SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_count,
                SUM(CASE WHEN status = "approved" THEN total_cost ELSE 0 END) as approved_value
            ')
            ->where('created_at >=', date('Y-01-01'))
            ->groupBy('YEAR(created_at), MONTH(created_at)')
            ->orderBy('year, month')
            ->findAll();

        $data = [
            'title' => 'Quotation Analytics',
            'stats' => $stats,
            'conversion_rate' => round($conversionRate, 2),
            'rejection_rate' => round($rejectionRate, 2),
            'avg_quotation_value' => $avgQuotationValue,
            'monthly_data' => $monthlyData
        ];

        return view('admin/quotations/analytics', $data);
    }

    /**
     * Export quotations to CSV
     */
    public function export(): ResponseInterface
    {
        $status = $this->request->getGet('status');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $builder = $this->quotationModel->select('
                quotations.quotation_number,
                repair_orders.order_number,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                customers.email as customer_email,
                quotations.parts_cost,
                quotations.labor_cost,
                quotations.total_cost,
                quotations.status,
                quotations.created_at,
                quotations.sent_at,
                quotations.responded_at,
                quotations.valid_until
            ')
            ->join('repair_orders', 'repair_orders.id = quotations.order_id')
            ->join('customers', 'customers.id = repair_orders.customer_id');

        if ($status) {
            $builder->where('quotations.status', $status);
        }

        if ($dateFrom) {
            $builder->where('DATE(quotations.created_at) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(quotations.created_at) <=', $dateTo);
        }

        $quotations = $builder->orderBy('quotations.created_at', 'DESC')->findAll();

        // Generate CSV
        $csvContent = "Quotation Number,Order Number,Customer Name,Phone,Email,Parts Cost,Labor Cost,Total Cost,Status,Created Date,Sent Date,Responded Date,Valid Until\n";

        foreach ($quotations as $quotation) {
            $csvContent .= sprintf('"%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
                $quotation['quotation_number'],
                $quotation['order_number'],
                $quotation['customer_name'],
                $quotation['customer_phone'],
                $quotation['customer_email'] ?? '',
                $quotation['parts_cost'],
                $quotation['labor_cost'],
                $quotation['total_cost'],
                $quotation['status'],
                $quotation['created_at'],
                $quotation['sent_at'] ?? '',
                $quotation['responded_at'] ?? '',
                $quotation['valid_until']
            );
        }

        $filename = 'quotations_export_' . date('Y-m-d_H-i-s') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csvContent);
    }

    /**
     * Bulk actions for quotations
     */
    public function bulkAction(): RedirectResponse
    {
        $quotationIds = $this->request->getPost('quotation_ids');
        $action = $this->request->getPost('action');

        if (empty($quotationIds) || !is_array($quotationIds)) {
            return redirect()->back()->with('error', 'Please select quotations to process');
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($quotationIds as $quotationId) {
            $quotation = $this->quotationModel->find($quotationId);
            if (!$quotation) {
                $errorCount++;
                continue;
            }

            switch ($action) {
                case 'mark_expired':
                    if ($quotation['status'] === 'sent') {
                        if ($this->quotationModel->update($quotationId, ['status' => 'expired'])) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    } else {
                        $errorCount++;
                    }
                    break;

                case 'delete':
                    if ($quotation['status'] !== 'approved') {
                        if ($this->quotationModel->delete($quotationId)) {
                            $successCount++;
                        } else {
                            $errorCount++;
                        }
                    } else {
                        $errorCount++;
                    }
                    break;

                default:
                    $errorCount++;
                    break;
            }
        }

        $message = "Processed {$successCount} quotations successfully";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} failed";
        }

        return redirect()->back()->with('success', $message);
    }

    // ============================================================================
    // HELPER METHODS
    // ============================================================================

    /**
     * Generate unique quotation number
     */
    private function generateQuotationNumber(): string
    {
        $prefix = 'QT';
        $date = date('Ymd');
        $count = $this->quotationModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults() + 1;

        return $prefix . $date . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Send quotation email to customer
     */
    private function sendQuotationEmail($quotation): bool
    {
        try {
            $email = \Config\Services::email();

            $subject = "Repair Quotation - #{$quotation['order_number']}";
            $message = view('emails/quotation_sent', [
                'quotation' => $quotation,
                'approval_url' => base_url("quotation/{$quotation['id']}")
            ]);

            $email->setTo($quotation['customer_email']);
            $email->setSubject($subject);
            $email->setMessage($message);

            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send quotation email: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send reminder email to customer
     */
    private function sendReminderEmail($quotation): bool
    {
        try {
            $email = \Config\Services::email();

            $subject = "Reminder: Quotation #{$quotation['quotation_number']} - Awaiting Your Response";
            $message = view('emails/quotation_reminder', [
                'quotation' => $quotation,
                'approval_url' => base_url("quotation/{$quotation['id']}")
            ]);

            $email->setTo($quotation['customer_email']);
            $email->setSubject($subject);
            $email->setMessage($message);

            return $email->send();
        } catch (\Exception $e) {
            log_message('error', 'Failed to send reminder email: ' . $e->getMessage());
            return false;
        }
    }
}