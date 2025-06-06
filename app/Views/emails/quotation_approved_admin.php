<?php
// File: app/Views/emails/quotation_approved_admin.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotation Approved - <?= $quotation['quotation_number'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #28a745; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-radius: 6px; border-left: 4px solid #28a745; }
        .action-needed { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; margin: 15px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>✅ Quotation Approved!</h1>
        <p><?= $quotation['quotation_number'] ?></p>
    </div>

    <div class="content">
        <p><strong>Great news!</strong> The customer has approved the quotation and authorized us to proceed with the repair.</p>

        <div class="info-box">
            <h3>Order Details</h3>
            <p><strong>Order:</strong> <?= $quotation['order_number'] ?></p>
            <p><strong>Customer:</strong> <?= $quotation['customer_name'] ?></p>
            <p><strong>Device:</strong> <?= $quotation['device_type_name'] ?> - <?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></p>
            <p><strong>Approved Amount:</strong> <?= format_currency($quotation['total_cost']) ?></p>
            <p><strong>Estimated Duration:</strong> <?= $quotation['estimated_duration'] ?></p>
        </div>

        <?php if ($customer_notes): ?>
            <div class="info-box">
                <h3>Customer Notes</h3>
                <p><?= nl2br($customer_notes) ?></p>
            </div>
        <?php endif; ?>

        <div class="action-needed">
            <h3>⚠️ Action Required</h3>
            <ul>
                <li>Begin repair work within 1 business day</li>
                <li>Order any required parts immediately</li>
                <li>Assign technician if not already assigned</li>
                <li>Update customer on progress regularly</li>
            </ul>
        </div>

        <p style="text-align: center; margin: 20px 0;">
            <a href="<?= base_url("admin/orders/{$quotation['order_id']}") ?>"
               style="background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                View Order Details
            </a>
        </p>

        <p>The order status has been automatically updated to "In Progress" and the customer is expecting regular updates.</p>
    </div>
</div>
</body>
</html>

<?php
// File: app/Views/emails/quotation_rejected_admin.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quotation Rejected - <?= $quotation['quotation_number'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 20px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 15px; margin: 10px 0; border-radius: 6px; border-left: 4px solid #dc3545; }
        .action-needed { background: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 6px; margin: 15px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>❌ Quotation Declined</h1>
        <p><?= $quotation['quotation_number'] ?></p>
    </div>

    <div class="content">
        <p>The customer has declined the quotation. Please review their feedback and consider next steps.</p>

        <div class="info-box">
            <h3>Order Details</h3>
            <p><strong>Order:</strong> <?= $quotation['order_number'] ?></p>
            <p><strong>Customer:</strong> <?= $quotation['customer_name'] ?></p>
            <p><strong>Phone:</strong> <?= $quotation['customer_phone'] ?></p>
            <p><strong>Device:</strong> <?= $quotation['device_type_name'] ?> - <?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></p>
            <p><strong>Declined Amount:</strong> <?= format_currency($quotation['total_cost']) ?></p>
        </div>

        <div class="info-box">
            <h3>Customer's Reason for Declining</h3>
            <p><?= nl2br($customer_notes) ?></p>
        </div>

        <div class="action-needed">
            <h3>⚠️ Recommended Actions</h3>
            <ul>
                <li>Contact customer within 1 business day to discuss alternatives</li>
                <li>Consider offering revised quotation with different options</li>
                <li>Review pricing if customer indicated cost concerns</li>
                <li>Offer alternative repair approaches if available</li>
                <li>Document follow-up actions in order notes</li>
            </ul>
        </div>

        <p style="text-align: center; margin: 20px 0;">
            <a href="<?= base_url("admin/orders/{$quotation['order_id']}") ?>"
               style="background: #007bff; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">
                View Order & Contact Customer
            </a>
        </p>

        <p>The order status has been updated back to "Diagnosed" to allow for revision or alternative approaches.</p>
    </div>
</div>
</body>
</html>

<?php
// File: app/Views/emails/order_status_update.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Status Update - <?= $order['order_number'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 20px; background: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px 20px; text-align: center; }
        .content { padding: 30px; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: bold; text-transform: uppercase; font-size: 12px; }
        .status-completed { background: #d4edda; color: #155724; }
        .status-in_progress { background: #cce5ff; color: #004085; }
        .status-waiting_approval { background: #fff3cd; color: #856404; }
        .status-diagnosed { background: #e2e3e5; color: #383d41; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .info-box { background: #f8f9fa; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #667eea; }
        .footer { background: #2c3e50; color: white; padding: 20px; text-align: center; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>📱 Order Status Update</h1>
        <p>Order #<?= $order['order_number'] ?></p>
    </div>

    <div class="content">
        <p>Dear <?= $customer['full_name'] ?>,</p>

        <p>We have an update on your device repair order!</p>

        <div class="info-box">
            <h3>New Status: <span class="status-badge status-<?= str_replace(' ', '_', strtolower($new_status)) ?>"><?= ucfirst(str_replace('_', ' ', $new_status)) ?></span></h3>

            <?php
            $statusMessages = [
                'diagnosed' => 'Our technician has completed the diagnosis of your device. You should receive a detailed quotation shortly.',
                'waiting_approval' => 'We have sent you a quotation for the repair. Please review and let us know if you\'d like to proceed.',
                'in_progress' => 'Great news! We have begun working on your device repair.',
                'waiting_parts' => 'We are waiting for replacement parts to arrive. We\'ll update you once they\'re in.',
                'completed' => '🎉 Excellent! Your device repair has been completed successfully and is ready for pickup.',
                'delivered' => 'Your device has been delivered/picked up. Thank you for choosing our services!'
            ];
            ?>

            <p><?= $statusMessages[$new_status] ?? 'Your order status has been updated.' ?></p>

            <?php if ($notes): ?>
                <p><strong>Additional Notes:</strong></p>
                <p><?= nl2br($notes) ?></p>
            <?php endif; ?>
        </div>

        <div class="info-box">
            <h3>Order Summary</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Device:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?= $order['device_type_name'] ?? 'N/A' ?> - <?= $order['device_brand'] ?? '' ?> <?= $order['device_model'] ?? '' ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Order Date:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Priority:</strong></td>
                    <td style="padding: 8px;"><?= ucfirst($order['priority'] ?? 'normal') ?></td>
                </tr>
            </table>
        </div>

        <?php if (in_array($new_status, ['completed', 'delivered'])): ?>
            <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #155724; margin-top: 0;">🎉 Repair Completed!</h3>
                <p style="color: #155724;">
                    <?php if ($new_status === 'completed'): ?>
                        Your device is ready for pickup! Please bring this email and a valid ID when collecting your device.
                    <?php else: ?>
                        Thank you for choosing our services! We hope your device serves you well. If you experience any issues during the warranty period, please don't hesitate to contact us.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <?php if ($new_status === 'waiting_approval'): ?>
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #856404; margin-top: 0;">⏳ Your Response Needed</h3>
                <p style="color: #856404;">
                    We've sent you a detailed quotation for the repair. Please check your email and respond within the specified timeframe to proceed with the repair.
                </p>
            </div>
        <?php endif; ?>

        <!-- Track Order Link -->
        <div style="text-align: center; margin: 30px 0;">
            <a href="<?= base_url("track-order?order={$order['order_number']}") ?>"
               style="background: #667eea; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; display: inline-block; font-weight: bold;">
                📱 Track Your Order Online
            </a>
        </div>

        <!-- Contact Information -->
        <div style="background: #e3f2fd; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="color: #1976d2; margin-top: 0;">📞 Questions or Concerns?</h3>
            <p style="color: #1976d2; margin: 0;">
                Feel free to contact us if you have any questions about your repair:
            </p>
            <div style="margin: 15px 0; color: #1976d2;">
                <?php if (get_site_setting('contact_phone')): ?>
                    <strong>📱 Phone:</strong> <?= get_site_setting('contact_phone') ?><br>
                <?php endif; ?>
                <?php if (get_site_setting('contact_email')): ?>
                    <strong>📧 Email:</strong> <?= get_site_setting('contact_email') ?><br>
                <?php endif; ?>
                <strong>🕒 Hours:</strong> <?= get_site_setting('business_hours', 'Mon-Fri 9AM-6PM') ?>
            </div>
        </div>

        <p>Thank you for trusting us with your device repair!</p>

        <p>
            Best regards,<br>
            <strong>The <?= get_site_setting('site_name', 'Computer Repair Shop') ?> Team</strong>
        </p>
    </div>

    <div class="footer">
        <p><strong><?= get_site_setting('site_name', 'Computer Repair Shop') ?></strong></p>
        <?php if (get_site_setting('address')): ?>
            <p><?= get_site_setting('address') ?></p>
        <?php endif; ?>
        <p style="font-size: 12px; opacity: 0.8; margin-top: 15px;">
            This email was sent regarding order <?= $order['order_number'] ?>.<br>
            If you believe you received this email in error, please contact us immediately.
        </p>
    </div>
</div>
</body>
</html>