<?php


namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class UserController extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $role = $this->request->getGet('role');

        $builder = $this->userModel;

        if ($search) {
            $builder = $builder->groupStart()
                ->like('username', $search)
                ->orLike('full_name', $search)
                ->orLike('email', $search)
                ->groupEnd();
        }

        if ($role) {
            $builder = $builder->where('role', $role);
        }

        $users = $builder->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'title' => 'Users',
            'users' => $users,
            'search' => $search,
            'selected_role' => $role
        ];

        return view('admin/users/index', $data);
    }

    public function show($id)
    {
        $user = $this->userModel->getUserWithProfile($id);

        if (!$user) {
            throw new PageNotFoundException('User not found');
        }

        $data = [
            'title' => 'User Details',
            'user' => $user
        ];

        return view('admin/users/show', $data);
    }

    public function new()
    {
        $data = [
            'title' => 'Add New User'
        ];

        return view('admin/users/create', $data);
    }

    public function store()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'full_name' => 'required|min_length[2]|max_length[100]',
            'role' => 'required|in_list[admin,technician,customer]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'), // Will be hashed by model
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status', 'active'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'User created successfully');
        }

        return redirect()->back()->with('error', 'Failed to create user');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new PageNotFoundException('User not found');
        }

        $data = [
            'title' => 'Edit User',
            'user' => $user
        ];

        return view('admin/users/edit', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new PageNotFoundException('User not found');
        }

        $rules = [
            'username' => "required|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'full_name' => 'required|min_length[2]|max_length[100]',
            'role' => 'required|in_list[admin,technician,customer]'
        ];

        // Only validate password if it's provided
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'full_name' => $this->request->getPost('full_name'),
            'phone' => $this->request->getPost('phone'),
            'role' => $this->request->getPost('role'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password'); // Will be hashed by model
        }

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'User updated successfully');
        }

        return redirect()->back()->with('error', 'Failed to update user');
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new PageNotFoundException('User not found');
        }

        // Prevent deleting current user
        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'Cannot delete your own account');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'User deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete user');
    }
}