<?php

if (!function_exists('format_currency')) {
    /**
     * Format currency to Indonesian Rupiah
     */
    function format_currency($amount, $symbol = 'Rp ')
    {
        return $symbol . number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('time_ago')) {
    /**
     * Convert timestamp to time ago format
     */
    function time_ago($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 2592000) return floor($time/86400) . ' days ago';
        if ($time < 31536000) return floor($time/2592000) . ' months ago';

        return floor($time/31536000) . ' years ago';
    }
}

if (!function_exists('status_badge')) {
    /**
     * Generate status badge HTML
     */
    function status_badge($status, $type = 'order')
    {
        $classes = [
            'order' => [
                'received' => 'bg-yellow-100 text-yellow-800',
                'diagnosed' => 'bg-blue-100 text-blue-800',
                'waiting_approval' => 'bg-orange-100 text-orange-800',
                'in_progress' => 'bg-blue-100 text-blue-800',
                'waiting_parts' => 'bg-purple-100 text-purple-800',
                'completed' => 'bg-green-100 text-green-800',
                'delivered' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800'
            ],
            'priority' => [
                'low' => 'bg-gray-100 text-gray-800',
                'normal' => 'bg-blue-100 text-blue-800',
                'high' => 'bg-orange-100 text-orange-800',
                'urgent' => 'bg-red-100 text-red-800'
            ]
        ];

        $class = $classes[$type][$status] ?? 'bg-gray-100 text-gray-800';
        $label = ucfirst(str_replace('_', ' ', $status));

        return "<span class=\"px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {$class}\">{$label}</span>";
    }
}

if (!function_exists('get_avatar')) {
    /**
     * Generate avatar for user
     */
    function get_avatar($name, $size = 40)
    {
        $initials = '';
        $words = explode(' ', $name);
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
        }
        $initials = substr($initials, 0, 2);

        return "https://ui-avatars.com/api/?name={$initials}&size={$size}&background=3B82F6&color=fff";
    }
}

if (!function_exists('truncate_text')) {
    /**
     * Truncate text with ellipsis
     */
    function truncate_text($text, $length = 100, $suffix = '...')
    {
        if (strlen($text) <= $length) {
            return $text;
        }

        return substr($text, 0, $length) . $suffix;
    }
}

if (!function_exists('generate_order_number')) {
    /**
     * Generate unique order number
     */
    function generate_order_number()
    {
        return 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('get_site_setting')) {
    /**
     * Get site setting from database
     */
    function get_site_setting($key, $default = null)
    {
        static $settings = null;

        if ($settings === null) {
            $settingModel = new \App\Models\CmsSettingModel();
            $settings = $settingModel->getAllSettings();
        }

        return $settings[$key] ?? $default;
    }
}

if (!function_exists('breadcrumb')) {
    /**
     * Generate breadcrumb navigation
     */
    function breadcrumb($items = [])
    {
        if (empty($items)) return '';

        $html = '<nav class="flex mb-4" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">';

        foreach ($items as $index => $item) {
            $isLast = ($index === count($items) - 1);

            if ($isLast) {
                $html .= '<li aria-current="page">
                            <div class="flex items-center">
                                <i class="fas fa-chevron-right text-gray-400 mr-2"></i>
                                <span class="text-gray-500">' . esc($item['title']) . '</span>
                            </div>
                          </li>';
            } else {
                $html .= '<li>
                            <div class="flex items-center">
                                ' . ($index > 0 ? '<i class="fas fa-chevron-right text-gray-400 mr-2"></i>' : '') . '
                                <a href="' . esc($item['url']) . '" class="text-blue-600 hover:text-blue-800">
                                    ' . esc($item['title']) . '
                                </a>
                            </div>
                          </li>';
            }
        }

        $html .= '</ol></nav>';

        return $html;
    }
}

if (!function_exists('alert_message')) {
    /**
     * Generate alert message HTML
     */
    function alert_message($message, $type = 'info', $dismissible = true)
    {
        $types = [
            'success' => 'bg-green-100 border-green-400 text-green-700',
            'error' => 'bg-red-100 border-red-400 text-red-700',
            'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
            'info' => 'bg-blue-100 border-blue-400 text-blue-700'
        ];

        $class = $types[$type] ?? $types['info'];
        $dismissBtn = $dismissible ? '<button onclick="this.parentElement.remove()" class="float-right font-bold">&times;</button>' : '';

        return "<div class=\"{$class} px-4 py-3 rounded border mb-4\">{$dismissBtn}<span>{$message}</span></div>";
    }
}

if (!function_exists('get_status_icon')) {
    /**
     * Get icon for order status
     */
    function get_status_icon($status)
    {
        $icons = [
            'received' => 'inbox',
            'diagnosed' => 'search',
            'waiting_approval' => 'clock',
            'in_progress' => 'wrench',
            'waiting_parts' => 'box',
            'completed' => 'check-circle',
            'delivered' => 'truck',
            'cancelled' => 'times-circle'
        ];
        return $icons[$status] ?? 'circle';
    }
}

if (!function_exists('get_status_description')) {
    /**
     * Get description for order status
     */
    function get_status_description($status)
    {
        $descriptions = [
            'received' => 'Order has been received and logged',
            'diagnosed' => 'Device has been examined and diagnosed',
            'waiting_approval' => 'Awaiting customer approval for repair',
            'in_progress' => 'Repair work is currently in progress',
            'waiting_parts' => 'Waiting for replacement parts to arrive',
            'completed' => 'Repair has been completed successfully',
            'delivered' => 'Device has been delivered to customer',
            'cancelled' => 'Order has been cancelled'
        ];
        return $descriptions[$status] ?? '';
    }
}

if (!function_exists('get_status_progress')) {
    /**
     * Get progress percentage for order status
     */
    function get_status_progress($status)
    {
        $progress = [
            'received' => 10,
            'diagnosed' => 25,
            'waiting_approval' => 40,
            'in_progress' => 60,
            'waiting_parts' => 50,
            'completed' => 90,
            'delivered' => 100,
            'cancelled' => null
        ];
        return $progress[$status];
    }
}

if (!function_exists('get_status_color')) {
    /**
     * Get color class for order status
     */
    function get_status_color($status)
    {
        $colors = [
            'received' => 'bg-yellow-100 text-yellow-800',
            'diagnosed' => 'bg-blue-100 text-blue-800',
            'waiting_approval' => 'bg-orange-100 text-orange-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'waiting_parts' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'delivered' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800'
        ];
        return $colors[$status] ?? 'bg-gray-100 text-gray-800';
    }
}

if (!function_exists('get_priority_color')) {
    /**
     * Get color class for order priority
     */
    function get_priority_color($priority)
    {
        $colors = [
            'low' => 'bg-gray-100 text-gray-800',
            'normal' => 'bg-blue-100 text-blue-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800'
        ];
        return $colors[$priority] ?? 'bg-gray-100 text-gray-800';
    }
}

if (!function_exists('format_order_status')) {
    /**
     * Format order status with badge
     */
    function format_order_status($status)
    {
        $statusLabels = [
            'received' => 'Received',
            'diagnosed' => 'Diagnosed',
            'waiting_approval' => 'Waiting Approval',
            'in_progress' => 'In Progress',
            'waiting_parts' => 'Waiting Parts',
            'completed' => 'Completed',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        $label = $statusLabels[$status] ?? ucfirst($status);
        $color = get_status_color($status);

        return "<span class=\"px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {$color}\">{$label}</span>";
    }
}

if (!function_exists('format_order_priority')) {
    /**
     * Format order priority with badge
     */
    function format_order_priority($priority)
    {
        $color = get_priority_color($priority);
        $label = ucfirst($priority);

        return "<span class=\"px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {$color}\">{$label}</span>";
    }
}

if (!function_exists('can_change_status')) {
    /**
     * Check if status can be changed from current to new status
     */
    function can_change_status($currentStatus, $newStatus)
    {
        // Define valid status transitions
        $validTransitions = [
            'received' => ['diagnosed', 'cancelled'],
            'diagnosed' => ['waiting_approval', 'in_progress', 'cancelled'],
            'waiting_approval' => ['in_progress', 'cancelled'],
            'in_progress' => ['waiting_parts', 'completed', 'cancelled'],
            'waiting_parts' => ['in_progress', 'cancelled'],
            'completed' => ['delivered'],
            'delivered' => [], // Final state
            'cancelled' => [] // Final state
        ];

        return in_array($newStatus, $validTransitions[$currentStatus] ?? []);
    }
}

if (!function_exists('get_next_status_options')) {
    /**
     * Get available next status options for current status
     */
    function get_next_status_options($currentStatus)
    {
        $statusLabels = [
            'received' => 'Received',
            'diagnosed' => 'Diagnosed',
            'waiting_approval' => 'Waiting Approval',
            'in_progress' => 'In Progress',
            'waiting_parts' => 'Waiting Parts',
            'completed' => 'Completed',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        $validTransitions = [
            'received' => ['diagnosed', 'cancelled'],
            'diagnosed' => ['waiting_approval', 'in_progress', 'cancelled'],
            'waiting_approval' => ['in_progress', 'cancelled'],
            'in_progress' => ['waiting_parts', 'completed', 'cancelled'],
            'waiting_parts' => ['in_progress', 'cancelled'],
            'completed' => ['delivered'],
            'delivered' => [],
            'cancelled' => []
        ];

        $options = [];
        $nextStatuses = $validTransitions[$currentStatus] ?? [];

        foreach ($nextStatuses as $status) {
            $options[$status] = $statusLabels[$status];
        }

        return $options;
    }
}


if (!function_exists('record_stock_movement')) {
    /**
     * Helper function to record stock movement
     */
    function record_stock_movement($partId, $movementType, $quantityBefore, $quantityChange, $quantityAfter, $referenceType = 'manual', $referenceId = null, $unitCost = 0, $notes = null, $createdBy = null)
    {
        $stockMovementModel = new \App\Models\StockMovementModel();

        return $stockMovementModel->recordMovement([
            'part_id' => $partId,
            'movement_type' => $movementType,
            'quantity_before' => $quantityBefore,
            'quantity_change' => $quantityChange,
            'quantity_after' => $quantityAfter,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'unit_cost' => $unitCost,
            'total_cost' => $unitCost * $quantityChange,
            'notes' => $notes,
            'created_by' => $createdBy ?: session()->get('user_id')
        ]);
    }
}

if (!function_exists('update_part_stock')) {
    /**
     * Helper function to update part stock and record movement
     */
    function update_part_stock($partId, $newQuantity, $movementType, $referenceType = 'manual', $referenceId = null, $notes = null, $unitCost = null)
    {
        $partModel = new \App\Models\PartModel();
        $part = $partModel->find($partId);

        if (!$part) {
            return false;
        }

        $oldQuantity = (int)$part['stock_quantity'];
        $quantityChange = abs($newQuantity - $oldQuantity);

        // Update part stock
        $updateResult = $partModel->update($partId, [
            'stock_quantity' => $newQuantity,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($updateResult && $quantityChange > 0) {
            // Record stock movement
            record_stock_movement(
                $partId,
                $movementType,
                $oldQuantity,
                $quantityChange,
                $newQuantity,
                $referenceType,
                $referenceId,
                $unitCost ?: $part['cost_price'],
                $notes
            );
        }

        return $updateResult;
    }
}

if (!function_exists('use_part_for_order')) {
    /**
     * Helper function to use part in an order
     */
    function use_part_for_order($partId, $quantity, $orderId, $unitPrice = null, $notes = null)
    {
        $partModel = new \App\Models\PartModel();
        $part = $partModel->find($partId);

        if (!$part) {
            return false;
        }

        $currentStock = (int)$part['stock_quantity'];

        // Check if enough stock available
        if ($currentStock < $quantity) {
            return false;
        }

        $newStock = $currentStock - $quantity;
        $price = $unitPrice ?: $part['selling_price'];

        // Update stock
        $updateResult = $partModel->update($partId, [
            'stock_quantity' => $newStock,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($updateResult) {
            // Record movement
            record_stock_movement(
                $partId,
                'use',
                $currentStock,
                $quantity,
                $newStock,
                'order',
                $orderId,
                $price,
                $notes ?: "Used in order #{$orderId}"
            );

            return true;
        }

        return false;
    }
}

if (!function_exists('return_part_from_order')) {
    /**
     * Helper function to return part to stock from an order
     */
    function return_part_from_order($partId, $quantity, $orderId, $unitPrice = null, $notes = null)
    {
        $partModel = new \App\Models\PartModel();
        $part = $partModel->find($partId);

        if (!$part) {
            return false;
        }

        $currentStock = (int)$part['stock_quantity'];
        $newStock = $currentStock + $quantity;
        $price = $unitPrice ?: $part['cost_price'];

        // Update stock
        $updateResult = $partModel->update($partId, [
            'stock_quantity' => $newStock,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if ($updateResult) {
            // Record movement
            record_stock_movement(
                $partId,
                'return',
                $currentStock,
                $quantity,
                $newStock,
                'order',
                $orderId,
                $price,
                $notes ?: "Returned from order #{$orderId}"
            );

            return true;
        }

        return false;
    }
}

if (!function_exists('get_low_stock_parts')) {
    /**
     * Get parts with low stock
     */
    function get_low_stock_parts($limit = null)
    {
        $partModel = new \App\Models\PartModel();
        $query = $partModel->where('stock_quantity <=', 'min_stock', false)
            ->where('status', 'active')
            ->orderBy('stock_quantity', 'ASC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->findAll();
    }
}

if (!function_exists('get_part_usage_stats')) {
    /**
     * Get part usage statistics
     */
    function get_part_usage_stats($partId, $days = 30)
    {
        $stockMovementModel = new \App\Models\StockMovementModel();
        $orderPartModel = new \App\Models\OrderPartModel();

        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        // Get usage from stock movements
        $movements = $stockMovementModel
            ->where('part_id', $partId)
            ->where('movement_type IN', ['use', 'subtract'])
            ->where('created_at >=', $startDate)
            ->findAll();

        // Get revenue from order parts
        $orderParts = $orderPartModel
            ->select('order_parts.*, repair_orders.status')
            ->join('repair_orders', 'repair_orders.id = order_parts.order_id')
            ->where('order_parts.part_id', $partId)
            ->where('order_parts.created_at >=', $startDate)
            ->where('repair_orders.status !=', 'cancelled')
            ->findAll();

        $totalUsed = array_sum(array_column($movements, 'quantity_change'));
        $totalRevenue = array_sum(array_column($orderParts, 'total_price'));
        $timesUsed = count($orderParts);

        return [
            'total_used' => $totalUsed,
            'total_revenue' => $totalRevenue,
            'times_used' => $timesUsed,
            'days_period' => $days,
            'movements' => $movements,
            'order_parts' => $orderParts
        ];
    }
}

if (!function_exists('get_stock_value_summary')) {
    /**
     * Get stock value summary
     */
    function get_stock_value_summary()
    {
        $partModel = new \App\Models\PartModel();
        $parts = $partModel->where('status', 'active')->findAll();

        $totalCostValue = 0;
        $totalSellingValue = 0;
        $lowStockCount = 0;
        $outOfStockCount = 0;

        foreach ($parts as $part) {
            $stock = (int)$part['stock_quantity'];
            $totalCostValue += $stock * $part['cost_price'];
            $totalSellingValue += $stock * $part['selling_price'];

            if ($stock <= 0) {
                $outOfStockCount++;
            } elseif ($stock <= $part['min_stock']) {
                $lowStockCount++;
            }
        }

        return [
            'total_parts' => count($parts),
            'total_cost_value' => $totalCostValue,
            'total_selling_value' => $totalSellingValue,
            'potential_profit' => $totalSellingValue - $totalCostValue,
            'low_stock_count' => $lowStockCount,
            'out_of_stock_count' => $outOfStockCount
        ];
    }
}

if (!function_exists('get_recent_stock_movements')) {
    /**
     * Get recent stock movements across all parts
     */
    function get_recent_stock_movements($limit = 20)
    {
        $stockMovementModel = new \App\Models\StockMovementModel();

        return $stockMovementModel->getRecentMovements($limit);
    }
}

if (!function_exists('get_parts_needing_reorder')) {
    /**
     * Get parts that need reordering
     */
    function get_parts_needing_reorder()
    {
        $partModel = new \App\Models\PartModel();

        return $partModel->select('
                parts.*,
                (min_stock - stock_quantity) as shortage_quantity,
                (min_stock - stock_quantity) * cost_price as reorder_cost
            ')
            ->where('stock_quantity <', 'min_stock', false)
            ->where('status', 'active')
            ->orderBy('shortage_quantity', 'DESC')
            ->findAll();
    }
}

if (!function_exists('calculate_inventory_turnover')) {
    /**
     * Calculate inventory turnover for a part
     */
    function calculate_inventory_turnover($partId, $days = 365)
    {
        $stockMovementModel = new \App\Models\StockMovementModel();
        $partModel = new \App\Models\PartModel();

        $part = $partModel->find($partId);
        if (!$part) {
            return null;
        }

        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        // Get total used in the period
        $totalUsed = $stockMovementModel
            ->selectSum('quantity_change', 'total_used')
            ->where('part_id', $partId)
            ->where('movement_type IN', ['use', 'subtract'])
            ->where('created_at >=', $startDate)
            ->first()['total_used'] ?? 0;

        $averageInventory = $part['stock_quantity']; // Simplified - could be more sophisticated

        if ($averageInventory > 0) {
            $turnoverRatio = $totalUsed / $averageInventory;
            $daysToTurnover = $averageInventory > 0 ? ($days / $turnoverRatio) : 0;

            return [
                'turnover_ratio' => $turnoverRatio,
                'days_to_turnover' => $daysToTurnover,
                'total_used' => $totalUsed,
                'average_inventory' => $averageInventory,
                'period_days' => $days
            ];
        }

        return null;
    }
}

if (!function_exists('generate_stock_report')) {
    /**
     * Generate comprehensive stock report
     */
    function generate_stock_report($format = 'array')
    {
        $summary = get_stock_value_summary();
        $lowStockParts = get_low_stock_parts();
        $reorderParts = get_parts_needing_reorder();
        $recentMovements = get_recent_stock_movements(50);

        $report = [
            'generated_at' => date('Y-m-d H:i:s'),
            'summary' => $summary,
            'low_stock_parts' => $lowStockParts,
            'parts_needing_reorder' => $reorderParts,
            'recent_movements' => $recentMovements,
            'recommendations' => []
        ];

        // Add recommendations
        if (count($lowStockParts) > 0) {
            $report['recommendations'][] = [
                'type' => 'warning',
                'message' => count($lowStockParts) . ' parts have low stock levels and need attention.'
            ];
        }

        if (count($reorderParts) > 0) {
            $totalReorderCost = array_sum(array_column($reorderParts, 'reorder_cost'));
            $report['recommendations'][] = [
                'type' => 'action',
                'message' => 'Consider reordering ' . count($reorderParts) . ' parts. Estimated cost: ' . format_currency($totalReorderCost)
            ];
        }

        if ($summary['potential_profit'] > 0) {
            $profitMargin = ($summary['potential_profit'] / $summary['total_selling_value']) * 100;
            $report['recommendations'][] = [
                'type' => 'info',
                'message' => 'Current inventory profit margin: ' . number_format($profitMargin, 1) . '%'
            ];
        }

        return $report;
    }
}