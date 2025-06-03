<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ServiceModel;
use App\Models\ServiceCategoryModel;

class ServiceController extends BaseController
{
    protected ServiceModel $serviceModel;
    protected ServiceCategoryModel $serviceCategoryModel;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
        $this->serviceCategoryModel = new ServiceCategoryModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $categoryId = $this->request->getGet('category');

        $builder = $this->serviceModel->select('services.*, service_categories.name as category_name')
            ->join('service_categories', 'service_categories.id = services.category_id');

        if ($search) {
            $builder->groupStart()
                ->like('services.name', $search)
                ->orLike('services.description', $search)
                ->groupEnd();
        }

        if ($categoryId) {
            $builder->where('services.category_id', $categoryId);
        }

        $services = $builder->orderBy('services.name', 'ASC')->findAll();

        $data = [
            'title' => 'Services',
            'services' => $services,
            'categories' => $this->serviceCategoryModel->getActiveCategories(),
            'search' => $search,
            'selected_category' => $categoryId
        ];

        return view('admin/services/index', $data);
    }

    public function show($id)
    {
        $service = $this->serviceModel->select('services.*, service_categories.name as category_name')
            ->join('service_categories', 'service_categories.id = services.category_id')
            ->where('services.id', $id)
            ->first();

        if (!$service) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Service not found');
        }

        $data = [
            'title' => 'Service Details',
            'service' => $service
        ];

        return view('admin/services/show', $data);
    }

    public function new()
    {
        $data = [
            'title' => 'Add New Service',
            'categories' => $this->serviceCategoryModel->getActiveCategories()
        ];

        return view('admin/services/create', $data);
    }

    public function store()
    {
        $rules = [
            'category_id' => 'required|integer',
            'name' => 'required|min_length[2]|max_length[100]',
            'base_price' => 'required|decimal',
            'estimated_duration' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'base_price' => $this->request->getPost('base_price'),
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'status' => $this->request->getPost('status', 'active'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->serviceModel->insert($data)) {
            return redirect()->to('/admin/services')->with('success', 'Service created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create service');
    }

    public function edit($id)
    {
        $service = $this->serviceModel->find($id);

        if (!$service) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Service not found');
        }

        $data = [
            'title' => 'Edit Service',
            'service' => $service,
            'categories' => $this->serviceCategoryModel->getActiveCategories()
        ];

        return view('admin/services/edit', $data);
    }

    public function update($id)
    {
        $service = $this->serviceModel->find($id);

        if (!$service) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Service not found');
        }

        $rules = [
            'category_id' => 'required|integer',
            'name' => 'required|min_length[2]|max_length[100]',
            'base_price' => 'required|decimal',
            'estimated_duration' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'base_price' => $this->request->getPost('base_price'),
            'estimated_duration' => $this->request->getPost('estimated_duration'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->serviceModel->update($id, $data)) {
            return redirect()->to('/admin/services')->with('success', 'Service updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update service');
    }

    public function delete($id)
    {
        $service = $this->serviceModel->find($id);

        if (!$service) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Service not found');
        }

        if ($this->serviceModel->delete($id)) {
            return redirect()->to('/admin/services')->with('success', 'Service deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete service');
    }
}