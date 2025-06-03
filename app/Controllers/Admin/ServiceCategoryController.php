<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ServiceCategoryModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ServiceCategoryController extends BaseController
{
    protected ServiceCategoryModel $serviceCategoryModel;

    public function __construct()
    {
        $this->serviceCategoryModel = new ServiceCategoryModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');

        $builder = $this->serviceCategoryModel;

        if ($search) {
            $builder = $builder->like('name', $search)
                ->orLike('description', $search);
        }

        $categories = $builder->orderBy('name', 'ASC')->findAll();

        $data = [
            'title' => 'Service Categories',
            'categories' => $categories,
            'search' => $search
        ];

        return view('admin/service_categories/index', $data);
    }

    public function show($id)
    {
        $category = $this->serviceCategoryModel->find($id);

        if (!$category) {
            throw new PageNotFoundException('Category not found');
        }

        // Get services in this category
        $serviceModel = new \App\Models\ServiceModel();
        $services = $serviceModel->where('category_id', $id)->findAll();

        $data = [
            'title' => 'Category Details',
            'category' => $category,
            'services' => $services
        ];

        return view('admin/service_categories/show', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Category'
        ];

        return view('admin/service_categories/create', $data);
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'icon' => $this->request->getPost('icon'),
            'status' => $this->request->getPost('status', 'active'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->serviceCategoryModel->insert($data)) {
            return redirect()->to('/admin/service-categories')->with('success', 'Category created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create category');
    }

    public function edit($id)
    {
        $category = $this->serviceCategoryModel->find($id);

        if (!$category) {
            throw new PageNotFoundException('Category not found');
        }

        $data = [
            'title' => 'Edit Category',
            'category' => $category
        ];

        return view('admin/service_categories/edit', $data);
    }

    public function update($id)
    {
        $category = $this->serviceCategoryModel->find($id);

        if (!$category) {
            throw new PageNotFoundException('Category not found');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'icon' => $this->request->getPost('icon'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->serviceCategoryModel->update($id, $data)) {
            return redirect()->to('/admin/service-categories')->with('success', 'Category updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update category');
    }

    public function delete($id)
    {
        $category = $this->serviceCategoryModel->find($id);

        if (!$category) {
            throw new PageNotFoundException('Category not found');
        }

        // Check if category has services
        $serviceModel = new \App\Models\ServiceModel();
        $serviceCount = $serviceModel->where('category_id', $id)->countAllResults();

        if ($serviceCount > 0) {
            return redirect()->back()->with('error', 'Cannot delete category that has services. Please move or delete services first.');
        }

        if ($this->serviceCategoryModel->delete($id)) {
            return redirect()->to('/admin/service-categories')->with('success', 'Category deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete category');
    }
}