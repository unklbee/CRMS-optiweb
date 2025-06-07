<?php $this->extend('admin/layout/main'); ?>

<?php $this->section('content'); ?>

    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800"><?= $title ?></h1>
                    <p class="text-gray-600 mt-1">Diagnosis details for order #<?= $order['order_number'] ?></p>
                </div>
                <div class="flex space-x-3">
                    <a href="/admin/orders/<?= $order['id'] ?>"
                       class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Order
                    </a>
                    <?php if ($order['diagnosis_status'] === 'completed' || $order['status'] === 'waiting_approval'): ?>
                        <a href="/admin/diagnosis/<?= $order['id'] ?>/edit"
                           class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-edit mr-2"></i>Edit Diagnosis
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Order Info -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Order Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Order Information</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">Order Number:</span>
                            <span class="font-medium ml-2"><?= $order['order_number'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Customer:</span>
                            <span class="font-medium ml-2"><?= $order['customer_name'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Device:</span>
                            <span class="font-medium ml-2"><?= $order['device_brand'] ?> <?= $order['device_model'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Device Type:</span>
                            <span class="font-medium ml-2"><?= $order['device_type_name'] ?></span>
                        </div>
                        <div>
                            <span class="text-gray-600">Status:</span>
                            <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium
                              <?= ($order['status'] === 'diagnosed') ? 'bg-green-100 text-green-800' :
                                (($order['status'] === 'received') ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                        </div>
                    </div>
                </div>

                <!-- Diagnosis Status -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Diagnosis Status</h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-gray-600">Status:</span>
                            <span class="ml-2 px-2 py-1 rounded-full text-xs font-medium
                              <?= ($order['diagnosis_status'] === 'completed') ? 'bg-green-100 text-green-800' :
                                (($order['diagnosis_status'] === 'in_progress') ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') ?>">
                            <?= ucfirst(str_replace('_', ' ', $order['diagnosis_status'] ?: 'pending')) ?>
                        </span>
                        </div>
                        <?php if ($order['diagnosed_by_name']): ?>
                            <div>
                                <span class="text-gray-600">Diagnosed by:</span>
                                <span class="ml-2 font-medium"><?= $order['diagnosed_by_name'] ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if ($order['diagnosis_date']): ?>
                            <div>
                                <span class="text-gray-600">Diagnosis Date:</span>
                                <span class="ml-2 font-medium"><?= date('M d, Y H:i', strtotime($order['diagnosis_date'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer Problem -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Customer's Problem</h3>
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <p class="text-gray-900 text-sm"><?= nl2br($order['problem_description']) ?></p>
                    </div>
                    <?php if ($order['accessories']): ?>
                        <div class="mt-4">
                            <h4 class="font-medium text-gray-800 mb-2">Accessories</h4>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-900 text-sm"><?= nl2br($order['accessories']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right Column - Diagnosis Details -->
            <div class="lg:col-span-2 space-y-6">

                <?php if (!empty($order['diagnosis_notes'])): ?>
                    <!-- Diagnosis Notes -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Diagnosis Notes</h3>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-gray-900"><?= nl2br($order['diagnosis_notes']) ?></p>
                        </div>
                    </div>

                    <!-- Issues Found -->
                    <?php if (!empty($order['issues_found'])): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Issues Found</h3>
                            <div class="space-y-3">
                                <?php
                                $issues = is_string($order['issues_found']) ? json_decode($order['issues_found'], true) : $order['issues_found'];
                                if ($issues && is_array($issues)):
                                    foreach ($issues as $issue): ?>
                                        <div class="border border-gray-200 rounded-lg p-4">
                                            <div class="flex justify-between items-start mb-2">
                                                <h4 class="font-medium text-gray-800"><?= htmlspecialchars($issue['description']) ?></h4>
                                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                                  <?= ($issue['severity'] === 'high') ? 'bg-red-100 text-red-800' :
                                                    (($issue['severity'] === 'medium') ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') ?>">
                                                <?= ucfirst($issue['severity']) ?>
                                            </span>
                                            </div>
                                            <div class="text-sm text-gray-600">
                                                <span class="font-medium">Repair Needed:</span>
                                                <span class="ml-1"><?= ($issue['repair_needed'] ?? true) ? 'Yes' : 'No' ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach;
                                else: ?>
                                    <p class="text-gray-600 italic">No issues recorded</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Recommended Actions -->
                    <?php if (!empty($order['recommended_actions'])): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Recommended Actions</h3>
                            <div class="bg-green-50 p-4 rounded-lg">
                                <p class="text-gray-900"><?= nl2br($order['recommended_actions']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Estimated Hours -->
                    <?php if (!empty($order['estimated_hours'])): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Estimated Work Hours</h3>
                            <div class="text-2xl font-bold text-blue-600">
                                <?= $order['estimated_hours'] ?> hours
                            </div>
                            <p class="text-gray-600 text-sm mt-1">Estimated time to complete the repair</p>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- No Diagnosis Yet -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                        <div class="text-gray-400 mb-4">
                            <i class="fas fa-stethoscope text-4xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Diagnosis Yet</h3>
                        <p class="text-gray-600 mb-4">This order has not been diagnosed yet.</p>

                        <?php if (in_array($order['diagnosis_status'], ['pending', 'in_progress'])): ?>
                            <div class="space-y-3">
                                <a href="/admin/diagnosis/<?= $order['id'] ?>/create"
                                   class="inline-block bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>Start Diagnosis
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Action Buttons -->
        <?php if (!empty($order['diagnosis_notes']) && $order['diagnosis_status'] === 'completed'): ?>
            <div class="mt-6 flex justify-center space-x-4">
                <a href="/admin/quotations/create/<?= $order['id'] ?>"
                   class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-colors">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>Create Quotation
                </a>
                <a href="/admin/orders/<?= $order['id'] ?>/repair"
                   class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 transition-colors">
                    <i class="fas fa-tools mr-2"></i>Start Repair
                </a>
            </div>
        <?php endif; ?>

    </div>

<?php $this->endSection(); ?>