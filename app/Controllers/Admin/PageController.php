<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CmsPageModel;

class PageController extends BaseController
{
    protected CmsPageModel $pageModel;

    public function __construct()
    {
        $this->pageModel = new CmsPageModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $builder = $this->pageModel->select('cms_pages.*, users.full_name as created_by_name')
            ->join('users', 'users.id = cms_pages.created_by');

        if ($search) {
            $builder->groupStart()
                ->like('cms_pages.title', $search)
                ->orLike('cms_pages.slug', $search)
                ->orLike('cms_pages.content', $search)
                ->groupEnd();
        }

        if ($status) {
            $builder->where('cms_pages.status', $status);
        }

        $pages = $builder->orderBy('cms_pages.updated_at', 'DESC')->findAll();

        $data = [
            'title' => 'CMS Pages',
            'pages' => $pages,
            'search' => $search,
            'selected_status' => $status
        ];

        return view('admin/pages/index', $data);
    }

    public function show($id)
    {
        $page = $this->pageModel->select('cms_pages.*, users.full_name as created_by_name')
            ->join('users', 'users.id = cms_pages.created_by')
            ->where('cms_pages.id', $id)
            ->first();

        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        $data = [
            'title' => 'Page Details',
            'page' => $page
        ];

        return view('admin/pages/show', $data);
    }

    public function new()
    {
        $data = [
            'title' => 'Create New Page'
        ];

        return view('admin/pages/new', $data);
    }

    public function store()
    {
        $rules = [
            'title' => 'required|min_length[2]|max_length[200]',
            'slug' => 'permit_empty|min_length[2]|max_length[200]|is_unique[cms_pages.slug]',
            'content' => 'permit_empty',
            'status' => 'required|in_list[draft,published]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = $this->request->getPost('slug');
        if (empty($slug)) {
            $slug = url_title($this->request->getPost('title'), '-', true);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => $slug,
            'content' => $this->request->getPost('content'),
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'status' => $this->request->getPost('status'),
            'featured_image' => $this->request->getPost('featured_image'),
            'template' => $this->request->getPost('template', 'default'),
            'created_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->pageModel->insert($data)) {
            return redirect()->to('/admin/pages')->with('success', 'Page created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create page');
    }

    public function edit($id)
    {
        $page = $this->pageModel->find($id);

        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        $data = [
            'title' => 'Edit Page',
            'page' => $page
        ];

        return view('admin/pages/edit', $data);
    }

    public function update($id)
    {
        $page = $this->pageModel->find($id);

        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        $rules = [
            'title' => 'required|min_length[2]|max_length[200]',
            'slug' => "permit_empty|min_length[2]|max_length[200]|is_unique[cms_pages.slug,id,{$id}]",
            'content' => 'permit_empty',
            'status' => 'required|in_list[draft,published]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $slug = $this->request->getPost('slug');
        if (empty($slug)) {
            $slug = url_title($this->request->getPost('title'), '-', true);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'slug' => $slug,
            'content' => $this->request->getPost('content'),
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'status' => $this->request->getPost('status'),
            'featured_image' => $this->request->getPost('featured_image'),
            'template' => $this->request->getPost('template', 'default'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->pageModel->update($id, $data)) {
            return redirect()->to('/admin/pages')->with('success', 'Page updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update page');
    }

    public function delete($id)
    {
        $page = $this->pageModel->find($id);

        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        if ($this->pageModel->delete($id)) {
            return redirect()->to('/admin/pages')->with('success', 'Page deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete page');
    }

    public function duplicate($id)
    {
        $page = $this->pageModel->find($id);

        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        // Create duplicate with modified title and slug
        $duplicateData = $page;
        unset($duplicateData['id']);
        $duplicateData['title'] = $page['title'] . ' (Copy)';
        $duplicateData['slug'] = $page['slug'] . '-copy-' . time();
        $duplicateData['status'] = 'draft';
        $duplicateData['created_by'] = session()->get('user_id');
        $duplicateData['created_at'] = date('Y-m-d H:i:s');
        $duplicateData['updated_at'] = date('Y-m-d H:i:s');

        if ($this->pageModel->insert($duplicateData)) {
            return redirect()->to('/admin/pages')->with('success', 'Page duplicated successfully');
        }

        return redirect()->back()->with('error', 'Failed to duplicate page');
    }
}