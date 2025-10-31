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
     *
     * This function returns users and attaches a resolved jabatan_name using flexible matching.
     */
    public function getAllUsers()
    {
        $db = \Config\Database::connect();

        // Get users
        $users = $db->table('user_tbl u')
                    ->orderBy('u.full_name', 'ASC')
                    ->get()
                    ->getResultArray();

        // If jabatan_tbl exists, load departments to help resolve names
        $jabatanList = [];
        if ($db->tableExists('jabatan_tbl')) {
            $jabatanList = $db->table('jabatan_tbl')
                              ->orderBy('jabatan_name', 'ASC')
                              ->get()
                              ->getResultArray();
        }

        // Build resolved jabatan_name for each user
        $resolvedUsers = [];
        foreach ($users as $user) {
            $resolvedName = $user['jabatan_id']; // fallback to original text

            if (!empty($jabatanList)) {
                // Try fast special-case: exact or substring match
                $found = false;
                foreach ($jabatanList as $jabatan) {
                    if (strcasecmp(trim($user['jabatan_id']), trim($jabatan['jabatan_name'])) === 0) {
                        $resolvedName = $jabatan['jabatan_name'];
                        $found = true;
                        break;
                    }
                    if (stripos($user['jabatan_id'], $jabatan['jabatan_name']) !== false) {
                        $resolvedName = $jabatan['jabatan_name'];
                        $found = true;
                        break;
                    }
                }

                // If not found, try similarity check (similar_text)
                if (!$found) {
                    $bestPercent = 0;
                    $bestMatch = null;
                    foreach ($jabatanList as $jabatan) {
                        similar_text(
                            mb_strtolower($user['jabatan_id']),
                            mb_strtolower($jabatan['jabatan_name']),
                            $percent
                        );
                        if ($percent > $bestPercent) {
                            $bestPercent = $percent;
                            $bestMatch = $jabatan['jabatan_name'];
                        }
                    }

                    // Threshold (adjustable) - 60% by default
                    if ($bestPercent >= 60 && $bestMatch !== null) {
                        $resolvedName = $bestMatch;
                    }
                }
            }

            // Attach resolved name
            $user['jabatan_name'] = $resolvedName;
            $resolvedUsers[] = $user;
        }

        return $resolvedUsers;
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
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
     *
     * This function attempts:
     * 1) exact/LIKE matching,
     * 2) similarity matching (similar_text) as fallback,
     * 3) after assigning matched users to jabatan_tbl rows, it groups any remaining unmatched users into their own buckets.
     */
    public function getDepartmentStats()
    {
        $db = \Config\Database::connect();
        $departmentStats = [];

        // Load users (only real 'user' Category)
        $users = $db->table('user_tbl')
                    ->select('user_Id, jabatan_id')
                    ->where('Category', 'user')
                    ->get()
                    ->getResultArray();

        // Load jabatan_tbl if exists
        $jabatanList = [];
        if ($db->tableExists('jabatan_tbl')) {
            $jabatanList = $db->table('jabatan_tbl')
                              ->orderBy('jabatan_name', 'ASC')
                              ->get()
                              ->getResultArray();
        }

        // If we have jabatan_tbl, try to assign user rows to those departments
        if (!empty($jabatanList)) {
            // track which user indices are assigned
            $assigned = [];

            // For each jabatan, attempt to match users
            foreach ($jabatanList as $jabatan) {
                $count = 0;
                foreach ($users as $idx => $user) {
                    if (isset($assigned[$idx])) {
                        continue; // already assigned
                    }

                    $userJabatanText = $user['jabatan_id'];
                    $deptName = $jabatan['jabatan_name'];

                    // 1) Fast check: exact or substring (case-insensitive)
                    if (strcasecmp(trim($userJabatanText), trim($deptName)) === 0
                        || stripos($userJabatanText, $deptName) !== false
                        || stripos($deptName, $userJabatanText) !== false) {
                        $count++;
                        $assigned[$idx] = true;
                        continue;
                    }

                    // 2) Similarity check
                    similar_text(mb_strtolower($userJabatanText), mb_strtolower($deptName), $percent);
                    if ($percent >= 60) { // threshold, tweak if needed
                        $count++;
                        $assigned[$idx] = true;
                        continue;
                    }
                }

                $departmentStats[] = [
                    'jabatan_id'   => $jabatan['jabatan_id'],
                    'jabatan_name' => $jabatan['jabatan_name'],
                    'user_count'   => $count
                ];
            }

            // Handle unmatched users — group by their original jabatan_id text
            $unmatched = [];
            foreach ($users as $idx => $user) {
                if (isset($assigned[$idx])) continue;
                $key = $user['jabatan_id'];
                if (!isset($unmatched[$key])) $unmatched[$key] = 0;
                $unmatched[$key]++;
            }

            foreach ($unmatched as $name => $count) {
                $departmentStats[] = [
                    'jabatan_id'   => $name,
                    'jabatan_name' => $name,
                    'user_count'   => $count
                ];
            }
        } else {
            // No jabatan_tbl: group users by their jabatan_id string
            $grouped = [];
            foreach ($users as $user) {
                $name = $user['jabatan_id'];
                if (!isset($grouped[$name])) $grouped[$name] = 0;
                $grouped[$name]++;
            }

            foreach ($grouped as $name => $count) {
                $departmentStats[] = [
                    'jabatan_id'   => $name,
                    'jabatan_name' => $name,
                    'user_count'   => $count
                ];
            }
        }

        return $departmentStats;
    }
}