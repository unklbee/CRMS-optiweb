<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation Reminder - <?= $quotation['quotation_number'] ?></title>
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
            background: linear-gradient(135deg, #ff9a56 0%, #ff6b35 100%);
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
        .urgency-box {
            background: #fff3cd;
            border: 2px solid #ffc107;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .urgency-box h3 {
            color: #856404;
            margin-top: 0;
        }
        .summary-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #ff6b35;
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
        .btn-decline {
            background-color: #dc3545;
            color: white;
        }
        .btn-view {
            background-color: #6c757d;
            color: white;
        }
        .timeline {
            background: #e3f2fd;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .timeline-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
            font-size: 14px;
        }
        .timeline-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
            color: white;
        }
        .timeline-icon.done {
            background: #28a745;
        }
        .timeline-icon.current {
            background: #ffc107;
            color: #333;
        }
        .timeline-icon.pending {
            background: #6c757d;
        }
        .footer {
            background: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
        }
        @media (max-width: 600px) {
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
        <h1>⏰ Quotation Reminder</h1>
        <p><?= $quotation['quotation_number'] ?></p>
    </div>

    <!-- Content -->
    <div class="content">
        <p>Hello <?= $quotation['customer_name'] ?>,</p>

        <p>We hope this email finds you well. This is a friendly reminder about the repair quotation we sent for your <strong><?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></strong>.</p>

        <!-- Urgency Box -->
        <div class="urgency-box">
            <h3>⚠️ Action Required</h3>
            <p><strong>Your quotation expires on <?= date('F d, Y', strtotime($quotation['valid_until'])) ?></strong></p>
            <?php
            $daysLeft = ceil((strtotime($quotation['valid_until']) - time()) / (60 * 60 * 24));
            if ($daysLeft > 0):
                ?>
                <p>You have <strong><?= $daysLeft ?> day<?= $daysLeft > 1 ? 's' : '' ?> left</strong> to respond to this quotation.</p>
            <?php else: ?>
                <p style="color: #dc3545;"><strong>This quotation expires today!</strong></p>
            <?php endif; ?>
        </div>

        <!-- Quick Summary -->
        <div class="summary-box">
            <h3 style="margin-top: 0;">📋 Quick Summary</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Order:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?= $quotation['order_number'] ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Device:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><?= $quotation['device_type_name'] ?> - <?= $quotation['device_brand'] ?> <?= $quotation['device_model'] ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Cost:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd; font-size: 18px; font-weight: bold; color: #1976d2;"><?= format_currency($quotation['total_cost']) ?></td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Estimated Duration:</strong></td>
                    <td style="padding: 8px;"><?= $quotation['estimated_duration'] ?></td>
                </tr>
            </table>
        </div>

        <!-- Current Process Status -->
        <div class="timeline">
            <h3 style="margin-top: 0;">🔄 Current Status</h3>
            <div class="timeline-item">
                <div class="timeline-icon done">✓</div>
                <div>Device received and logged into our system</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon done">✓</div>
                <div>Complete diagnosis performed by our technician</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon done">✓</div>
                <div>Detailed quotation prepared and sent to you</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon current">!</div>
                <div><strong>Waiting for your approval to proceed</strong></div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon pending">4</div>
                <div>Begin repair work (pending your approval)</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon pending">5</div>
                <div>Complete repair and testing</div>
            </div>
            <div class="timeline-item">
                <div class="timeline-icon pending">6</div>
                <div>Device ready for pickup/delivery</div>
            </div>
        </div>

        <!-- Why We Need Your Response -->
        <div class="summary-box">
            <h3 style="margin-top: 0;">🤔 Why Do We Need Your Response?</h3>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li><strong>Parts Availability:</strong> Some replacement parts need to be ordered and may take time to arrive</li>
                <li><strong>Scheduling:</strong> We want to allocate the right technician and time slot for your repair</li>
                <li><strong>Cost Confirmation:</strong> We need your approval before any charges are applied</li>
                <li><strong>Device Safety:</strong> Your device is safe with us, but we want to complete the repair promptly</li>
            </ul>
        </div>

        <!-- What If You Don't Respond -->
        <?php if ($daysLeft <= 2): ?>
            <div style="background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 8px; padding: 15px; margin: 20px 0; color: #721c24;">
                <h4 style="margin-top: 0;">⏳ If We Don't Hear From You</h4>
                <p>If we don't receive your response by the expiry date:</p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>This quotation will become invalid</li>
                    <li>We may need to re-diagnose your device if left too long</li>
                    <li>Parts pricing may change due to market fluctuations</li>
                    <li>Your device will remain safely stored, but repair will be delayed</li>
                </ul>
                <p><strong>We'd love to help you get your device working again!</strong></p>
            </div>
        <?php endif; ?>

        <!-- Easy Response Options -->
        <div class="action-buttons">
            <h3>📱 Quick Response Options</h3>
            <p>Choose one of the options below - it only takes 2 minutes!</p>

            <a href="<?= base_url("quotation/{$quotation['id']}/approve") ?>" class="btn btn-approve">
                ✅ YES, PROCEED WITH REPAIR
            </a>

            <a href="<?= base_url("quotation/{$quotation['id']}/reject") ?>" class="btn btn-decline">
                ❌ NO, I WANT TO DECLINE
            </a>

            <br>

            <a href="<?= base_url("quotation/{$quotation['id']}") ?>" class="btn btn-view">
                👀 VIEW FULL QUOTATION AGAIN
            </a>
        </div>

        <!-- Still Have Questions -->
        <div class="summary-box">
            <h3 style="margin-top: 0;">💬 Still Have Questions?</h3>
            <p>Our friendly team is here to help! We understand that device repairs can be a big decision.</p>

            <div style="background: white; padding: 15px; border-radius: 6px; margin: 15px 0;">
                <h4 style="margin: 0 0 10px 0;">Common Questions We Can Help With:</h4>
                <ul style="margin: 0; padding-left: 20px; font-size: 14px;">
                    <li>Explanation of what work will be performed</li>
                    <li>Timeline details and what might affect duration</li>
                    <li>Warranty coverage and what it includes</li>
                    <li>Payment options and methods accepted</li>
                    <li>Alternative repair options if available</li>
                    <li>Data recovery and backup options</li>
                </ul>
            </div>

            <div style="text-align: center; margin: 15px 0;">
                <?php if (get_site_setting('contact_phone')): ?>
                    <strong>📱 Call Us:</strong> <?= get_site_setting('contact_phone') ?><br>
                <?php endif; ?>
                <?php if (get_site_setting('contact_email')): ?>
                    <strong>📧 Email Us:</strong> <?= get_site_setting('contact_email') ?><br>
                <?php endif; ?>
                <?php if (get_site_setting('whatsapp_number')): ?>
                    <strong>💬 WhatsApp:</strong> <?= get_site_setting('whatsapp_number') ?><br>
                <?php endif; ?>
                <strong>🕒 Business Hours:</strong> <?= get_site_setting('business_hours', 'Mon-Fri 9AM-6PM') ?>
            </div>
        </div>

        <!-- Personal Note -->
        <div style="background: #e8f5e8; border-radius: 8px; padding: 20px; margin: 20px 0; border-left: 4px solid #28a745;">
            <p style="margin: 0; font-style: italic;">
                "We know how important your device is to you. Our goal is to get it back to you in perfect working condition,
                with the quality and care you deserve. We're here to answer any questions and make this process as smooth as possible."
            </p>
            <p style="margin: 10px 0 0 0; text-align: right; font-weight: bold;">
                - The <?= get_site_setting('site_name', 'Computer Repair Shop') ?> Team
            </p>
        </div>

        <p>We appreciate your business and look forward to hearing from you soon!</p>

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
            Quotation <?= $quotation['quotation_number'] ?> | Order <?= $quotation['order_number'] ?><br>
            To unsubscribe from reminders, please respond to this quotation or contact us directly.
        </p>
    </div>
</div>
</body>
</html>