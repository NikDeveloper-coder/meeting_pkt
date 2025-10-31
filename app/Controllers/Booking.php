<?php
namespace App\Controllers;

use App\Models\BookingModel;

class Booking extends BaseController
{
    protected $bookingModel;
    
    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        
        if (!session()->get('role')) {
            return redirect()->to('/login');
        }
    }
    
    public function index()
    {
        $data = [
            'booking_ref' => "BKG" . uniqid(),
            'user_id' => session()->get('user_Id'),
            'current_date' => date("Y-m-d (l)")
        ];
        
        return view('booking/index', $data);
    }
    
    public function create()
    {
        $bookingData = [
            'booking_ref' => $this->request->getPost('booking_ref'),
            'user_id' => $this->request->getPost('user_id'),
            'reason' => $this->request->getPost('reason'),
            'booking_date' => $this->request->getPost('booking_date'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'extra_request' => $this->request->getPost('extra_request'),
            'doc_Attachment' => $this->handleFileUpload(),
            'created_date' => date('Y-m-d H:i:s')
        ];
        
        if ($this->bookingModel->insert($bookingData)) {
            return redirect()->to('/booking')->with('success', 'Booking Successful!');
        } else {
            return redirect()->to('/booking')->with('error', 'Booking Failed!');
        }
    }
    
    private function handleFileUpload()
    {
        $file = $this->request->getFile('attachment');
        
        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads', $newName);
            return 'uploads/' . $newName;
        }
        
        return null;
    }
}