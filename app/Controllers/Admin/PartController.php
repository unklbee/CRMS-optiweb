<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PartModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class PartController extends BaseController
{
    protected $partModel;

    public function __construct()
    {
        $this->partModel = new PartModel();
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

        $data = [
            'title' => 'Part Details',
            'part' => $part
        ];

        return view('admin/parts/show', $data);
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

        if ($this->partModel->insert($data)) {
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
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->partModel->update($id, $data)) {
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

        $data = [
            'title' => 'Adjust Stock',
            'part' => $part
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

        $currentStock = $part['stock_quantity'];
        $newStock = $currentStock;

        switch ($adjustmentType) {
            case 'add':
                $newStock = $currentStock + $quantity;
                break;
            case 'subtract':
                $newStock = max(0, $currentStock - $quantity);
                break;
            case 'set':
                $newStock = $quantity;
                break;
        }

        $updateData = [
            'stock_quantity' => $newStock,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->partModel->update($id, $updateData)) {
            $message = "Stock adjusted from {$currentStock} to {$newStock}";
            if ($notes) {
                $message .= ". Notes: {$notes}";
            }

            return redirect()->to('/admin/parts')->with('success', $message);
        }

        return redirect()->back()->with('error', 'Failed to adjust stock');
    }
}
