<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PartModel;
use App\Models\StockMovementModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PartController extends BaseController
{
    protected PartModel $partModel;
    protected StockMovementModel $stockMovementModel;

    public function __construct()
    {
        $this->partModel = new PartModel();
        $this->stockMovementModel = new StockMovementModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $category = $this->request->getGet('category');

        $builder = $this->partModel;

        if ($search) {
            $builder = $builder->groupStart()
                ->like('name', $search)
                ->orLike('part_number', $search)
                ->orLike('brand', $search)
                ->groupEnd();
        }

        if ($category) {
            $builder = $builder->where('category', $category);
        }

        $parts = $builder->orderBy('name', 'ASC')->findAll();

        // Get categories for filter
        $categories = $this->partModel->select('category')
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->groupBy('category')
            ->findAll();

        $data = [
            'title' => 'Parts & Inventory',
            'parts' => $parts,
            'categories' => $categories,
            'search' => $search,
            'selected_category' => $category
        ];

        return view('admin/parts/index', $data);
    }

    public function show($id)
    {
        $part = $this->partModel->find($id);

        if (!$part) {
            throw new PageNotFoundException('Part not found');
        }

        // Get stock movement history
        $movementHistory = $this->stockMovementModel->getPartMovementHistory($id, 20);

        // Get part usage statistics
        $usageStats = $this->getPartUsageStats($id);

        $data = [
            'title' => 'Part Details',
            'part' => $part,
            'movement_history' => $movementHistory,
            'usage_stats' => $usageStats
        ];

        return view('admin/parts/show', $data);
    }

    private function getPartUsageStats($partId): array
    {
        // Get usage statistics from order_parts table and stock movements
        $orderPartModel = new \App\Models\OrderPartModel();

        $usage = $orderPartModel->select('
                COUNT(*) as times_used,
                SUM(quantity) as total_quantity_used,
                SUM(total_price) as total_revenue,
                MAX(order_parts.created_at) as last_used
            ')
            ->join('repair_orders', 'repair_orders.id = order_parts.order_id')
            ->where('part_id', $partId)
            ->where('repair_orders.status !=', 'cancelled')
            ->first();

        return $usage ?: [
            'times_used' => 0,
            'total_quantity_used' => 0,
            'total_revenue' => 0,
            'last_used' => null
        ];
    }

    public function new(): string
    {
        // Get existing categories for dropdown
        $categories = $this->partModel->select('category')
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->groupBy('category')
            ->findAll();

        $data = [
            'title' => 'Add New Part',
            'categories' => $categories
        ];

        return view('admin/parts/create', $data);
    }

    public function store()
    {
        $rules = [
            'part_number' => 'required|min_length[2]|max_length[50]|is_unique[parts.part_number]',
            'name' => 'required|min_length[2]|max_length[100]',
            'cost_price' => 'required|decimal',
            'selling_price' => 'required|decimal',
            'stock_quantity' => 'required|integer',
            'min_stock' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'part_number' => $this->request->getPost('part_number'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'brand' => $this->request->getPost('brand'),
            'cost_price' => $this->request->getPost('cost_price'),
            'selling_price' => $this->request->getPost('selling_price'),
            'stock_quantity' => $this->request->getPost('stock_quantity'),
            'min_stock' => $this->request->getPost('min_stock'),
            'location' => $this->request->getPost('location'),
            'status' => $this->request->getPost('status', 'active'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $partId = $this->partModel->insert($data);

        if ($partId) {
            // Record initial stock movement
            $initialStock = (int)$this->request->getPost('stock_quantity');
            if ($initialStock > 0) {
                $this->stockMovementModel->recordMovement([
                    'part_id' => $partId,
                    'movement_type' => 'add',
                    'quantity_before' => 0,
                    'quantity_change' => $initialStock,
                    'quantity_after' => $initialStock,
                    'reference_type' => 'initial',
                    'unit_cost' => $this->request->getPost('cost_price'),
                    'notes' => 'Initial stock when part was created',
                    'created_by' => session()->get('user_id')
                ]);
            }

            return redirect()->to('/admin/parts')->with('success', 'Part created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create part');
    }

    public function edit($id)
    {
        $part = $this->partModel->find($id);

        if (!$part) {
            throw new PageNotFoundException('Part not found');
        }

        // Get existing categories for dropdown
        $categories = $this->partModel->select('category')
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->groupBy('category')
            ->findAll();

        $data = [
            'title' => 'Edit Part',
            'part' => $part,
            'categories' => $categories
        ];

        return view('admin/parts/edit', $data);
    }

    public function update($id)
    {
        $part = $this->partModel->find($id);

        if (!$part) {
            throw new PageNotFoundException('Part not found');
        }

        $rules = [
            'part_number' => "required|min_length[2]|max_length[50]|is_unique[parts.part_number,id,{$id}]",
            'name' => 'required|min_length[2]|max_length[100]',
            'cost_price' => 'required|decimal',
            'selling_price' => 'required|decimal',
            'stock_quantity' => 'required|integer',
            'min_stock' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $newStockQuantity = (int)$this->request->getPost('stock_quantity');
        $oldStockQuantity = (int)$part['stock_quantity'];

        $data = [
            'part_number' => $this->request->getPost('part_number'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'brand' => $this->request->getPost('brand'),
            'cost_price' => $this->request->getPost('cost_price'),
            'selling_price' => $this->request->getPost('selling_price'),
            'stock_quantity' => $newStockQuantity,
            'min_stock' => $this->request->getPost('min_stock'),
            'location' => $this->request->getPost('location'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->partModel->update($id, $data)) {
            // Record stock movement if quantity changed manually
            if ($newStockQuantity !== $oldStockQuantity) {
                $quantityChange = $newStockQuantity - $oldStockQuantity;
                $movementType = $quantityChange > 0 ? 'add' : 'subtract';

                $this->stockMovementModel->recordMovement([
                    'part_id' => $id,
                    'movement_type' => $movementType,
                    'quantity_before' => $oldStockQuantity,
                    'quantity_change' => abs($quantityChange),
                    'quantity_after' => $newStockQuantity,
                    'reference_type' => 'adjustment',
                    'unit_cost' => $this->request->getPost('cost_price'),
                    'notes' => 'Manual stock adjustment during part edit',
                    'created_by' => session()->get('user_id')
                ]);
            }

            return redirect()->to('/admin/parts')->with('success', 'Part updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update part');
    }

    public function delete($id)
    {
        $part = $this->partModel->find($id);

        if (!$part) {
            throw new PageNotFoundException('Part not found');
        }

        if ($this->partModel->delete($id)) {
            return redirect()->to('/admin/parts')->with('success', 'Part deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete part');
    }

    public function adjustStock($id)
    {
        $part = $this->partModel->find($id);

        if (!$part) {
            throw new PageNotFoundException('Part not found');
        }

        // Get recent stock movements
        $recentMovements = $this->stockMovementModel->getPartMovementHistory($id, 10);

        $data = [
            'title' => 'Adjust Stock',
            'part' => $part,
            'recent_movements' => $recentMovements
        ];

        return view('admin/parts/adjust_stock', $data);
    }

    public function updateStock($id)
    {
        $part = $this->partModel->find($id);

        if (!$part) {
            throw new PageNotFoundException('Part not found');
        }

        $rules = [
            'adjustment_type' => 'required|in_list[add,subtract,set]',
            'quantity' => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $adjustmentType = $this->request->getPost('adjustment_type');
        $quantity = (int)$this->request->getPost('quantity');
        $notes = $this->request->getPost('notes');

        $currentStock = (int)$part['stock_quantity'];
        $newStock = $currentStock;

        switch ($adjustmentType) {
            case 'add':
                $newStock = $currentStock + $quantity;
                $quantityChange = $quantity;
                break;
            case 'subtract':
                $newStock = max(0, $currentStock - $quantity);
                $quantityChange = $currentStock - $newStock; // Actual amount subtracted
                break;
            case 'set':
                $newStock = $quantity;
                $quantityChange = abs($newStock - $currentStock);
                $adjustmentType = $newStock > $currentStock ? 'add' : 'subtract';
                break;
        }

        // Update part stock
        $updateData = [
            'stock_quantity' => $newStock,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->partModel->update($id, $updateData)) {
            // Record stock movement
            if ($quantityChange > 0) {
                $this->stockMovementModel->recordMovement([
                    'part_id' => $id,
                    'movement_type' => $adjustmentType,
                    'quantity_before' => $currentStock,
                    'quantity_change' => $quantityChange,
                    'quantity_after' => $newStock,
                    'reference_type' => 'adjustment',
                    'unit_cost' => $part['cost_price'],
                    'notes' => $notes ?: 'Manual stock adjustment',
                    'created_by' => session()->get('user_id')
                ]);
            }

            $message = "Stock adjusted from {$currentStock} to {$newStock}";
            if ($notes) {
                $message .= ". Notes: {$notes}";
            }

            return redirect()->to('/admin/parts')->with('success', $message);
        }

        return redirect()->back()->with('error', 'Failed to adjust stock');
    }

    /**
     * Record part usage from order
     */
    public function recordPartUsage($partId, $quantity, $orderId, $unitPrice = null): bool
    {
        $part = $this->partModel->find($partId);
        if (!$part) {
            return false;
        }

        $currentStock = (int)$part['stock_quantity'];
        $actualQuantityUsed = min($quantity, $currentStock); // Can't use more than available
        $newStock = $currentStock - $actualQuantityUsed;

        // Update part stock
        $this->partModel->update($partId, [
            'stock_quantity' => $newStock,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Record stock movement
        $this->stockMovementModel->recordMovement([
            'part_id' => $partId,
            'movement_type' => 'use',
            'quantity_before' => $currentStock,
            'quantity_change' => $actualQuantityUsed,
            'quantity_after' => $newStock,
            'reference_type' => 'order',
            'reference_id' => $orderId,
            'unit_cost' => $unitPrice ?: $part['cost_price'],
            'notes' => "Used in repair order #{$orderId}",
            'created_by' => session()->get('user_id')
        ]);

        return true;
    }

    /**
     * Get movements for AJAX API
     */
    public function getMovements($id)
    {
        $this->response->setContentType('application/json');

        $part = $this->partModel->find($id);
        if (!$part) {
            return $this->response->setJSON(['error' => 'Part not found'])->setStatusCode(404);
        }

        $movements = $this->stockMovementModel->getPartMovementHistory($id, 20);

        return $this->response->setJSON([
            'success' => true,
            'movements' => $movements
        ]);
    }

    /**
     * Get usage stats for AJAX API
     */
    public function getUsageStats($id)
    {
        $this->response->setContentType('application/json');

        $part = $this->partModel->find($id);
        if (!$part) {
            return $this->response->setJSON(['error' => 'Part not found'])->setStatusCode(404);
        }

        $usageStats = $this->getPartUsageStats($id);

        return $this->response->setJSON([
            'success' => true,
            'stats' => $usageStats
        ]);
    }

// ===========================================================================
// TAMBAHAN METHODS UNTUK OrderController.php (tambahkan ke file yang sudah ada)

    /**
     * Get order parts for AJAX API
     */
    public function getOrderParts($id)
    {
        $this->response->setContentType('application/json');

        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->response->setJSON(['error' => 'Order not found'])->setStatusCode(404);
        }

        $orderParts = $this->orderPartModel->getOrderParts($id);

        return $this->response->setJSON([
            'success' => true,
            'parts' => $orderParts
        ]);
    }

    /**
     * Get order movements for AJAX API
     */
    public function getOrderMovements($id)
    {
        $this->response->setContentType('application/json');

        $order = $this->orderModel->find($id);
        if (!$order) {
            return $this->response->setJSON(['error' => 'Order not found'])->setStatusCode(404);
        }

        $stockMovements = $this->stockMovementModel->getMovementsByReference('order', $id);

        return $this->response->setJSON([
            'success' => true,
            'movements' => $stockMovements
        ]);
    }

// ===========================================================================
// TAMBAHAN METHODS UNTUK DashboardController.php (tambahkan ke file yang sudah ada)

    /**
     * Get dashboard stats for AJAX API
     */
    public function getStats()
    {
        $this->response->setContentType('application/json');

        $orderModel = new RepairOrderModel();
        $customerModel = new CustomerModel();
        $partModel = new PartModel();
        $stockMovementModel = new StockMovementModel();

        $stats = [
            'orders' => [
                'total' => $orderModel->countAll(),
                'pending' => $orderModel->where('status', 'received')->countAllResults(),
                'in_progress' => $orderModel->where('status', 'in_progress')->countAllResults(),
                'completed' => $orderModel->where('status', 'completed')->countAllResults(),
            ],
            'parts' => [
                'total' => $partModel->countAll(),
                'low_stock' => $partModel->where('stock_quantity <=', 'min_stock', false)->countAllResults(),
                'out_of_stock' => $partModel->where('stock_quantity', 0)->countAllResults(),
            ],
            'customers' => [
                'total' => $customerModel->countAll(),
            ],
            'movements_today' => $stockMovementModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults()
        ];

        return $this->response->setJSON([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Get recent activities for AJAX API
     */
    public function getRecentActivities()
    {
        $this->response->setContentType('application/json');

        $stockMovementModel = new StockMovementModel();
        $orderModel = new RepairOrderModel();

        $recentMovements = $stockMovementModel->getRecentMovements(10);
        $recentOrders = $orderModel->getOrdersWithDetails(10);

        return $this->response->setJSON([
            'success' => true,
            'recent_movements' => $recentMovements,
            'recent_orders' => $recentOrders
        ]);
    }

    /**
     * Search parts for AJAX API
     */
    public function searchParts()
    {
        $this->response->setContentType('application/json');

        $search = $this->request->getGet('q');
        if (!$search || strlen($search) < 2) {
            return $this->response->setJSON(['error' => 'Search term too short'])->setStatusCode(400);
        }

        $partModel = new PartModel();
        $parts = $partModel->like('name', $search)
            ->orLike('part_number', $search)
            ->where('status', 'active')
            ->limit(20)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'parts' => $parts
        ]);
    }

    /**
     * Search customers for AJAX API
     */
    public function searchCustomers()
    {
        $this->response->setContentType('application/json');

        $search = $this->request->getGet('q');
        if (!$search || strlen($search) < 2) {
            return $this->response->setJSON(['error' => 'Search term too short'])->setStatusCode(400);
        }

        $customerModel = new CustomerModel();
        $customers = $customerModel->like('full_name', $search)
            ->orLike('phone', $search)
            ->orLike('email', $search)
            ->limit(20)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'customers' => $customers
        ]);
    }
}