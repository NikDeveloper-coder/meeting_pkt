<?php
namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table = 'booking_tbl';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'booking_ref', 'user_id', 'reason', 'booking_date', 
        'start_time', 'end_time', 'extra_request', 'doc_Attachment', 
        'created_date', 'extra_info'
    ];
    protected $useTimestamps = false;
    
    public function getBookingsWithUsers($where = [])
    {
        $builder = $this->db->table('booking_tbl b');
        $builder->select('b.*, u.full_name, u.email, u.jabatan_id');
        $builder->join('user_tbl u', 'b.user_id = u.user_Id');
        
        if (!empty($where)) {
            $builder->where($where);
        }
        
        $builder->orderBy('b.created_date', 'DESC');
        return $builder->get()->getResultArray();
    }
    
    public function getUserBookings($user_id, $date_filter = '')
    {
        $builder = $this->db->table('booking_tbl b');
        $builder->select('b.*, u.full_name, u.Email');
        $builder->join('user_tbl u', 'b.user_id = u.user_Id', 'left');
        $builder->where('b.user_id', $user_id);
        
        if (!empty($date_filter)) {
            $builder->where('b.booking_date', $date_filter);
        }
        
        $builder->orderBy('b.booking_date', 'DESC');
        
        return $builder->get()->getResultArray();
    }
    
    // NEW METHOD: Get all bookings for public calendar
    public function getAllBookingsForCalendar()
    {
        $builder = $this->db->table('booking_tbl b');
        $builder->select('b.booking_date, b.start_time, b.end_time, b.reason, b.extra_info, u.full_name');
        $builder->join('user_tbl u', 'b.user_id = u.user_Id', 'left');
        $builder->where('b.booking_date >=', date('Y-m-d')); // Only current and future dates
        $builder->orderBy('b.booking_date', 'ASC');
        $builder->orderBy('b.start_time', 'ASC');
        
        return $builder->get()->getResultArray();
    }
}