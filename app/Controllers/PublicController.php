<?php
namespace App\Controllers;

use App\Models\BookingModel;

class PublicController extends BaseController
{
    protected $bookingModel;
    
    public function __construct()
    {
        $this->bookingModel = new BookingModel();
    }
    
    public function index()
    {
        return $this->calendar();
    }
    
    public function calendar()
    {
        // Get all bookings to show on calendar
        $bookings = $this->bookingModel->getAllBookingsForCalendar();
        
        // Format bookings for calendar display
        $calendarEvents = [];
        foreach ($bookings as $booking) {
            $status = $booking['extra_info'] ?: 'Pending';
            $color = $status === 'Approved' ? '#dc3545' : '#ffc107'; // Red for approved, yellow for pending
            
            $calendarEvents[] = [
                'title' => $booking['full_name'] . ' (' . $booking['start_time'] . ')',
                'start' => $booking['booking_date'] . 'T' . $booking['start_time'],
                'end' => $booking['booking_date'] . 'T' . $booking['end_time'],
                'color' => $color,
                'extendedProps' => [
                    'user' => $booking['full_name'],
                    'reason' => $booking['reason'],
                    'start_time' => $booking['start_time'],
                    'end_time' => $booking['end_time'],
                    'status' => $status
                ]
            ];
        }
        
        $data = [
            'title' => 'Meeting Room Availability Calendar',
            'calendarEvents' => $calendarEvents,
            'current_month' => date('F Y')
        ];
        
        return view('public/calendar', $data);
    }
    
    public function getBookedDates()
    {
        // Get all bookings
        $bookings = $this->bookingModel->getAllBookingsForCalendar();
        
        // Group by date
        $bookedSlots = [];
        foreach ($bookings as $booking) {
            $date = $booking['booking_date'];
            if (!isset($bookedSlots[$date])) {
                $bookedSlots[$date] = [];
            }
            
            $bookedSlots[$date][] = [
                'time' => $booking['start_time'] . ' - ' . $booking['end_time'],
                'user' => $booking['full_name'],
                'reason' => $booking['reason'],
                'status' => $booking['extra_info'] ?: 'Pending'
            ];
        }
        
        return $this->response->setJSON($bookedSlots);
    }
}