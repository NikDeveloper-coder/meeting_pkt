<?php
namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\BookingModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    protected $adminModel;
    protected $bookingModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->bookingModel = new BookingModel();
        $this->userModel = new UserModel();
        
        if (!session()->get('role') || session()->get('role') !== "admin") {
            return redirect()->to('/login');
        }
    }
    
    public function index()
    {
        return $this->dashboard();
    }
    
    public function dashboard()
    {
        $where = [];
        
        if (!empty($this->request->getGet('date'))) {
            $where['b.booking_date'] = $this->request->getGet('date');
        }
        
        if (!empty($this->request->getGet('jabatan'))) {
            $where['u.jabatan_id'] = $this->request->getGet('jabatan');
        }
        
        $data = [
            'bookings' => $this->bookingModel->getBookingsWithUsers($where),
            'jabatan_list' => $this->adminModel->getAllJabatan(),
            'stats' => $this->adminModel->getAdminStats(),
            'department_stats' => $this->adminModel->getDepartmentStats(),
            'section' => 'dashboard',
            'filter_date' => $this->request->getGet('date'),
            'filter_jabatan' => $this->request->getGet('jabatan')
        ];
        
        return view('admin/dashboard', $data);
    }
    
    public function users()
    {
        $data = [
            'users' => $this->adminModel->getAllUsers(),
            'section' => 'users'
        ];
        
        return view('admin/users', $data);
    }
    
    public function action($action, $id)
    {
        $status = ($action === "approve") ? "Approved" : "Cancelled";
        
        $this->bookingModel->update($id, ['extra_info' => $status]);
        
        return redirect()->back()->with('success', "Booking {$status} successfully.");
    }
    
    public function mailbox()
    {
        $data = [
            'bookings' => $this->bookingModel->getBookingsWithUsers(),
            'section' => 'mailbox'
        ];
        
        return view('admin/mailbox', $data);
    }
    
    public function usersCreate()
    {
        if (!session()->get('role') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $data = [
            'jabatan_list' => $this->adminModel->getAllJabatan(),
            'section' => 'users'
        ];
        
        return view('admin/users_form', $data);
    }
    
    public function usersStore()
    {
        if (!session()->get('role') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $rules = [
            'full_name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[user_tbl.Email]',
            'username' => 'required|min_length[3]|is_unique[user_tbl.user_Category]',
            'password' => 'required|min_length[6]',
            'jabatan_id' => 'required',
            'category' => 'required|in_list[user,admin]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'Email' => $this->request->getPost('email'),
            'user_Category' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'jabatan_id' => $this->request->getPost('jabatan_id'),
            'Category' => $this->request->getPost('category')
        ];
        
        if ($this->userModel->insert($data)) {
            return redirect()->to(site_url('admin/users'))->with('success', 'User created successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create user');
        }
    }
    
    public function usersEdit($id = null)
    {
        if (!session()->get('role') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User not found');
        }
        
        $data = [
            'user' => $user,
            'jabatan_list' => $this->adminModel->getAllJabatan(),
            'section' => 'users'
        ];
        
        return view('admin/users_edit', $data);
    }
    
    public function usersUpdate($id)
    {
        if (!session()->get('role') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User not found');
        }
        
        $rules = [
            'full_name' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[user_tbl.Email,user_Id,{$id}]",
            'username' => "required|min_length[3]|is_unique[user_tbl.user_Category,user_Id,{$id}]",
            'jabatan_id' => 'required',
            'category' => 'required|in_list[user,admin]'
        ];
        
        // Only validate password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'Email' => $this->request->getPost('email'),
            'user_Category' => $this->request->getPost('username'),
            'jabatan_id' => $this->request->getPost('jabatan_id'),
            'Category' => $this->request->getPost('category')
        ];
        
        // Update password only if provided
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        if ($this->userModel->update($id, $data)) {
            return redirect()->to(site_url('admin/users'))->with('success', 'User updated successfully');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update user');
        }
    }
    
    public function usersDelete($id)
    {
        if (!session()->get('role') || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }
        
        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to(site_url('admin/users'))->with('error', 'User not found');
        }
        
        // Check if user has any bookings
        $userBookings = $this->bookingModel->where('user_id', $id)->countAllResults();
        if ($userBookings > 0) {
            return redirect()->to(site_url('admin/users'))->with('error', 'Cannot delete user with existing bookings');
        }
        
        if ($this->userModel->delete($id)) {
            return redirect()->to(site_url('admin/users'))->with('success', 'User deleted successfully');
        } else {
            return redirect()->to(site_url('admin/users'))->with('error', 'Failed to delete user');
        }
    }
}