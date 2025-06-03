<?php
namespace App\Libraries;

use App\Models\CmsSettingModel;
use CodeIgniter\Email\Email;

class NotificationService
{
    protected Email $email;
    protected CmsSettingModel $settingModel;

    public function __construct()
    {
        $this->email = \Config\Services::email();
        $this->settingModel = new CmsSettingModel();
    }

    /**
     * Send order status update notification
     */
    public function sendOrderStatusUpdate($order, $newStatus): bool
    {
        $customerEmail = $order['customer_email'];
        if (!$customerEmail) return false;

        $subject = "Order Status Update - " . $order['order_number'];
        $message = $this->buildOrderStatusEmail($order, $newStatus);

        $this->email->setTo($customerEmail);
        $this->email->setSubject($subject);
        $this->email->setMessage($message);

        return $this->email->send();
    }

    /**
     * Send new order confirmation
     */
    public function sendOrderConfirmation($order): bool
    {
        $customerEmail = $order['customer_email'];
        if (!$customerEmail) return false;

        $subject = "Order Confirmation - " . $order['order_number'];
        $message = $this->buildOrderConfirmationEmail($order);

        $this->email->setTo($customerEmail);
        $this->email->setSubject($subject);
        $this->email->setMessage($message);

        return $this->email->send();
    }

    /**
     * Build order status email template
     */
    private function buildOrderStatusEmail($order, $newStatus): string
    {
        $siteName = get_site_setting('site_name', 'Computer Repair Shop');
        $trackingUrl = base_url('track-order?order=' . $order['order_number']);

        return view('emails/order_status_update', [
            'order' => $order,
            'newStatus' => $newStatus,
            'siteName' => $siteName,
            'trackingUrl' => $trackingUrl
        ]);
    }

    /**
     * Build order confirmation email template
     */
    private function buildOrderConfirmationEmail($order): string
    {
        $siteName = get_site_setting('site_name', 'Computer Repair Shop');
        $trackingUrl = base_url('track-order?order=' . $order['order_number']);

        return view('emails/order_confirmation', [
            'order' => $order,
            'siteName' => $siteName,
            'trackingUrl' => $trackingUrl
        ]);
    }
}