<?php
// app/Controllers/Admin/CustomerController.php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\RepairOrderModel;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class CustomerController extends BaseController
{
    protected CustomerModel $customerModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->customerModel = new CustomerModel();
        $this->userModel = new UserModel();
    }

    public function index(): string
    {
        $perPage = 20;
        $search = $this->request->getGet('search');

        $query = $this->customerModel->select('customers.*, users.username')
            ->join('users', 'users.id = customers.user_id', 'left');

        if ($search) {
            $query->groupStart()
                ->like('customers.full_name', $search)
                ->orLike('customers.email', $search)
                ->orLike('customers.phone', $search)
                ->groupEnd();
        }

        $customers = $query->orderBy('customers.created_at', 'DESC')
            ->paginate($perPage);

        $data = [
            'customers' => $customers,
            'pager' => $this->customerModel->pager,
            'search' => $search
        ];

        return view('admin/customers/index', $data);
    }

    public function show($id)
    {
        $customer = $this->customerModel->getWithUser($id);

        if (!$customer) {
            throw new PageNotFoundException('Customer not found');
        }

        // Get customer's repair history
        $orderModel = new RepairOrderModel();
        $orders = $orderModel->select('
                repair_orders.*,
                customers.full_name as customer_name,
                customers.phone as customer_phone,
                device_types.name as device_type_name,
                users.full_name as technician_name
            ')
            ->join('customers', 'customers.id = repair_orders.customer_id')
            ->join('device_types', 'device_types.id = repair_orders.device_type_id')
            ->join('users', 'users.id = repair_orders.technician_id', 'left')
            ->where('repair_orders.customer_id', $id)
            ->orderBy('repair_orders.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Customer Details',
            'customer' => $customer,
            'orders' => $orders
        ];

        return view('admin/customers/show', $data);
    }

    public function new(): string
    {
        return view('admin/customers/new');
    }

    public function store()
    {
        $rules = [
            'full_name' => 'required|min_length[2]|max_length[100]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->customerModel->insert($data)) {
            return redirect()->to('/admin/customers')->with('success', 'Customer created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create customer');
    }

    public function edit($id): string
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw new PageNotFoundException('Customer not found');
        }

        $data = ['customer' => $customer];

        return view('admin/customers/edit', $data);
    }

    public function update($id)
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw new PageNotFoundException('Customer not found');
        }

        $rules = [
            'full_name' => 'required|min_length[2]|max_length[100]',
            'phone' => 'required|min_length[10]|max_length[20]',
            'email' => 'permit_empty|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->customerModel->update($id, $data)) {
            return redirect()->to('/admin/customers')->with('success', 'Customer updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update customer');
    }

    public function delete($id): RedirectResponse
    {
        $customer = $this->customerModel->find($id);

        if (!$customer) {
            throw new PageNotFoundException('Customer not found');
        }

        if ($this->customerModel->delete($id)) {
            return redirect()->to('/admin/customers')->with('success', 'Customer deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete customer');
    }
}