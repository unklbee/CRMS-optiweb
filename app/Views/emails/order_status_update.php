<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Status Update</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3B82F6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .order-info { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .status-update { background: #10B981; color: white; padding: 15px; margin: 10px 0; border-radius: 5px; text-align: center; }
        .btn { display: inline-block; padding: 10px 20px; background: #3B82F6; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><?= $siteName ?></h1>
        <h2>Order Status Update</h2>
    </div>

    <div class="content">
        <p>Dear <?= esc($order['customer_name']) ?>,</p>

        <p>We have an update on your repair order:</p>

        <div class="status-update">
            <h3>Status Updated to: <?= ucfirst(str_replace('_', ' ', $newStatus)) ?></h3>
        </div>

        <div class="order-info">
            <h3>Order Details</h3>
            <p><strong>Order Number:</strong> <?= $order['order_number'] ?></p>
            <p><strong>Device:</strong> <?= $order['device_type_name'] ?> - <?= $order['device_brand'] ?> <?= $order['device_model'] ?></p>
            <p><strong>Current Status:</strong> <?= ucfirst(str_replace('_', ' ', $newStatus)) ?></p>
            <?php if ($order['technician_name']): ?>
                <p><strong>Technician:</strong> <?= $order['technician_name'] ?></p>
            <?php endif; ?>
            <?php if ($order['estimated_completion']): ?>
                <p><strong>Estimated Completion:</strong> <?= date('M d, Y', strtotime($order['estimated_completion'])) ?></p>
            <?php endif; ?>
        </div>

        <p>You can track your order status anytime using the link below:</p>
        <p><a href="<?= $trackingUrl ?>" class="btn">Track Your Order</a></p>

        <p>Thank you for your patience. If you have any questions, please don't hesitate to contact us.</p>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> <?= $siteName ?>. All rights reserved.</p>
    </div>
</div>
</body>
</html>