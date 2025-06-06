<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RepairOrderModel;
use App\Models\CustomerModel;
use App\Models\DeviceTypeModel;
use App\Models\UserModel;
use App\Models\OrderStatusHistoryModel;
use App\Models\DiagnosisTemplateModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class DiagnosisController extends BaseController
{
    protected RepairOrderModel $orderModel;
    protected CustomerModel $customerModel;
    protected DeviceTypeModel $deviceTypeModel;
    protected UserModel $userModel;
    protected DiagnosisTemplateModel $templateModel;

    public function __construct()
    {
        $this->orderModel = new RepairOrderModel();
        $this->customerModel = new CustomerModel();
        $this->deviceTypeModel = new DeviceTypeModel();
        $this->userModel = new UserModel();
        $this->templateModel = new DiagnosisTemplateModel();
    }

    /**
     * Show diagnosis queue/list
     */
    public function index(): string
    {
        $perPage = 20;
        $page = $this->request->getGet('page') ?? 1;

        // Get filter parameters
        $status = $this->request->getGet('status');
        $deviceType = $this->request->getGet('device_type');
        $technician = $this->request->getGet('technician');
        $search = $this->request->getGet('search');

        // Build main query
        $builder = $this->orderModel->select('
            repair_orders.*,
            customers.full_name as customer_name,
            customers.phone as customer_phone,
            customers.email as customer_email,
            device_types.name as device_type_name,
            diagnosed_by_user.full_name as diagnosed_by_name,
            repair_orders.diagnosis_date
        ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users as diagnosed_by_user', 'diagnosed_by_user.id = repair_orders.diagnosed_by', 'left');

        // Apply filters
        if ($status) {
            $builder->where('repair_orders.diagnosis_status', $status);
        } else {
            // Show orders that need diagnosis attention
            $builder->whereIn('repair_orders.diagnosis_status', ['pending', 'in_progress', 'completed']);
        }

        if ($deviceType) {
            $builder->where('repair_orders.device_type_id', $deviceType);
        }

        if ($technician) {
            $builder->where('repair_orders.diagnosed_by', $technician);
        }

        if ($search) {
            $builder->groupStart()
                ->like('repair_orders.order_number', $search)
                ->orLike('customers.full_name', $search)
                ->orLike('customers.phone', $search)
                ->orLike('repair_orders.device_brand', $search)
                ->orLike('repair_orders.device_model', $search)
                ->groupEnd();
        }

        // Order by priority and date
        $builder->orderBy('repair_orders.priority', 'DESC')
            ->orderBy('repair_orders.created_at', 'ASC');

        // Get paginated results
        $orders = $builder->paginate($perPage);
        $pager = $this->orderModel->pager;

        // Get statistics
        $stats = $this->getDiagnosisStatistics();

        // Get priority orders (high priority, pending diagnosis)
        $priorityOrders = $this->orderModel->select('
            repair_orders.id,
            repair_orders.order_number,
            customers.full_name as customer_name
        ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->where('repair_orders.diagnosis_status', 'pending')
            ->where('repair_orders.priority', 'high')
            ->orderBy('repair_orders.created_at', 'ASC')
            ->limit(10)
            ->findAll();

        // Get today's completed diagnoses
        $todayCompleted = $this->orderModel->select('
            repair_orders.id,
            repair_orders.order_number,
            diagnosed_by_user.full_name as diagnosed_by_name
        ')
            ->join('users as diagnosed_by_user', 'diagnosed_by_user.id = repair_orders.diagnosed_by', 'left')
            ->where('repair_orders.diagnosis_status', 'completed')
            ->where('DATE(repair_orders.diagnosis_date)', date('Y-m-d'))
            ->orderBy('repair_orders.diagnosis_date', 'DESC')
            ->limit(10)
            ->findAll();

        // Get popular templates
        $popularTemplates = $this->templateModel->select('
            diagnosis_templates.*,
            device_types.name as device_type_name
        ')
            ->join('device_types', 'device_types.id = diagnosis_templates.device_type_id')
            ->orderBy('diagnosis_templates.id', 'DESC') // Could be usage_count if you track it
            ->limit(10)
            ->findAll();

        // Prepare data for view
        $data = [
            'title' => 'Diagnosis Queue',
            'orders' => $orders,
            'pager' => $pager,
            'stats' => $stats,
            'device_types' => $this->deviceTypeModel->findAll(),
            'technicians' => $this->userModel->getTechnicians(),
            'priority_orders' => $priorityOrders,
            'today_completed' => $todayCompleted,
            'popular_templates' => $popularTemplates,

            // Current filter values for form persistence
            'current_filters' => [
                'status' => $status,
                'device_type' => $deviceType,
                'technician' => $technician,
                'search' => $search
            ]
        ];

        return view('admin/diagnosis/index', $data);
    }

    /**
     * Get diagnosis statistics
     */
    private function getDiagnosisStatistics(): array
    {
        $pending = $this->orderModel->where('diagnosis_status', 'pending')->countAllResults();
        $inProgress = $this->orderModel->where('diagnosis_status', 'in_progress')->countAllResults();
        $completed = $this->orderModel->where('diagnosis_status', 'completed')->countAllResults();
        $today = $this->orderModel->where('DATE(diagnosis_date)', date('Y-m-d'))->countAllResults();

        return [
            'pending' => $pending,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'today' => $today,
            'total' => $pending + $inProgress + $completed
        ];
    }

    /**
     * Start diagnosis process
     */
    public function start($orderId): RedirectResponse
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if order can be diagnosed
        if (!in_array($order['status'], ['received', 'diagnosed'])) {
            return redirect()->to("/admin/orders/{$orderId}")
                ->with('error', 'This order cannot be diagnosed at current status: ' . $order['status']);
        }

        // Update diagnosis status to in_progress
        $updateData = [
            'diagnosis_status' => 'in_progress',
            'diagnosed_by' => session()->get('user_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->orderModel->update($orderId, $updateData)) {
            // Log the action in status history
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $orderId,
                $order['status'],
                $order['status'], // Keep same status, just starting diagnosis
                'Diagnosis started by technician',
                session()->get('user_id')
            );

            return redirect()->to("/admin/diagnosis/{$orderId}/create")
                ->with('success', 'Diagnosis started. You can now input your findings.');
        }

        return redirect()->back()->with('error', 'Failed to start diagnosis');
    }

    /**
     * Show diagnosis form
     */
    public function create($orderId): string
    {
        $order = $this->orderModel->getDiagnosisDetails($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if order can be diagnosed
        if (!in_array($order['diagnosis_status'], ['pending', 'in_progress'])) {
            return redirect()->to("/admin/diagnosis/{$orderId}")
                ->with('error', 'This order diagnosis is already completed or cannot be modified');
        }

        $data = [
            'title' => 'Diagnosis - Order #' . $order['order_number'],
            'order' => $order,
            'device_types' => $this->deviceTypeModel->findAll(),
            'technicians' => $this->userModel->getTechnicians(),
            'common_issues' => $this->getCommonIssues($order['device_type_id']),
            'templates' => $this->templateModel->getByDeviceType($order['device_type_id'])
        ];

        return view('admin/diagnosis/create', $data);
    }

    /**
     * Save diagnosis results
     */
    public function store($orderId): RedirectResponse
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'diagnosis_notes' => 'required|min_length[10]',
            'recommended_actions' => 'required|min_length[10]',
            'estimated_hours' => 'permit_empty|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare issues found array
        $issuesFound = [];
        $issueInputs = $this->request->getPost('issues') ?? [];

        foreach ($issueInputs as $issue) {
            if (!empty($issue['description'])) {
                $issuesFound[] = [
                    'description' => $issue['description'],
                    'severity' => $issue['severity'] ?? 'medium',
                    'repair_needed' => $issue['repair_needed'] ?? true
                ];
            }
        }

        $diagnosisData = [
            'diagnosis_notes' => $this->request->getPost('diagnosis_notes'),
            'issues_found' => $issuesFound,
            'recommended_actions' => $this->request->getPost('recommended_actions'),
            'estimated_hours' => $this->request->getPost('estimated_hours')
            // Removed estimated_cost as per recommendation
        ];

        if ($this->orderModel->updateDiagnosis($orderId, $diagnosisData)) {
            // Update order status to diagnosed
            $this->orderModel->update($orderId, [
                'status' => 'diagnosed',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            // Log status change
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $orderId,
                $order['status'],
                'diagnosed',
                'Diagnosis completed: ' . $this->request->getPost('diagnosis_notes'),
                session()->get('user_id')
            );

            // Check if should auto-create quotation
            if ($this->request->getPost('create_quotation')) {
                return redirect()->to("/admin/quotations/create/{$orderId}")
                    ->with('success', 'Diagnosis saved successfully. Creating quotation...');
            }

            return redirect()->to("/admin/diagnosis/{$orderId}")
                ->with('success', 'Diagnosis saved successfully');
        }

        return redirect()->back()->with('error', 'Failed to save diagnosis');
    }

    /**
     * Show diagnosis details
     */
    public function show($orderId): string
    {
        $order = $this->orderModel->getDiagnosisDetails($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $data = [
            'title' => 'Diagnosis Details - Order #' . $order['order_number'],
            'order' => $order
        ];

        return view('admin/diagnosis/show', $data);
    }

    /**
     * Edit existing diagnosis
     */
    public function edit($orderId): string
    {
        $order = $this->orderModel->getDiagnosisDetails($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        // Check if diagnosis can be edited
        if ($order['diagnosis_status'] !== 'completed' && $order['status'] !== 'waiting_approval') {
            return redirect()->to("/admin/diagnosis/{$orderId}")
                ->with('error', 'This diagnosis cannot be edited at current status');
        }

        $data = [
            'title' => 'Edit Diagnosis - Order #' . $order['order_number'],
            'order' => $order,
            'device_types' => $this->deviceTypeModel->findAll(),
            'technicians' => $this->userModel->getTechnicians(),
            'common_issues' => $this->getCommonIssues($order['device_type_id']),
            'templates' => $this->templateModel->getByDeviceType($order['device_type_id']),
            'is_edit' => true
        ];

        return view('admin/diagnosis/edit', $data);
    }

    /**
     * Update existing diagnosis
     */
    public function update($orderId): RedirectResponse
    {
        $order = $this->orderModel->find($orderId);

        if (!$order) {
            throw new PageNotFoundException('Order not found');
        }

        $rules = [
            'diagnosis_notes' => 'required|min_length[10]',
            'recommended_actions' => 'required|min_length[10]',
            'estimated_hours' => 'permit_empty|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Prepare issues found array
        $issuesFound = [];
        $issueInputs = $this->request->getPost('issues') ?? [];

        foreach ($issueInputs as $issue) {
            if (!empty($issue['description'])) {
                $issuesFound[] = [
                    'description' => $issue['description'],
                    'severity' => $issue['severity'] ?? 'medium',
                    'repair_needed' => $issue['repair_needed'] ?? true
                ];
            }
        }

        $diagnosisData = [
            'diagnosis_notes' => $this->request->getPost('diagnosis_notes'),
            'issues_found' => $issuesFound,
            'recommended_actions' => $this->request->getPost('recommended_actions'),
            'estimated_hours' => $this->request->getPost('estimated_hours')
        ];

        if ($this->orderModel->updateDiagnosis($orderId, $diagnosisData)) {
            // Log the update
            $historyModel = new OrderStatusHistoryModel();
            $historyModel->addStatusChange(
                $orderId,
                $order['status'],
                $order['status'], // Keep same status
                'Diagnosis updated: ' . $this->request->getPost('diagnosis_notes'),
                session()->get('user_id')
            );

            return redirect()->to("/admin/diagnosis/{$orderId}")
                ->with('success', 'Diagnosis updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update diagnosis');
    }

    /**
     * Get common issues by device type (AJAX)
     */
    public function getCommonIssues($deviceTypeId): array
    {
        // This could be expanded to use database templates
        $commonIssues = [
            1 => [ // Laptop
                'Screen not working',
                'Keyboard malfunction',
                'Battery not charging',
                'Overheating issues',
                'Performance degradation',
                'Hard drive failure',
                'RAM issues'
            ],
            2 => [ // Desktop
                'Not booting',
                'Blue screen errors',
                'Performance issues',
                'Hardware component failure',
                'Power supply issues',
                'Graphics card problems'
            ],
            3 => [ // Smartphone
                'Screen cracked',
                'Battery draining fast',
                'Not charging',
                'Camera not working',
                'Water damage',
                'Software issues'
            ]
        ];

        return $commonIssues[$deviceTypeId] ?? [
            'Device not powering on',
            'Performance issues',
            'Hardware malfunction',
            'Software problems',
            'Physical damage'
        ];
    }

    /**
     * Diagnosis templates management
     */
    public function templates(): string
    {
        $templates = $this->templateModel->getTemplatesWithDeviceTypes();

        $data = [
            'title' => 'Diagnosis Templates',
            'templates' => $templates,
            'device_types' => $this->deviceTypeModel->findAll()
        ];

        return view('admin/diagnosis/templates', $data);
    }

    /**
     * Get diagnosis queue for dashboard widget
     */
    public function getQueueWidget(): array
    {
        $pending = $this->orderModel->where('diagnosis_status', 'pending')->countAllResults();
        $inProgress = $this->orderModel->where('diagnosis_status', 'in_progress')->countAllResults();
        $completed = $this->orderModel->where('diagnosis_status', 'completed')
            ->where('status', 'diagnosed')
            ->countAllResults();

        return [
            'pending' => $pending,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'total' => $pending + $inProgress + $completed
        ];
    }
}