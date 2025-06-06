<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Quotation - <?= $quotation['quotation_number'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 30px;
        }
        .quotation-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .info-section {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #666;
        }
        .value {
            text-align: right;
        }
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin: 30px 0;
        }
        .total-amount {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .action-buttons {
            text-align: center;
            margin: 30px 0;
        }
        .btn {
            display: inline-block;
            padding: 15px 30px;
            margin: 10px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .btn-decline {
            background-color: #dc3545;
            color: white;
        }
        .btn-view {
            background-color: #007bff;
            color: white;
        }
        .timeline-section {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            background-color: #343a40;
            color: white;
            border-radius: 8px;
        }
        .contact-info {
            margin: 10px 0;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <div class="quotation-number"><?= $quotation['quotation_number'] ?></div>
        <div>Repair Quotation for Order #<?= $quotation['order_number'] ?></div>
    </div>

    <!-- Greeting -->
    <h2>Dear <?= $quotation['customer_name'] ?>,</h2>
    <p>Thank you for choosing our computer repair services. We have completed the diagnosis of your device and prepared a detailed quotation for the required repairs.</p>

    <!-- Device Info -->
    <div class="info-section">
        <h3>Device Information</h3>
        <div class="info-row">
            <span class="label">Device Type:</span>
            <span class="value"><?= $quotation['device_type_name'] ?></span>
        </div>
        <div class="info-row">
            <span class="label">Brand & Model:</span>
            <span class="value"><?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></span>
        </div>
        <div class="info-row">
            <span class="label">Problem Description:</span>
            <span class="value"><?= truncate_text($quotation['problem_description'], 100) ?></span>
        </div>
    </div>

    <!-- Cost Breakdown -->
    <div class="info-section">
        <h3>Cost Breakdown</h3>

        <?php if ($quotation['service_cost'] > 0): ?>
            <div class="info-row">
                <span class="label">Labor & Service:</span>
                <span class="value"><?= format_currency($quotation['service_cost']) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($quotation['parts_cost'] > 0): ?>
            <div class="info-row">
                <span class="label">Parts & Materials:</span>
                <span class="value"><?= format_currency($quotation['parts_cost']) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($quotation['additional_cost'] > 0): ?>
            <div class="info-row">
                <span class="label">Additional Charges:</span>
                <span class="value"><?= format_currency($quotation['additional_cost']) ?></span>
            </div>
        <?php endif; ?>

        <div class="info-row">
            <span class="label">Subtotal:</span>
            <span class="value"><?= format_currency($quotation['service_cost'] + $quotation['parts_cost'] + $quotation['additional_cost']) ?></span>
        </div>

        <?php if ($quotation['discount_amount'] > 0): ?>
            <div class="info-row">
                <span class="label">Discount <?= $quotation['discount_percentage'] > 0 ? '(' . $quotation['discount_percentage'] . '%)' : '' ?>:</span>
                <span class="value" style="color: #28a745;">-<?= format_currency($quotation['discount_amount']) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($quotation['tax_amount'] > 0): ?>
            <div class="info-row">
                <span class="label">Tax (<?= $quotation['tax_percentage'] ?>%):</span>
                <span class="value"><?= format_currency($quotation['tax_amount']) ?></span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Total -->
    <div class="total-section">
        <div>TOTAL AMOUNT</div>
        <div class="total-amount"><?= format_currency($quotation['total_cost']) ?></div>
        <div>All inclusive</div>
    </div>

    <!-- Timeline & Warranty -->
    <div class="timeline-section">
        <h3>Service Details</h3>
        <div class="info-row">
            <span class="label">Estimated Duration:</span>
            <span class="value"><?= $quotation['estimated_duration'] ?></span>
        </div>
        <?php if ($quotation['warranty_period']): ?>
            <div class="info-row">
                <span class="label">Warranty Period:</span>
                <span class="value"><?= $quotation['warranty_period'] ?></span>
            </div>
        <?php endif; ?>
        <div class="info-row">
            <span class="label">Valid Until:</span>
            <span class="value"><?= date('F d, Y', strtotime($quotation['valid_until'])) ?></span>
        </div>
    </div>

    <!-- Warning about expiry -->
    <div class="warning">
        <strong>⚠️ Important:</strong> This quotation is valid until <?= date('F d, Y', strtotime($quotation['valid_until'])) ?>.
        Please respond before this date to proceed with the repair.
    </div>

    <!-- Action Buttons -->
    <div class="action-buttons">
        <h3>Ready to proceed?</h3>
        <p>Click one of the buttons below to respond to this quotation:</p>

        <a href="<?= base_url("quotation/{$quotation['id']}/approve") ?>" class="btn btn-approve">
            ✅ APPROVE & START REPAIR
        </a>

        <a href="<?= base_url("quotation/{$quotation['id']}/reject") ?>" class="btn btn-decline">
            ❌ DECLINE QUOTATION
        </a>

        <a href="<?= base_url("quotation/{$quotation['id']}") ?>" class="btn btn-view">
            👁️ VIEW FULL QUOTATION
        </a>
    </div>

    <!-- Terms & Conditions -->
    <?php if ($quotation['terms_conditions']): ?>
        <div class="info-section">
            <h3>Terms & Conditions</h3>
            <div style="font-size: 14px; line-height: 1.5;">
                <?= nl2br($quotation['terms_conditions']) ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contact Info -->
    <div class="info-section">
        <h3>Questions?</h3>
        <p>If you have any questions about this quotation, please don't hesitate to contact us:</p>
        <div class="contact-info">
            <?php if (isset($shop_info['phone']) && $shop_info['phone']): ?>
                <div>📞 Phone: <?= $shop_info['phone'] ?></div>
            <?php endif; ?>
            <?php if (isset($shop_info['email']) && $shop_info['email']): ?>
                <div>📧 Email: <?= $shop_info['email'] ?></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <h3><?= $shop_info['name'] ?? 'Computer Repair Shop' ?></h3>
        <div class="contact-info">
            <?php if (isset($shop_info['address']) && $shop_info['address']): ?>
                <div><?= $shop_info['address'] ?></div>
            <?php endif; ?>
        </div>
        <div style="margin-top: 15px; font-size: 12px; opacity: 0.8;">
            Professional Computer Repair Services
        </div>
    </div>
</div>
</body>
</html>