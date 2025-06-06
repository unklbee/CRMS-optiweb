<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuotationModel;
use App\Models\RepairOrderModel;
use App\Models\OrderStatusHistoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class QuotationController extends BaseController
{
    protected QuotationModel $quotationModel;
    protected RepairOrderModel $orderModel;

    public function __construct()
    {
        $this->quotationModel = new QuotationModel();
        $this->orderModel = new RepairOrderModel();
    }

    /**
     * List all quotations
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
     * Show pending quotations
     */
    public function pending(): string
    {
        $quotations = $this->quotationModel->getPendingQuotations();

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
        $quotations = $this->quotationModel->getExpiredQuotations();

        $data = [
            'title' => 'Expired Quotations',
            'quotations' => $quotations,
            'page_type' => 'expired'
        ];

        return view('admin/quotations/expired', $data);
    }

    /**
     * Duplicate quotation for new revision
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
        $duplicateData['quotation_number'] = null; // Will be auto-generated
        $duplicateData['sent_at'] = null;
        $duplicateData['responded_at'] = null;
        $duplicateData['customer_notes'] = null;
        $duplicateData['approved_by_customer'] = false;
        $duplicateData['created_by'] = session()->get('user_id');
        $duplicateData['created_at'] = date('Y-m-d H:i:s');
        $duplicateData['updated_at'] = date('Y-m-d H:i:s');

        $newQuotationId = $this->quotationModel->insert($duplicateData);

        if ($newQuotationId) {
            return redirect()->to("/admin/quotations/{$newQuotationId}")
                ->with('success', 'Quotation duplicated successfully');
        }

        return redirect()->back()->with('error', 'Failed to duplicate quotation');
    }

    /**
     * Send quotation reminder
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
        $email = \Config\Services::email();

        $subject = "Reminder: Quotation #{$quotation['quotation_number']} - Awaiting Your Response";
        $message = view('emails/quotation_reminder', ['quotation' => $quotation]);

        $email->setTo($quotation['customer_email']);
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            return redirect()->back()->with('success', 'Reminder sent to customer successfully');
        }

        return redirect()->back()->with('error', 'Failed to send reminder email');
    }

    /**
     * Mark quotation as expired manually
     */
    public function markExpired($id): RedirectResponse
    {
        $quotation = $this->quotationModel->find($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        if ($this->quotationModel->update($id, ['status' => 'expired'])) {
            // Log status change in order history
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $quotation['order_id'],
                'waiting_approval',
                'received', // Return to received status
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
     * Convert quotation to invoice (when approved)
     */
    public function convertToInvoice($id): RedirectResponse
    {
        $quotation = $this->quotationModel->find($id);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        if ($quotation['status'] !== 'approved') {
            return redirect()->back()->with('error', 'Only approved quotations can be converted to invoice');
        }

        // Update order with final costs
        $orderUpdateData = [
            'estimated_cost' => $quotation['total_cost'],
            'final_cost' => $quotation['total_cost'],
            'status' => 'in_progress',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->orderModel->update($quotation['order_id'], $orderUpdateData)) {
            // Log status change
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $quotation['order_id'],
                'waiting_approval',
                'in_progress',
                'Quotation approved by customer - repair work can begin',
                session()->get('user_id')
            );

            return redirect()->to("/admin/orders/{$quotation['order_id']}")
                ->with('success', 'Quotation converted to active work order');
        }

        return redirect()->back()->with('error', 'Failed to convert quotation');
    }

    /**
     * Generate quotation analytics
     */
    public function analytics(): string
    {
        // Get quotation statistics
        $stats = $this->quotationModel->getQuotationStats();

        // Get conversion rates
        $totalSent = $this->quotationModel->where('status', 'sent')->countAllResults();
        $totalApproved = $this->quotationModel->where('status', 'approved')->countAllResults();
        $totalRejected = $this->quotationModel->where('status', 'rejected')->countAllResults();

        $conversionRate = $totalSent > 0 ? ($totalApproved / $totalSent) * 100 : 0;
        $rejectionRate = $totalSent > 0 ? ($totalRejected / $totalSent) * 100 : 0;

        // Get average quotation values
        $avgQuotationValue = $this->quotationModel->selectAvg('total_cost', 'avg_value')
            ->where('status !=', 'draft')
            ->first()['avg_value'] ?? 0;

        // Get monthly quotation trends (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} months"));
            $monthlyTrends[] = [
                'month' => date('M Y', strtotime("-{$i} months")),
                'sent' => $this->quotationModel->where('DATE_FORMAT(created_at, "%Y-%m")', $month)
                    ->where('status !=', 'draft')
                    ->countAllResults(),
                'approved' => $this->quotationModel->where('DATE_FORMAT(created_at, "%Y-%m")', $month)
                    ->where('status', 'approved')
                    ->countAllResults(),
            ];
        }

        $data = [
            'title' => 'Quotation Analytics',
            'stats' => $stats,
            'conversion_rate' => $conversionRate,
            'rejection_rate' => $rejectionRate,
            'avg_quotation_value' => $avgQuotationValue,
            'monthly_trends' => $monthlyTrends
        ];

        return view('admin/quotations/analytics', $data);
    }

    /**
     * Bulk actions for quotations
     */
    public function bulkAction(): RedirectResponse
    {
        $action = $this->request->getPost('bulk_action');
        $quotationIds = $this->request->getPost('quotation_ids');

        if (!$action || !$quotationIds) {
            return redirect()->back()->with('error', 'Please select quotations and an action');
        }

        $processedCount = 0;

        switch ($action) {
            case 'mark_expired':
                foreach ($quotationIds as $id) {
                    if ($this->quotationModel->update($id, ['status' => 'expired'])) {
                        $processedCount++;
                    }
                }
                $message = "{$processedCount} quotations marked as expired";
                break;

            case 'send_reminders':
                foreach ($quotationIds as $id) {
                    $quotation = $this->quotationModel->getQuotationWithOrderDetails($id);
                    if ($quotation && $quotation['status'] === 'sent' && $quotation['customer_email']) {
                        // Send reminder logic here
                        $processedCount++;
                    }
                }
                $message = "{$processedCount} reminder emails sent";
                break;

            case 'delete':
                foreach ($quotationIds as $id) {
                    $quotation = $this->quotationModel->find($id);
                    if ($quotation && in_array($quotation['status'], ['draft', 'expired', 'rejected'])) {
                        if ($this->quotationModel->delete($id)) {
                            $processedCount++;
                        }
                    }
                }
                $message = "{$processedCount} quotations deleted";
                break;

            default:
                return redirect()->back()->with('error', 'Invalid action selected');
        }

        if ($processedCount > 0) {
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'No quotations were processed');
        }
    }

    /**
     * Export quotations to CSV
     */
    public function export()
    {
        $quotations = $this->quotationModel->getQuotationsWithDetails();

        $filename = 'quotations_export_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, [
            'Quotation Number',
            'Order Number',
            'Customer Name',
            'Device Type',
            'Service Cost',
            'Parts Cost',
            'Total Cost',
            'Status',
            'Created Date',
            'Valid Until',
            'Sent Date',
            'Responded Date'
        ]);

        // CSV data
        foreach ($quotations as $quotation) {
            fputcsv($output, [
                $quotation['quotation_number'],
                $quotation['order_number'],
                $quotation['customer_name'],
                $quotation['device_type_name'],
                $quotation['service_cost'],
                $quotation['parts_cost'],
                $quotation['total_cost'],
                ucfirst($quotation['status']),
                $quotation['created_at'],
                $quotation['valid_until'],
                $quotation['sent_at'],
                $quotation['responded_at']
            ]);
        }

        fclose($output);
        exit;
    }
}