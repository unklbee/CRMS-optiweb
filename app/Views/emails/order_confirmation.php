<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Order Confirmation</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #3B82F6; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #666; }
        .order-info { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .btn { display: inline-block; padding: 10px 20px; background: #3B82F6; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><?= $siteName ?></h1>
        <h2>Order Confirmation</h2>
    </div>

    <div class="content">
        <p>Dear <?= esc($order['customer_name']) ?>,</p>

        <p>Thank you for choosing our repair service. Your order has been received and is being processed.</p>

        <div class="order-info">
            <h3>Order Details</h3>
            <p><strong>Order Number:</strong> <?= $order['order_number'] ?></p>
            <p><strong>Device:</strong> <?= $order['device_type_name'] ?> - <?= $order['device_brand'] ?> <?= $order['device_model'] ?></p>
            <p><strong>Problem:</strong> <?= esc($order['problem_description']) ?></p>
            <p><strong>Status:</strong> <?= ucfirst(str_replace('_', ' ', $order['status'])) ?></p>
            <p><strong>Date:</strong> <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></p>
        </div>

        <p>You can track your order status anytime using the link below:</p>
        <p><a href="<?= $trackingUrl ?>" class="btn">Track Your Order</a></p>

        <p>We will keep you updated on the progress of your repair. If you have any questions, please don't hesitate to contact us.</p>
    </div>

    <div class="footer">
        <p>&copy; <?= date('Y') ?> <?= $siteName ?>. All rights reserved.</p>
    </div>
</div>
</body>
</html>