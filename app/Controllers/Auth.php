<?php
namespace App\Controllers;

use App\Models\UserModel;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Apple;

class Auth extends BaseController
{
    protected $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
    }
    
    // =========================
    // ===== NORMAL LOGIN =====
    // =========================
    public function login()
    {
        if (session()->get('user_Id')) {
            return $this->redirectToDashboard();
        }
        return view('auth/login');
    }
    
    public function attemptLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        $user = $this->userModel->getUserByCredentials($username, $password);
        
        if ($user) {
            $sessionData = [
                'user_Id' => $user['user_Id'],
                'fullname' => $user['full_name'],
                'email' => $user['Email'],
                'role' => $user['Category']
            ];
            session()->set($sessionData);
            
            if ($user['Category'] === 'admin') {
                return redirect()->to('/admin');
            } else {
                return redirect()->to('/dashboard');
            }
        } else {
            return redirect()->back()->with('error', '❌ Username or Password incorrect!');
        }
    }

    // =========================
    // ===== ADMIN LOGIN ======
    // =========================
    public function adminLogin()
    {
        if (session()->get('role') === 'admin') {
            return redirect()->to('/admin');
        }
        return view('auth/admin_login');
    }

    public function attemptAdminLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        $user = $this->userModel->getUserByCredentials($username, $password);
        
        if ($user && $user['Category'] === 'admin') {
            $sessionData = [
                'user_Id' => $user['user_Id'],
                'fullname' => $user['full_name'],
                'email' => $user['Email'],
                'role' => $user['Category']
            ];
            session()->set($sessionData);
            return redirect()->to('/admin');
        } else {
            return redirect()->back()->with('error', '❌ Admin access denied! Invalid credentials.');
        }
    }

    // =========================
    // ===== REGISTER =====
    // =========================
    public function register()
    {
        return view('auth/register');
    }
    
    public function attemptRegister()
    {
        $data = [
            'full_name' => $this->request->getPost('fullname'),
            'Email' => $this->request->getPost('email'),
            'jabatan_id' => $this->request->getPost('jabatan'),
            'password' => $this->request->getPost('password'),
            'user_Category' => $this->request->getPost('email'),
            'Category' => 'user'
        ];
        
        if ($this->userModel->checkEmailExists($data['Email'])) {
            return redirect()->back()->with('error', '⚠️ Email already exists!');
        }
        
        if ($this->userModel->insert($data)) {
            return redirect()->to('/login')->with('success', '✅ Registration Successful! Please login.');
        } else {
            return redirect()->back()->with('error', '❌ Registration Failed. Try again.');
        }
    }

    // =========================
    // ===== LOGOUT =====
    // =========================
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    private function redirectToDashboard()
    {
        if (session()->get('role') === 'admin') {
            return redirect()->to('/admin');
        } else {
            return redirect()->to('/dashboard');
        }
    }

    // =========================
    // ===== PROFILE UPDATE ====
    // =========================
    public function updateProfile()
    {
        if (!session()->get('user_Id')) {
            return redirect()->to('/login');
        }

        $user_id = session()->get('user_Id');
        $new_username = $this->request->getPost('username');
        $new_password = $this->request->getPost('password');
        
        if ($new_username) {
            $updateData = ['full_name' => $new_username];
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

    // =========================
    // ===== CHANGE PASSWORD ===
    // =========================
    public function changePassword()
    {
        if (!session()->get('user_Id')) {
            return redirect()->to('/login');
        }

        $user_id = session()->get('user_Id');
        $current_password = $this->request->getPost('current_password');
        $new_password = $this->request->getPost('new_password');
        $confirm_password = $this->request->getPost('confirm_password');
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            return redirect()->back()->with('error', 'All password fields are required.');
        }
        
        if ($new_password !== $confirm_password) {
            return redirect()->back()->with('error', 'New password and confirmation do not match.');
        }
        
        $user = $this->userModel->find($user_id);
        if (!$user || !password_verify($current_password, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }
        
        $updateData = [
            'password' => password_hash($new_password, PASSWORD_DEFAULT)
        ];
        
        if ($this->userModel->update($user_id, $updateData)) {
            return redirect()->to('/dashboard/profile')->with('profile_msg', 'Password changed successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to change password.');
        }
    }

    // =========================
    // ===== FORGOT PASSWORD ===
    // =========================
    public function forgotPassword()
    {
        return view('auth/forgot_password');
    }
    
    public function processForgotPassword()
    {
        $email = $this->request->getPost('email');
        $user = $this->userModel->where('Email', $email)->first();
        
        if ($user) {
            $reset_token = bin2hex(random_bytes(32));
            return redirect()->to('/login')->with('success', 'Password reset instructions have been sent to your email.');
        } else {
            return redirect()->back()->with('error', 'Email not found in our system.');
        }
    }

    // =========================
    // ===== SOCIAL LOGIN =====
    // =========================
    public function google()
    {
        $provider = new Google([
            'clientId'     => 'YOUR_GOOGLE_CLIENT_ID',
            'clientSecret' => 'YOUR_GOOGLE_CLIENT_SECRET',
            'redirectUri'  => site_url('auth/googleCallback'),
        ]);

        if (!isset($_GET['code'])) {
            return redirect()->to($provider->getAuthorizationUrl());
        }

        $token = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
        $googleUser = $provider->getResourceOwner($token);

        $userData = [
            'full_name' => $googleUser->getName(),
            'Email'     => $googleUser->getEmail(),
            'oauth_id'  => $googleUser->getId(),
            'Category'  => 'user'
        ];

        $this->loginOrRegisterSocialUser($userData);
        return redirect()->to('/dashboard');
    }

    public function facebook()
    {
        $provider = new Facebook([
            'clientId'        => 'YOUR_FB_APP_ID',
            'clientSecret'    => 'YOUR_FB_APP_SECRET',
            'redirectUri'     => site_url('auth/facebookCallback'),
            'graphApiVersion' => 'v10.0',
        ]);

        if (!isset($_GET['code'])) {
            return redirect()->to($provider->getAuthorizationUrl());
        }

        $token = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
        $fbUser = $provider->getResourceOwner($token);

        $userData = [
            'full_name' => $fbUser->getName(),
            'Email'     => $fbUser->getEmail(),
            'oauth_id'  => $fbUser->getId(),
            'Category'  => 'user'
        ];

        $this->loginOrRegisterSocialUser($userData);
        return redirect()->to('/dashboard');
    }

    public function apple()
    {
        $provider = new Apple([
            'clientId'     => 'YOUR_APPLE_CLIENT_ID',
            'teamId'       => 'YOUR_APPLE_TEAM_ID',
            'keyFileId'    => 'YOUR_APPLE_KEY_ID',
            'privateKey'   => file_get_contents(APPPATH . 'Keys/AuthKey_YOUR_KEY_ID.p8'),
            'redirectUri'  => site_url('auth/appleCallback'),
        ]);

        if (!isset($_GET['code'])) {
            return redirect()->to($provider->getAuthorizationUrl());
        }

        $token = $provider->getAccessToken('authorization_code', ['code' => $_GET['code']]);
        $appleUser = $provider->getResourceOwner($token);

        $userData = [
            'full_name' => $appleUser->getName(),
            'Email'     => $appleUser->getEmail(),
            'oauth_id'  => $appleUser->getId(),
            'Category'  => 'user'
        ];

        $this->loginOrRegisterSocialUser($userData);
        return redirect()->to('/dashboard');
    }

    // Helper for Google / Facebook / Apple
    private function loginOrRegisterSocialUser($userData)
    {
        $existing = $this->userModel->where('Email', $userData['Email'])->first();
        if (!$existing) {
            $this->userModel->insert($userData);
            $existing = $this->userModel->where('Email', $userData['Email'])->first();
        }

        session()->set([
            'user_Id' => $existing['user_Id'],
            'fullname' => $existing['full_name'],
            'email' => $existing['Email'],
            'role' => $existing['Category']
        ]);
    }
}
