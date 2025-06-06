<?php
// app/Models/QuotationModel.php
namespace App\Models;

use CodeIgniter\Model;

class QuotationModel extends Model
{
    protected $table = 'quotations';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'quotation_number', 'order_id', 'service_cost', 'parts_cost', 'additional_cost',
        'discount_amount', 'discount_percentage', 'tax_percentage', 'tax_amount', 'total_cost',
        'estimated_duration', 'warranty_period', 'terms_conditions', 'internal_notes',
        'status', 'valid_until', 'sent_at', 'responded_at', 'customer_notes',
        'approved_by_customer', 'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $beforeInsert = ['generateQuotationNumber', 'calculateTotals'];
    protected $beforeUpdate = ['calculateTotals'];

    /**
     * Generate unique quotation number
     */
    protected function generateQuotationNumber(array $data): array
    {
        if (!isset($data['data']['quotation_number'])) {
            $data['data']['quotation_number'] = 'QUO' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    /**
     * Calculate totals before save - FIXED VERSION
     */
    protected function calculateTotals(array $data): array
    {
        if (isset($data['data'])) {
            $quotation = $data['data'];

            // Convert string values to float for calculation
            $serviceCost = (float)($quotation['service_cost'] ?? 0);
            $partsCost = (float)($quotation['parts_cost'] ?? 0);
            $additionalCost = (float)($quotation['additional_cost'] ?? 0);
            $discountAmount = (float)($quotation['discount_amount'] ?? 0);
            $discountPercentage = (float)($quotation['discount_percentage'] ?? 0);
            $taxPercentage = (float)($quotation['tax_percentage'] ?? 0);

            // Calculate subtotal
            $subtotal = $serviceCost + $partsCost + $additionalCost;

            // Apply percentage discount if set (and override discount_amount)
            if ($discountPercentage > 0) {
                $discountAmount = ($subtotal * $discountPercentage) / 100;
                $data['data']['discount_amount'] = $discountAmount;
            }

            // Subtotal after discount
            $afterDiscount = $subtotal - $discountAmount;

            // Calculate tax
            $taxAmount = ($afterDiscount * $taxPercentage) / 100;
            $data['data']['tax_amount'] = $taxAmount;

            // Final total
            $totalCost = $afterDiscount + $taxAmount;
            $data['data']['total_cost'] = $totalCost;

            // Debug log untuk development
            if (ENVIRONMENT === 'development') {
                log_message('debug', 'Quotation calculation: service=' . $serviceCost . ', parts=' . $partsCost . ', additional=' . $additionalCost . ', total=' . $totalCost);
            }
        }

        return $data;
    }

    /**
     * Get quotations with order details
     */
    public function getQuotationsWithDetails($limit = null): array
    {
        $query = $this->select('
                quotations.*,
                repair_orders.order_number,
                repair_orders.status as order_status,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                customers.email as customer_email,
                device_types.name as device_type_name,
                users.full_name as created_by_name
            ')
            ->join('repair_orders', 'repair_orders.id = quotations.order_id')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = quotations.created_by', 'left')
            ->orderBy('quotations.created_at', 'DESC');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->findAll();
    }

    /**
     * Get quotation for specific order
     */
    public function getQuotationByOrder($orderId): object|array|null
    {
        return $this->select('
                quotations.*,
                repair_orders.order_number,
                repair_orders.status as order_status,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                customers.email as customer_email,
                device_types.name as device_type_name,
                users.full_name as created_by_name
            ')
            ->join('repair_orders', 'repair_orders.id = quotations.order_id')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = quotations.created_by', 'left')
            ->where('quotations.order_id', $orderId)
            ->orderBy('quotations.created_at', 'DESC')
            ->first();
    }

    /**
     * Get pending quotations (sent but not responded)
     */
    public function getPendingQuotations(): array
    {
        return $this->getQuotationsWithDetails()
            ->whereIn('quotations.status', ['sent'])
            ->where('quotations.valid_until >=', date('Y-m-d'));
    }

    /**
     * Get expired quotations
     */
    public function getExpiredQuotations(): array
    {
        return $this->getQuotationsWithDetails()
            ->where('quotations.status', 'sent')
            ->where('quotations.valid_until <', date('Y-m-d'));
    }

    /**
     * Approve quotation
     */
    public function approveQuotation($id, $customerNotes = null): bool
    {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by_customer' => true,
            'responded_at' => date('Y-m-d H:i:s'),
            'customer_notes' => $customerNotes
        ]);
    }

    /**
     * Reject quotation
     */
    public function rejectQuotation($id, $customerNotes = null): bool
    {
        return $this->update($id, [
            'status' => 'rejected',
            'approved_by_customer' => false,
            'responded_at' => date('Y-m-d H:i:s'),
            'customer_notes' => $customerNotes
        ]);
    }

    /**
     * Mark quotation as sent
     */
    public function markAsSent($id): bool
    {
        return $this->update($id, [
            'status' => 'sent',
            'sent_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get quotation statistics
     */
    public function getQuotationStats(): array
    {
        return [
            'total' => $this->countAll(),
            'draft' => $this->where('status', 'draft')->countAllResults(),
            'sent' => $this->where('status', 'sent')->countAllResults(),
            'approved' => $this->where('status', 'approved')->countAllResults(),
            'rejected' => $this->where('status', 'rejected')->countAllResults(),
            'expired' => $this->where('status', 'sent')
                ->where('valid_until <', date('Y-m-d'))
                ->countAllResults()
        ];
    }

    /**
     * Get quotation by ID with order details - IMPROVED VERSION
     */
    public function getQuotationWithOrderDetails($id): ?array
    {
        $result = $this->select('
                quotations.*,
                repair_orders.order_number,
                repair_orders.status as order_status,
                repair_orders.device_brand,
                repair_orders.device_model,
                repair_orders.problem_description,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                customers.email as customer_email,
                customers.address as customer_address,
                device_types.name as device_type_name,
                users.full_name as created_by_name
            ')
            ->join('repair_orders', 'repair_orders.id = quotations.order_id')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = quotations.created_by', 'left')
            ->where('quotations.id', $id)
            ->first();

        // DEBUGGING: Log the raw result
        if (ENVIRONMENT === 'development' && $result) {
            log_message('debug', 'Raw quotation data: ' . print_r($result, true));
        }

        return $result ? $result : null;
    }

    /**
     * Calculate quotation totals - MANUAL CALCULATION METHOD
     */
    public function calculateQuotationTotals($data): array
    {
        $serviceCost = (float)($data['service_cost'] ?? 0);
        $partsCost = (float)($data['parts_cost'] ?? 0);
        $additionalCost = (float)($data['additional_cost'] ?? 0);
        $discountAmount = (float)($data['discount_amount'] ?? 0);
        $discountPercentage = (float)($data['discount_percentage'] ?? 0);
        $taxPercentage = (float)($data['tax_percentage'] ?? 0);

        // Calculate subtotal
        $subtotal = $serviceCost + $partsCost + $additionalCost;

        // Apply percentage discount if set
        if ($discountPercentage > 0) {
            $discountAmount = ($subtotal * $discountPercentage) / 100;
        }

        // Subtotal after discount
        $afterDiscount = $subtotal - $discountAmount;

        // Calculate tax
        $taxAmount = ($afterDiscount * $taxPercentage) / 100;

        // Final total
        $totalCost = $afterDiscount + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'discount_amount' => $discountAmount,
            'tax_amount' => $taxAmount,
            'total_cost' => $totalCost
        ];
    }

    /**
     * Update quotation with revision
     */
    public function createRevision($originalId, $newData): bool|int|string
    {
        // Mark original as superseded
        $this->update($originalId, [
            'status' => 'superseded',
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Create new revision
        $newData['status'] = 'draft';
        $newData['created_at'] = date('Y-m-d H:i:s');
        $newData['updated_at'] = date('Y-m-d H:i:s');

        return $this->insert($newData);
    }

}