<!-- Buat file baru: app/Views/emails/order_receipt.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Service Receipt - Order #<?= $order['order_number'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #3B82F6; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #1F2937; margin: 0; font-size: 24px; }
        .header p { color: #6B7280; margin: 5px 0 0 0; }
        .section { margin-bottom: 25px; }
        .section h3 { color: #374151; border-bottom: 1px solid #E5E7EB; padding-bottom: 8px; margin-bottom: 15px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .info-item { margin-bottom: 10px; }
        .info-label { font-weight: bold; color: #4B5563; }
        .info-value { color: #1F2937; margin-top: 2px; }
        .status-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; text-transform: uppercase; }
        .status-received { background-color: #FEF3C7; color: #92400E; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #E5E7EB; color: #6B7280; }
        .tracking-box { background-color: #EFF6FF; border: 1px solid #BFDBFE; border-radius: 6px; padding: 15px; margin: 15px 0; }
        @media (max-width: 600px) {
            .info-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1><?= get_site_setting('site_name', 'Computer Repair Shop') ?></h1>
        <p>Service Receipt - Order #<?= $order['order_number'] ?></p>
    </div>

    <!-- Order Status -->
    <div class="section">
        <h3>Order Status</h3>
        <p>Your device has been <strong>received</strong> and logged into our repair system.</p>
        <span class="status-badge status-received">Received</span>
        <p style="margin-top: 10px; color: #6B7280; font-size: 14px;">
            Received on: <?= date('F d, Y \a\t H:i', strtotime($order['created_at'])) ?>
        </p>
    </div>

    <!-- Customer Information -->
    <div class="section">
        <h3>Customer Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Name:</div>
                <div class="info-value"><?= $order['customer_name'] ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Phone:</div>
                <div class="info-value"><?= $order['customer_phone'] ?></div>
            </div>
        </div>
    </div>

    <!-- Device Information -->
    <div class="section">
        <h3>Device Information</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Device Type:</div>
                <div class="info-value"><?= $order['device_type_name'] ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Brand & Model:</div>
                <div class="info-value"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></div>
            </div>
            <?php if ($order['device_serial']): ?>
                <div class="info-item">
                    <div class="info-label">Serial Number:</div>
                    <div class="info-value"><?= $order['device_serial'] ?></div>
                </div>
            <?php endif; ?>
            <div class="info-item">
                <div class="info-label">Priority:</div>
                <div class="info-value"><?= ucfirst($order['priority']) ?></div>
            </div>
        </div>
    </div>

    <!-- Problem Description -->
    <div class="section">
        <h3>Problem Description</h3>
        <div style="background-color: #F9FAFB; padding: 15px; border-radius: 6px; border-left: 4px solid #3B82F6;">
            <?= nl2br(htmlspecialchars($order['problem_description'])) ?>
        </div>
        <?php if ($order['accessories']): ?>
            <div style="margin-top: 15px;">
                <div class="info-label">Accessories Included:</div>
                <div style="background-color: #F9FAFB; padding: 10px; border-radius: 6px; margin-top: 5px;">
                    <?= nl2br(htmlspecialchars($order['accessories'])) ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Tracking Information -->
    <div class="tracking-box">
        <h3 style="margin-top: 0; color: #1E40AF;">Track Your Order</h3>
        <p style="margin: 10px 0; color: #1F2937;">You can track your order status online:</p>
        <p style="font-family: monospace; background: white; padding: 8px; border-radius: 4px; margin: 10px 0; word-break: break-all;">
            <?= base_url("track-order?order={$order['order_number']}") ?>
        </p>
    </div>

    <!-- Next Steps -->
    <div class="section">
        <h3>What Happens Next?</h3>
        <ol style="color: #4B5563; line-height: 1.6;">
            <li><strong>Diagnosis:</strong> Our technician will examine your device to identify the problem</li>
            <li><strong>Quote:</strong> We'll contact you with repair cost and estimated completion time</li>
            <li><strong>Approval:</strong> Once approved, we'll proceed with the repair</li>
            <li><strong>Completion:</strong> We'll notify you when your device is ready for pickup</li>
        </ol>
    </div>

    <!-- Contact Information -->
    <div class="section">
        <h3>Contact Us</h3>
        <p style="color: #4B5563;">If you have any questions about your repair, please contact us:</p>
        <div style="margin-top: 10px;">
            <?php if (get_site_setting('contact_phone')): ?>
                <p><strong>Phone:</strong> <?= get_site_setting('contact_phone') ?></p>
            <?php endif; ?>
            <?php if (get_site_setting('contact_email')): ?>
                <p><strong>Email:</strong> <?= get_site_setting('contact_email') ?></p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>Important:</strong> Please keep this receipt for your records and device pickup.</p>
        <p style="margin-top: 15px;">Thank you for choosing <?= get_site_setting('site_name', 'Computer Repair Shop') ?>!</p>
        <p style="font-size: 12px; margin-top: 10px;">This is an automated email. Please do not reply to this email.</p>
    </div>
</div>
</body>
</html>