<?php
namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $table = 'admin_tbl';
    protected $primaryKey = 'admin_id';
    protected $allowedFields = [
        'username', 'email', 'password', 'role', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Validate admin credentials
     */
    public function validateAdmin($username, $password)
    {
        $admin = $this->where('username', $username)
                     ->orWhere('email', $username)
                     ->first();

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        
        return false;
    }
    
    /**
     * Get admin by ID
     */
    public function getAdminById($id)
    {
        return $this->find($id);
    }
    
    /**
     * Get all admins
     */
    public function getAllAdmins()
    {
        return $this->findAll();
    }
    
    /**
     * Get all jabatan (positions/departments)
     */
    public function getAllJabatan()
    {
        $db = \Config\Database::connect();
        
        // Check if jabatan table exists
        if (!$db->tableExists('jabatan_tbl')) {
            // If no jabatan table, get unique jabatan_ids from user_tbl
            $results = $db->table('user_tbl')
                         ->select('jabatan_id')
                         ->distinct()
                         ->orderBy('jabatan_id', 'ASC')
                         ->get()
                         ->getResultArray();
            
            // Format the results to match expected structure
            $jabatanList = [];
            foreach ($results as $row) {
                if (!empty($row['jabatan_id'])) {
                    $jabatanList[] = [
                        'jabatan_id' => $row['jabatan_id'],
                        'jabatan_name' => $row['jabatan_id']
                    ];
                }
            }
            
            return $jabatanList;
        }
        
        return $db->table('jabatan_tbl')
                 ->orderBy('jabatan_name', 'ASC')
                 ->get()
                 ->getResultArray();
    }
    
    /**
     * Get all users
     */
    public function getAllUsers()
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('user_tbl u');
        
        // Check if jabatan table exists
        if ($db->tableExists('jabatan_tbl')) {
            $builder->select('u.*, j.jabatan_name');
            $builder->join('jabatan_tbl j', 'u.jabatan_id = j.jabatan_id', 'left');
        } else {
            $builder->select('u.*, u.jabatan_id as jabatan_name');
        }
        
        $builder->orderBy('u.full_name', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Check if admin exists
     */
    public function adminExists($username, $email)
    {
        return $this->where('username', $username)
                   ->orWhere('email', $email)
                   ->first();
    }
    
    /**
     * Get admin statistics
     */
    public function getAdminStats()
    {
        $db = \Config\Database::connect();
        
        // More robust pending bookings count
        $pendingCount = $db->table('booking_tbl')
                          ->groupStart()
                            ->where('extra_info', 'pending')
                            ->orWhere('extra_info', '')
                            ->orWhere('extra_info IS NULL')
                          ->groupEnd()
                          ->countAllResults();
        
        $stats = [
            'total_bookings' => $db->table('booking_tbl')->countAll(),
            'total_users' => $db->table('user_tbl')->countAll(),
            'today_bookings' => $db->table('booking_tbl')
                                 ->where('booking_date', date('Y-m-d'))
                                 ->countAllResults(),
            'pending_bookings' => $pendingCount,
            'approved_bookings' => $db->table('booking_tbl')
                                    ->where('extra_info', 'Approved')
                                    ->countAllResults(),
            'cancelled_bookings' => $db->table('booking_tbl')
                                     ->where('extra_info', 'Cancelled')
                                     ->countAllResults()
        ];
        
        return $stats;
    }
    
    /**
     * Create default jabatan table and data with specific departments
     */
    public function createJabatanTable()
    {
        $db = \Config\Database::connect();
        
        // Create the table
        $sql = "
        CREATE TABLE IF NOT EXISTS jabatan_tbl (
            jabatan_id INT AUTO_INCREMENT PRIMARY KEY,
            jabatan_name VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->query($sql);
        
        // Insert specific data
        $defaultData = [
            ['jabatan_name' => 'Jabatan Kejuruteraan Elektronik'],
            ['jabatan_name' => 'Jabatan Teknologi Maklumat'],
            ['jabatan_name' => 'Jabatan Pendidikan Am']
        ];
        
        // Clear any existing data and insert new data
        $db->table('jabatan_tbl')->truncate();
        foreach ($defaultData as $data) {
            $db->table('jabatan_tbl')->insert($data);
        }
        
        return true;
    }
    
    /**
     * Get department statistics for the bar chart
     */
    public function getDepartmentStats()
    {
        $db = \Config\Database::connect();
        
        // Get all departments from jabatan_tbl
        $jabatanList = $db->table('jabatan_tbl')
                         ->orderBy('jabatan_name', 'ASC')
                         ->get()
                         ->getResultArray();
        
        $departmentStats = [];
        
        foreach ($jabatanList as $jabatan) {
            // Count users in each department
            $userCount = $db->table('user_tbl')
                           ->where('jabatan_id', $jabatan['jabatan_name'])
                           ->where('Category', 'user')
                           ->countAllResults();
            
            $departmentStats[] = [
                'jabatan_id' => $jabatan['jabatan_id'],
                'jabatan_name' => $jabatan['jabatan_name'],
                'user_count' => $userCount
            ];
        }
        
        return $departmentStats;
    }
}