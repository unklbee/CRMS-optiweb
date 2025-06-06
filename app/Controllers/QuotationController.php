<?php

namespace App\Controllers;

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
     * Show quotation to customer (public view)
     */
    public function view($quotationId): string
    {
        $quotation = $this->quotationModel->getQuotationWithOrderDetails($quotationId);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Check if quotation is accessible (not draft)
        if ($quotation['status'] === 'draft') {
            throw new PageNotFoundException('Quotation not available');
        }

        // Check if expired and update status
        if ($quotation['status'] === 'sent' && strtotime($quotation['valid_until']) < time()) {
            $this->quotationModel->update($quotationId, ['status' => 'expired']);
            $quotation['status'] = 'expired';
        }

        $data = [
            'title' => 'Quotation - ' . $quotation['quotation_number'],
            'quotation' => $quotation,
            'shop_info' => [
                'name' => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone' => get_site_setting('contact_phone', ''),
                'email' => get_site_setting('contact_email', ''),
            ]
        ];

        return view('admin/quotations/view', $data);
    }

    /**
     * Handle customer quotation approval
     */
    public function approve($quotationId)
    {
        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Validate quotation can be approved
        if (!in_array($quotation['status'], ['sent'])) {
            return redirect()->to("/quotation/{$quotationId}")
                ->with('error', 'This quotation cannot be approved at its current status.');
        }

        // Check if expired
        if (strtotime($quotation['valid_until']) < time()) {
            $this->quotationModel->update($quotationId, ['status' => 'expired']);
            return redirect()->to("/quotation/{$quotationId}")
                ->with('error', 'This quotation has expired and cannot be approved.');
        }

        if ($this->request->getMethod() === 'POST') {
            $customerNotes = $this->request->getPost('customer_notes');

            // Approve quotation
            if ($this->quotationModel->approveQuotation($quotationId, $customerNotes)) {
                // Update order status to in_progress
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
                    'Customer approved quotation. Repair work can begin. Customer notes: ' . ($customerNotes ?: 'None'),
                    null // No user ID for customer actions
                );

                // Send confirmation email to admin
                $this->sendApprovalNotificationToAdmin($quotation, $customerNotes);

                return redirect()->to("/quotation/{$quotationId}")
                    ->with('success', 'Quotation approved successfully! We will begin the repair work shortly.');
            }

            return redirect()->back()->with('error', 'Failed to approve quotation. Please try again.');
        }

        // Show approval form (GET request)
        $quotationDetails = $this->quotationModel->getQuotationWithOrderDetails($quotationId);

        $data = [
            'title' => 'Approve Quotation',
            'quotation' => $quotationDetails,
            'action' => 'approve',
            'shop_info' => [
                'name' => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone' => get_site_setting('contact_phone', ''),
                'email' => get_site_setting('contact_email', ''),
            ]
        ];

        return view('admin/quotations/approval_form', $data);
    }

    /**
     * Handle customer quotation rejection
     */
    public function reject($quotationId)
    {
        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Validate quotation can be rejected
        if (!in_array($quotation['status'], ['sent'])) {
            return redirect()->to("/quotation/{$quotationId}")
                ->with('error', 'This quotation cannot be rejected at its current status.');
        }

        if ($this->request->getMethod() === 'POST') {
            $customerNotes = $this->request->getPost('customer_notes');

            if (empty($customerNotes)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Please provide a reason for rejecting this quotation.');
            }

            // Reject quotation
            if ($this->quotationModel->rejectQuotation($quotationId, $customerNotes)) {
                // Update order status back to diagnosed
                $this->orderModel->update($quotation['order_id'], [
                    'status' => 'diagnosed',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                // Log status change
                $historyModel = new OrderStatusHistoryModel();
                $historyModel->addStatusChange(
                    $quotation['order_id'],
                    'waiting_approval',
                    'diagnosed',
                    'Customer rejected quotation. Reason: ' . $customerNotes,
                    null // No user ID for customer actions
                );

                // Send rejection notification to admin
                $this->sendRejectionNotificationToAdmin($quotation, $customerNotes);

                return redirect()->to("/quotation/{$quotationId}")
                    ->with('success', 'Quotation declined. We will contact you to discuss alternatives.');
            }

            return redirect()->back()->with('error', 'Failed to reject quotation. Please try again.');
        }

        // Show rejection form (GET request)
        $quotationDetails = $this->quotationModel->getQuotationWithOrderDetails($quotationId);

        $data = [
            'title' => 'Decline Quotation',
            'quotation' => $quotationDetails,
            'action' => 'reject',
            'shop_info' => [
                'name' => get_site_setting('site_name', 'Computer Repair Shop'),
                'address' => get_site_setting('address', ''),
                'phone' => get_site_setting('contact_phone', ''),
                'email' => get_site_setting('contact_email', ''),
            ]
        ];

        return view('admin/quotations/approval_form', $data);
    }

    /**
     * Download quotation as PDF
     */
    public function downloadPdf($quotationId)
    {
        $quotation = $this->quotationModel->getQuotationWithOrderDetails($quotationId);

        if (!$quotation) {
            throw new PageNotFoundException('Quotation not found');
        }

        // Check if quotation is accessible
        if ($quotation['status'] === 'draft') {
            throw new PageNotFoundException('Quotation not available');
        }

        $data = [
            'title' => 'Quotation - ' . $quotation['quotation_number'],
            'quotation' => $quotation,
            'print_mode' => true,
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
     * Check quotation status (AJAX endpoint)
     */
    public function checkStatus($quotationId)
    {
        $quotation = $this->quotationModel->find($quotationId);

        if (!$quotation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Quotation not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'status' => $quotation['status'],
            'valid_until' => $quotation['valid_until'],
            'is_expired' => strtotime($quotation['valid_until']) < time()
        ]);
    }

    /**
     * Send approval notification to admin
     */
    private function sendApprovalNotificationToAdmin($quotation, $customerNotes): bool
    {
        $email = \Config\Services::email();

        $adminEmail = get_site_setting('admin_email', 'cs@optiontech.id');
        if (!$adminEmail) {
            return false;
        }

        $subject = "Quotation Approved - {$quotation['quotation_number']}";

        $message = "
        <h2>Quotation Approved!</h2>
        <p>Good news! Customer has approved the quotation and authorized repair work to begin.</p>
        
        <h3>Quotation Details:</h3>
        <ul>
            <li><strong>Quotation Number:</strong> {$quotation['quotation_number']}</li>
            <li><strong>Order Number:</strong> {$quotation['order_number']}</li>
            <li><strong>Customer:</strong> {$quotation['customer_name']}</li>
            <li><strong>Device:</strong> {$quotation['device_type_name']} - {$quotation['device_brand']} {$quotation['device_model']}</li>
            <li><strong>Total Amount:</strong> " . format_currency($quotation['total_cost']) . "</li>
        </ul>
        
        <h3>Customer Notes:</h3>
        <p>" . ($customerNotes ?: 'No additional notes provided.') . "</p>
        
        <p><strong>Next Steps:</strong> You can now begin the repair work as the customer has given approval.</p>
        
        <p><a href='" . base_url("admin/orders/{$quotation['order_id']}") . "'>View Order Details</a></p>
        ";

        $email->setFrom('cs@optiontech.id', 'Computer Repair Shop');
        $email->setTo($adminEmail);
        $email->setSubject($subject);
        $email->setMessage($message);

        return $email->send();
    }

    /**
     * Send rejection notification to admin
     */
    private function sendRejectionNotificationToAdmin($quotation, $customerNotes): bool
    {
        $email = \Config\Services::email();

        $adminEmail = get_site_setting('admin_email', 'cs@optiontech.id');
        if (!$adminEmail) {
            return false;
        }

        $subject = "Quotation Rejected - {$quotation['quotation_number']}";

        $message = "
        <h2>Quotation Rejected</h2>
        <p>The customer has declined the quotation. Please review their feedback and consider next steps.</p>
        
        <h3>Quotation Details:</h3>
        <ul>
            <li><strong>Quotation Number:</strong> {$quotation['quotation_number']}</li>
            <li><strong>Order Number:</strong> {$quotation['order_number']}</li>
            <li><strong>Customer:</strong> {$quotation['customer_name']}</li>
            <li><strong>Device:</strong> {$quotation['device_type_name']} - {$quotation['device_brand']} {$quotation['device_model']}</li>
            <li><strong>Total Amount:</strong> " . format_currency($quotation['total_cost']) . "</li>
        </ul>
        
        <h3>Customer's Reason for Rejection:</h3>
        <p><em>" . $customerNotes . "</em></p>
        
        <p><strong>Recommended Actions:</strong></p>
        <ul>
            <li>Contact customer to discuss alternatives</li>
            <li>Consider providing a revised quotation</li>
            <li>Review pricing or service options</li>
        </ul>
        
        <p><a href='" . base_url("admin/orders/{$quotation['order_id']}") . "'>View Order Details</a></p>
        ";

        $email->setFrom('cs@optiontech.id', 'Computer Repair Shop');
        $email->setTo($adminEmail);
        $email->setSubject($subject);
        $email->setMessage($message);

        return $email->send();
    }
}