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