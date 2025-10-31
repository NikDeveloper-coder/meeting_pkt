<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user_tbl';
    protected $primaryKey = 'user_Id';
    protected $allowedFields = ['full_name', 'Email', 'password', 'jabatan_id', 'Category', 'user_Category'];
    protected $useTimestamps = false;
    
    public function getUserByCredentials($username, $password)
    {
        $user = $this->where('Email', $username)->first();
        
        // Compare plain text passwords directly (no hashing)
        if ($user && $user['password'] === $password) {
            return $user;
        }
        
        return false;
    }
    
    public function checkEmailExists($email)
    {
        return $this->where('Email', $email)->first();
    }
    
    /**
     * Get user by username or email (for admin operations)
     */
    public function getUserByUsername($username)
    {
        return $this->where('user_Category', $username)
                   ->orWhere('Email', $username)
                   ->first();
    }
    
    /**
     * Validate user credentials (for admin operations)
     */
    public function validateUser($username, $password)
    {
        $user = $this->getUserByUsername($username);
        
        if ($user && $user['password'] === $password) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Check if user exists (for admin operations)
     */
    public function userExists($username, $email)
    {
        return $this->where('user_Category', $username)
                   ->orWhere('Email', $email)
                   ->first();
    }
    
    /**
     * Get all users with optional filtering
     */
    public function getAllUsers($conditions = [])
    {
        if (!empty($conditions)) {
            return $this->where($conditions)->findAll();
        }
        return $this->findAll();
    }
    
    /**
     * Create new user (for admin operations)
     */
    public function createUser($data)
    {
        return $this->insert($data);
    }
    
    /**
     * Update user (for admin operations)
     */
    public function updateUser($id, $data)
    {
        return $this->update($id, $data);
    }
    
    /**
     * Delete user (for admin operations)
     */
    public function deleteUser($id)
    {
        return $this->delete($id);
    }
}