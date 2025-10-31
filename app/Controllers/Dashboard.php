<?php
namespace App\Controllers;

use App\Models\UserModel;
use App\Models\BookingModel;

class Dashboard extends BaseController
{
    protected $userModel;
    protected $bookingModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->bookingModel = new BookingModel();
        
        // FIX: Check multiple possible session keys
        if (!session()->get('user_Id') && !session()->get('user_id')) {
            return redirect()->to('/login');
        }
    }
    
    public function index()
    {
        return $this->dashboard();
    }
    
    public function dashboard()
    {
        $date_filter = $this->request->getGet('date');
        
        // FIX: Get user_id from multiple possible session keys
        $user_id = session()->get('user_Id') ?? session()->get('user_id');
        
        // Debug: Check if we're getting data
        // var_dump($user_id); // Uncomment to debug
        
        $data = [
            'bookings' => $this->bookingModel->getUserBookings($user_id, $date_filter),
            'filter_date' => $date_filter,
            'section' => 'dashboard'
        ];
        
        return view('dashboard/index', $data);
    }
    
    public function mailbox()
    {
        // FIX: Get user_id from multiple possible session keys
        $user_id = session()->get('user_Id') ?? session()->get('user_id');
        
        $bookings = $this->bookingModel->getUserBookings($user_id);
        
        $data = [
            'bookings' => $bookings,
            'section' => 'mailbox'
        ];
        
        return view('dashboard/mailbox', $data);
    }
    
    public function profile()
    {
        // FIX: Get user_id from multiple possible session keys
        $user_id = session()->get('user_Id') ?? session()->get('user_id');
        
        $user = $this->userModel->find($user_id);
        
        $data = [
            'user_data' => $user,
            'section' => 'profile'
        ];
        
        return view('dashboard/profile', $data);
    }
    
    public function updateProfile()
    {
        // FIX: Get user_id from multiple possible session keys
        $user_id = session()->get('user_Id') ?? session()->get('user_id');
        
        $new_username = $this->request->getPost('username');
        $new_password = $this->request->getPost('password');
        
        if ($new_username) {
            $updateData = [
                'full_name' => $new_username
            ];
            
            if (!empty($new_password)) {
                $updateData['password'] = password_hash($new_password, PASSWORD_DEFAULT);
            }
            
            if ($this->userModel->update($user_id, $updateData)) {
                session()->set('fullname', $new_username);
                return redirect()->to('/dashboard/profile')->with('profile_msg', 'Profile updated successfully.');
            } else {
                return redirect()->to('/dashboard/profile')->with('error', 'Failed to update profile.');
            }
        }
        
        return redirect()->to('/dashboard/profile')->with('error', 'Please fill username field.');
    }
}