<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\UserModel;

class BookingController extends BaseController
{
    public function update($id)
    {
        // Check if user is logged in - FIX: Use correct session key
        if (!session()->get('user_Id')) {
            return redirect()->to('/login');
        }

        $bookingModel = new BookingModel();
        
        // Get booking data
        $booking = $bookingModel->find($id);
        
        // Check if booking exists and belongs to user - FIX: Use correct session key
        if (!$booking || $booking['user_id'] != session()->get('user_Id')) {
            return redirect()->back()->with('error', 'Booking not found or access denied.');
        }

        // Handle form submission
        if ($this->request->getMethod() === 'POST') {
            $validationRules = [
                'booking_date' => 'required|valid_date',
                'start_time' => 'required',
                'end_time' => 'required',
                'reason' => 'required|min_length[5]'
            ];

            if ($this->validate($validationRules)) {
                $updateData = [
                    'booking_date' => $this->request->getPost('booking_date'),
                    'start_time' => $this->request->getPost('start_time'),
                    'end_time' => $this->request->getPost('end_time'),
                    'reason' => $this->request->getPost('reason'),
                    'extra_request' => $this->request->getPost('extra_request')
                ];

                // Handle file upload if exists - FIX: Use correct path
                $docAttachment = $this->request->getFile('doc_attachment');
                if ($docAttachment && $docAttachment->isValid() && !$docAttachment->hasMoved()) {
                    $newName = $docAttachment->getRandomName();
                    $docAttachment->move(ROOTPATH . 'public/uploads', $newName); // FIX: Use public/uploads
                    $updateData['doc_Attachment'] = 'uploads/' . $newName;
                    
                    // Delete old file if exists - FIX: Use correct path
                    if (!empty($booking['doc_Attachment']) && file_exists(ROOTPATH . 'public/' . $booking['doc_Attachment'])) {
                        unlink(ROOTPATH . 'public/' . $booking['doc_Attachment']);
                    }
                }

                if ($bookingModel->update($id, $updateData)) {
                    return redirect()->to('/dashboard')->with('success', 'Booking updated successfully!');
                } else {
                    return redirect()->back()->with('error', 'Failed to update booking.');
                }
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        // Display update form
        $data = [
            'title' => 'Update Booking',
            'booking' => $booking,
            'validation' => \Config\Services::validation()
        ];

        return view('booking/booking_update', $data);
    }

    public function delete($id)
    {
        // Check if user is logged in - FIX: Use correct session key
        if (!session()->get('user_Id')) {
            return redirect()->to('/login');
        }

        $bookingModel = new BookingModel();
        
        // Get booking data
        $booking = $bookingModel->find($id);
        
        // Check if booking exists and belongs to user - FIX: Use correct session key
        if (!$booking || $booking['user_id'] != session()->get('user_Id')) {
            return redirect()->back()->with('error', 'Booking not found or access denied.');
        }

        // Delete attachment file if exists - FIX: Use correct path
        if (!empty($booking['doc_Attachment']) && file_exists(ROOTPATH . 'public/' . $booking['doc_Attachment'])) {
            unlink(ROOTPATH . 'public/' . $booking['doc_Attachment']);
        }

        // Delete booking
        if ($bookingModel->delete($id)) {
            return redirect()->to('/dashboard')->with('success', 'Booking deleted successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to delete booking.');
        }
    }
    
    // Method untuk create booking
    public function create()
    {
        // Check if user is logged in - FIX: Use correct session key
        if (!session()->get('user_Id')) {
            return redirect()->to('/login');
        }

        if ($this->request->getMethod() === 'POST') {
            $validationRules = [
                'booking_date' => 'required|valid_date',
                'start_time' => 'required',
                'end_time' => 'required',
                'reason' => 'required|min_length[5]'
            ];

            if ($this->validate($validationRules)) {
                $bookingModel = new BookingModel();
                
                $bookingData = [
                    'booking_ref' => 'BRF_' . time(),
                    'user_id' => session()->get('user_Id'), // FIX: Use correct session key
                    'booking_date' => $this->request->getPost('booking_date'),
                    'start_time' => $this->request->getPost('start_time'),
                    'end_time' => $this->request->getPost('end_time'),
                    'reason' => $this->request->getPost('reason'),
                    'extra_request' => $this->request->getPost('extra_request'),
                    'created_date' => date('Y-m-d H:i:s'),
                    'extra_info' => 'Pending'
                ];

                // Handle file upload - FIX: Use correct path
                $docAttachment = $this->request->getFile('doc_attachment');
                if ($docAttachment && $docAttachment->isValid() && !$docAttachment->hasMoved()) {
                    $newName = $docAttachment->getRandomName();
                    $docAttachment->move(ROOTPATH . 'public/uploads', $newName); // FIX: Use public/uploads
                    $bookingData['doc_Attachment'] = 'uploads/' . $newName;
                }

                if ($bookingModel->insert($bookingData)) {
                    return redirect()->to('/dashboard')->with('success', 'Booking created successfully!');
                } else {
                    return redirect()->back()->with('error', 'Failed to create booking.');
                }
            } else {
                return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
            }
        }

        return view('booking_create');
    }
}