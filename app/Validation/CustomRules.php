<?php
namespace App\Validation;

use App\Models\RepairOrderModel;

class CustomRules
{
    /**
     * Validate phone number format
     */
    public function valid_phone(string $str, string &$error = null): bool
    {
        if (preg_match('/^[+]?[0-9\-\(\)\s]+$/', $str) && strlen($str) >= 10) {
            return true;
        }

        $error = 'The {field} field must contain a valid phone number.';
        return false;
    }

    /**
     * Check if technician is available
     */
    public function technician_available(string $str, string $params, array $data, string &$error = null): bool
    {
        if (empty($str)) return true; // Optional field

        $orderModel = new RepairOrderModel();
        $activeOrders = $orderModel->getTechnicianWorkload($str);

        $maxOrders = 5; // Maximum orders per technician

        if ($activeOrders >= $maxOrders) {
            $error = 'The selected technician has reached maximum workload.';
            return false;
        }

        return true;
    }

    /**
     * Validate order number format
     */
    public function valid_order_number(string $str, string &$error = null): bool
    {
        if (preg_match('/^ORD\d{8}\d{4}$/', $str)) {
            return true;
        }

        $error = 'The {field} field must be a valid order number format.';
        return false;
    }
}
