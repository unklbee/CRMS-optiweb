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
