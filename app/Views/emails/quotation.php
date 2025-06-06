<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Quotation - <?= $quotation['quotation_number'] ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .summary-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .cost-breakdown {
            margin: 20px 0;
        }
        .cost-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .cost-item:last-child {
            border-bottom: none;
        }
        .cost-item.total {
            font-weight: bold;
            font-size: 18px;
            background: #f0f7ff;
            padding: 15px;
            margin: 15px -15px -15px -15px;
            border-radius: 0 0 8px 8px;
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
            transition: all 0.3s ease;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .btn-approve:hover {
            background-color: #218838;
        }
        .btn-decline {
            background-color: #dc3545;
            color: white;
        }
        .btn-decline:hover {
            background-color: #c82333;
        }
        .btn-view {
            background-color: #6c757d;
            color: white;
        }
        .btn-view:hover {
            background-color: #5a6268;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .info-item {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            font-size: 14px;
        }
        .timeline-warranty {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin: 20px 0;
        }
        .timeline-item {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .timeline-item i {
            font-size: 24px;
            color: #1976d2;
            margin-bottom: 10px;
        }
        .warranty-item {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .warranty-item i {
            font-size: 24px;
            color: #4caf50;
            margin-bottom: 10px;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .footer p {
            margin: 5px 0;
        }
        .alert {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin: 20px 0;
            color: #856404;
        }
        .alert i {
            margin-right: 10px;
        }
        @media (max-width: 600px) {
            .info-grid,
            .timeline-warranty {
                grid-template-columns: 1fr;
            }
            .btn {
                display: block;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Header -->
    <div class="header">
        <h1>Repair Quotation Ready</h1>
        <p><?= $quotation['quotation_number'] ?></p>
    </div>

    <!-- Content -->
    <div class="content">
        <p>Dear <?= $quotation['customer_name'] ?>,</p>

        <p>Thank you for choosing <strong><?= get_site_setting('site_name', 'Computer Repair Shop') ?></strong> for your device repair needs. We have completed the diagnosis of your device and prepared a detailed quotation for the required repairs.</p>

        <!-- Device & Order Summary -->
        <div class="summary-box">
            <h3 style="margin-top: 0;">📱 Device & Order Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Order Number</div>
                    <div class="info-value"><?= $quotation['order_number'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Device</div>
                    <div class="info-value"><?= $quotation['device_type_name'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Brand & Model</div>
                    <div class="info-value"><?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Quotation Date</div>
                    <div class="info-value"><?= date('M d, Y', strtotime($quotation['created_at'])) ?></div>
                </div>
            </div>
        </div>

        <!-- Problem Description -->
        <div class="summary-box">
            <h3 style="margin-top: 0;">🔧 Repair Details</h3>
            <p><strong>Problem Identified:</strong></p>
            <p><?= nl2br($quotation['problem_description']) ?></p>
        </div>

        <!-- Cost Breakdown -->
        <div class="cost-breakdown">
            <h3>💰 Cost Breakdown</h3>
            <div style="background: #f8f9fa; border-radius: 8px; padding: 15px;">

                <?php if ($quotation['service_cost'] > 0): ?>
                    <div class="cost-item">
                        <span>
                            <strong>Labor & Service</strong><br>
                            <small>Professional repair services</small>
                        </span>
                        <span><strong><?= format_currency($quotation['service_cost']) ?></strong></span>
                    </div>
                <?php endif; ?>

                <?php if ($quotation['parts_cost'] > 0): ?>
                    <div class="cost-item">
                        <span>
                            <strong>Parts & Materials</strong><br>
                            <small>Replacement components</small>
                        </span>
                        <span><strong><?= format_currency($quotation['parts_cost']) ?></strong></span>
                    </div>
                <?php endif; ?>

                <?php if ($quotation['additional_cost'] > 0): ?>
                    <div class="cost-item">
                        <span>
                            <strong>Additional Charges</strong><br>
                            <small>Express service, shipping, etc.</small>
                        </span>
                        <span><strong><?= format_currency($quotation['additional_cost']) ?></strong></span>
                    </div>
                <?php endif; ?>

                <!-- Subtotal -->
                <div class="cost-item">
                    <span><strong>Subtotal</strong></span>
                    <span><strong><?= format_currency($quotation['service_cost'] + $quotation['parts_cost'] + $quotation['additional_cost']) ?></strong></span>
                </div>

                <!-- Discount -->
                <?php if ($quotation['discount_amount'] > 0): ?>
                    <div class="cost-item">
                        <span>
                            <strong>Discount <?php if ($quotation['discount_percentage'] > 0): ?>(<?= $quotation['discount_percentage'] ?>%)<?php endif; ?></strong>
                        </span>
                        <span style="color: #28a745;"><strong>-<?= format_currency($quotation['discount_amount']) ?></strong></span>
                    </div>
                <?php endif; ?>

                <!-- Tax -->
                <?php if ($quotation['tax_amount'] > 0): ?>
                    <div class="cost-item">
                        <span><strong>Tax (<?= $quotation['tax_percentage'] ?>%)</strong></span>
                        <span><strong><?= format_currency($quotation['tax_amount']) ?></strong></span>
                    </div>
                <?php endif; ?>

                <!-- Total -->
                <div class="cost-item total">
                        <span>
                            <strong>TOTAL AMOUNT</strong><br>
                            <small>All inclusive</small>
                        </span>
                    <span style="color: #1976d2; font-size: 20px;"><strong><?= format_currency($quotation['total_cost']) ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Timeline & Warranty -->
        <div class="timeline-warranty">
            <div class="timeline-item">
                <div>⏱️</div>
                <strong>Estimated Duration</strong><br>
                <?= $quotation['warranty_period'] ?>
            </div>
        </div>

        <!-- Validity Alert -->
        <div class="alert">
            <i>⚠️</i>
            <strong>Important:</strong> This quotation is valid until <strong><?= date('F d, Y', strtotime($quotation['valid_until'])) ?></strong>.
            Please respond before this date to proceed with the repair.
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <h3>🎯 Ready to Proceed?</h3>
            <p>Please choose one of the options below:</p>

            <a href="<?= base_url("quotation/{$quotation['id']}/approve") ?>" class="btn btn-approve">
                ✅ APPROVE & START REPAIR
            </a>

            <a href="<?= base_url("quotation/{$quotation['id']}/reject") ?>" class="btn btn-decline">
                ❌ DECLINE QUOTATION
            </a>

            <br>

            <a href="<?= base_url("quotation/{$quotation['id']}") ?>" class="btn btn-view">
                👀 VIEW FULL DETAILS
            </a>
        </div>

        <!-- Terms & Conditions -->
        <?php if ($quotation['terms_conditions']): ?>
            <div class="summary-box">
                <h3 style="margin-top: 0;">📋 Terms & Conditions</h3>
                <div style="font-size: 13px; line-height: 1.6;">
                    <?= nl2br($quotation['terms_conditions']) ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- What Happens Next -->
        <div class="summary-box">
            <h3 style="margin-top: 0;">🚀 What Happens Next?</h3>
            <div style="font-size: 14px;">
                <p><strong>If you APPROVE:</strong></p>
                <ul style="margin: 10px 0;">
                    <li>We'll begin repair work within 1 business day</li>
                    <li>You'll receive progress updates via email/SMS</li>
                    <li>We'll notify you when repair is complete</li>
                    <li>Payment is due upon pickup/delivery</li>
                </ul>

                <p><strong>If you DECLINE:</strong></p>
                <ul style="margin: 10px 0;">
                    <li>We'll contact you to discuss alternatives</li>
                    <li>Your device remains safe with us</li>
                    <li>No charges apply for diagnosis</li>
                    <li>We can arrange device return if needed</li>
                </ul>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="summary-box">
            <h3 style="margin-top: 0;">📞 Questions or Concerns?</h3>
            <p>Our team is here to help! Contact us if you need clarification about anything in this quotation:</p>
            <div style="text-align: center; margin: 15px 0;">
                <?php if (get_site_setting('contact_phone')): ?>
                    <strong>📱 Phone:</strong> <?= get_site_setting('contact_phone') ?><br>
                <?php endif; ?>
                <?php if (get_site_setting('contact_email')): ?>
                    <strong>📧 Email:</strong> <?= get_site_setting('contact_email') ?><br>
                <?php endif; ?>
                <?php
                // Ambil setting dari DB (didekode otomatis jadi array assoc)
                $rawHours = get_site_setting('business_hours', ['monday'=>'09:00-18:00','tuesday'=>'09:00-18:00','wednesday'=>'09:00-18:00','thursday'=>'09:00-18:00','friday'=>'09:00-18:00','saturday'=>'09:00-15:00','sunday'=>'closed']);

                // Jika tetap string (misalnya default fallback), langsung tampilkan.
                if (! is_array($rawHours)) {
                    $hoursToDisplay = $rawHours;
                } else {
                    // Urutkan supaya Senin–Minggu berurutan
                    $order = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                    $lines = [];
                    foreach ($order as $dayKey) {
                        if (isset($rawHours[$dayKey])) {
                            // Ubah penamaan agar tampilannya Capitalized, misalnya “Monday”
                            $dayLabel = ucfirst($dayKey);
                            $hours   = $rawHours[$dayKey];
                            $lines[] = "{$dayLabel}: {$hours}";
                        }
                    }
                    // Pilih format: masing‐masing hari di baris baru
                    $hoursToDisplay = implode("<br>", $lines);
                }
                ?>
                <strong>🕒 Hours:</strong><br>
                <?= $hoursToDisplay ?>

            </div>
        </div>

        <!-- Mobile App / Tracking -->
        <div style="text-align: center; margin: 20px 0; padding: 15px; background: #f0f7ff; border-radius: 8px;">
            <h4 style="margin: 0 0 10px 0;">📱 Track Your Repair</h4>
            <p style="margin: 0; font-size: 14px;">
                You can also track your repair progress online:<br>
                <a href="<?= base_url("track-order?order={$quotation['order_number']}") ?>" style="color: #1976d2; font-weight: bold;">
                    <?= base_url("track-order?order={$quotation['order_number']}") ?>
                </a>
            </p>
        </div>

        <p style="margin-top: 30px;">
            Thank you for trusting us with your device repair. We look forward to getting your device back to perfect working condition!
        </p>

        <p>
            Best regards,<br>
            <strong>The <?= get_site_setting('site_name', 'Computer Repair Shop') ?> Team</strong>
        </p>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong><?= get_site_setting('site_name', 'Computer Repair Shop') ?></strong></p>
        <?php if (get_site_setting('address')): ?>
            <p><?= get_site_setting('address') ?></p>
        <?php endif; ?>
        <p>
            <?php if (get_site_setting('contact_phone')): ?>
                📱 <?= get_site_setting('contact_phone') ?>
            <?php endif; ?>
            <?php if (get_site_setting('contact_email')): ?>
                📧 <?= get_site_setting('contact_email') ?>
            <?php endif; ?>
        </p>
        <p style="font-size: 12px; opacity: 0.8; margin-top: 15px;">
            This email was sent regarding quotation <?= $quotation['quotation_number'] ?> for order <?= $quotation['order_number'] ?>.<br>
            If you did not request this quotation, please contact us immediately.
        </p>
    </div>
</div>
</body>
</html>