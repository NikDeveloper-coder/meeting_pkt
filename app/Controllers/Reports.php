<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Reports extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        // Check if user is admin
        if (session()->get('Category') !== 'admin') {
            return redirect()->to('/admin/dashboard');
        }

        // Get stats data
        $stats = $this->getStats();
        
        // Get data for charts
        $monthly_bookings = $this->getMonthlyBookings();
        $status_distribution = $this->getStatusDistribution();
        $dept_distribution = $this->getDepartmentDistribution();
        $recent_bookings = $this->getRecentBookings();

        $data = [
            'section' => 'reports',
            'title' => 'Reports & Analytics',
            'stats' => $stats,
            'monthly_bookings' => $monthly_bookings,
            'status_distribution' => $status_distribution,
            'dept_distribution' => $dept_distribution,
            'recent_bookings' => $recent_bookings
        ];

        return view('admin/reports', $data);
    }

    public function printBooking($id = null)
    {
        if ($id) {
            // Single booking print
            $booking = $this->getBookingById($id);
            $data = [
                'print_all' => false,
                'booking' => $booking
            ];
        } else {
            // All bookings print
            $bookings = $this->getAllBookings();
            $data = [
                'print_all' => true,
                'bookings' => $bookings
            ];
        }

        return view('print_booking', $data);
    }

    public function exportBookings()
    {
        $bookings = $this->getAllBookingsForExport();
        
        $filename = "bookings_export_" . date('Y-m-d') . ".csv";
        
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        
        $file = fopen('php://output', 'w');
        
        // Add CSV headers
        fputcsv($file, [
            'Booking ID', 
            'Reference', 
            'User Name', 
            'Email', 
            'Department',
            'Booking Date', 
            'Start Time', 
            'End Time', 
            'Status', 
            'Reason'
        ]);
        
        // Add data
        foreach ($bookings as $booking) {
            fputcsv($file, [
                $booking['id'] ?? '',
                $booking['booking_ref'] ?? '',
                $booking['full_name'] ?? '',
                $booking['email'] ?? '',
                $booking['jabatan_id'] ?? '',
                $booking['booking_date'] ?? '',
                $booking['start_time'] ?? '',
                $booking['end_time'] ?? '',
                $booking['extra_info'] ?? 'Pending',
                $booking['reason'] ?? ''
            ]);
        }
        
        fclose($file);
        exit;
    }

    // Database query methods
    private function getStats()
    {
        $builder = $this->db->table('booking_tbl b');
        $builder->select('
            COUNT(*) as total_bookings,
            SUM(CASE WHEN b.extra_info = "Pending" THEN 1 ELSE 0 END) as pending_bookings,
            SUM(CASE WHEN b.extra_info = "Approved" THEN 1 ELSE 0 END) as approved_bookings,
            SUM(CASE WHEN b.extra_info = "Cancelled" THEN 1 ELSE 0 END) as cancelled_bookings
        ');
        $stats = $builder->get()->getRowArray();

        $userBuilder = $this->db->table('user_tbl');
        $userBuilder->select('COUNT(*) as total_users');
        $userStats = $userBuilder->get()->getRowArray();

        return [
            'total_bookings' => $stats['total_bookings'] ?? 0,
            'total_users' => $userStats['total_users'] ?? 0,
            'pending_bookings' => $stats['pending_bookings'] ?? 0,
            'approved_bookings' => $stats['approved_bookings'] ?? 0,
            'cancelled_bookings' => $stats['cancelled_bookings'] ?? 0
        ];
    }

    private function getMonthlyBookings()
    {
        $builder = $this->db->table('booking_tbl');
        $builder->select("
            DATE_FORMAT(booking_date, '%b') as month,
            COUNT(*) as count
        ");
        $builder->where('booking_date >=', date('Y-01-01'));
        $builder->groupBy('MONTH(booking_date), month');
        $builder->orderBy('MONTH(booking_date)');
        
        return $builder->get()->getResultArray();
    }

    private function getStatusDistribution()
    {
        $builder = $this->db->table('booking_tbl');
        $builder->select('
            COALESCE(extra_info, "Pending") as status,
            COUNT(*) as count
        ');
        $builder->groupBy('extra_info');
        
        return $builder->get()->getResultArray();
    }

    private function getDepartmentDistribution()
    {
        $builder = $this->db->table('user_tbl u');
        $builder->select('
            u.jabatan_id as jabatan_name,
            COUNT(*) as user_count
        ');
        $builder->groupBy('u.jabatan_id');
        
        return $builder->get()->getResultArray();
    }

    private function getRecentBookings()
    {
        $builder = $this->db->table('booking_tbl b');
        $builder->select('
            b.*,
            u.full_name,
            u.email,
            u.jabatan_id
        ');
        $builder->join('user_tbl u', 'b.user_id = u.user_Id');
        $builder->orderBy('b.created_date', 'DESC');
        $builder->limit(10);
        
        return $builder->get()->getResultArray();
    }

    private function getBookingById($id)
    {
        $builder = $this->db->table('booking_tbl b');
        $builder->select('
            b.*,
            u.full_name,
            u.email,
            u.jabatan_id
        ');
        $builder->join('user_tbl u', 'b.user_id = u.user_Id');
        $builder->where('b.id', $id);
        
        return $builder->get()->getRowArray();
    }

    private function getAllBookings()
    {
        $builder = $this->db->table('booking_tbl b');
        $builder->select('
            b.*,
            u.full_name,
            u.email,
            u.jabatan_id
        ');
        $builder->join('user_tbl u', 'b.user_id = u.user_Id');
        $builder->orderBy('b.booking_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }

    private function getAllBookingsForExport()
    {
        $builder = $this->db->table('booking_tbl b');
        $builder->select('
            b.*,
            u.full_name,
            u.email,
            u.jabatan_id
        ');
        $builder->join('user_tbl u', 'b.user_id = u.user_Id');
        $builder->orderBy('b.booking_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }
}