<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class ImageController extends Controller
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function serve($filename)
    {
        // Security: sanitize filename
        $filename = basename($filename);
        
        // Extract booking ID from filename (format: db_randomname.jpg)
        if (strpos($filename, 'db_') === 0) {
            // This is a database-stored image - find by filename
            $imageData = $this->getImageFromDatabaseByFilename($filename);
        } else {
            // This is a legacy file-stored image - find by original filename
            $imageData = $this->getImageFromDatabaseByOriginalFilename($filename);
        }
        
        if ($imageData) {
            // Serve image from database
            return $this->response
                ->setContentType($imageData['mime_type'])
                ->setHeader('Cache-Control', 'public, max-age=3600')
                ->setBody($imageData['data']);
        } else {
            // Serve default image
            return $this->serveDefaultImage("Image not found in database");
        }
    }

    private function getImageFromDatabaseByFilename($filename)
    {
        $builder = $this->db->table('booking_tbl');
        $builder->select('doc_attachment_data, doc_attachment_mime, doc_attachment_size');
        $builder->where('doc_Attachment', $filename);
        $result = $builder->get()->getRow();
        
        if ($result && !empty($result->doc_attachment_data)) {
            return [
                'data' => $result->doc_attachment_data,
                'mime_type' => $result->doc_attachment_mime ?: 'image/jpeg',
                'size' => $result->doc_attachment_size
            ];
        }
        
        return null;
    }

    private function getImageFromDatabaseByOriginalFilename($filename)
    {
        $builder = $this->db->table('booking_tbl');
        $builder->select('doc_attachment_data, doc_attachment_mime, doc_attachment_size');
        $builder->where('doc_attachment_name', $filename);
        $result = $builder->get()->getRow();
        
        if ($result && !empty($result->doc_attachment_data)) {
            return [
                'data' => $result->doc_attachment_data,
                'mime_type' => $result->doc_attachment_mime ?: 'image/jpeg',
                'size' => $result->doc_attachment_size
            ];
        }
        
        return null;
    }

    private function serveDefaultImage($message = "")
    {
        $defaultImage = WRITEPATH . 'uploads/default.jpg';
        
        if (!file_exists($defaultImage)) {
            $this->createDefaultImage($defaultImage, $message);
        }
        
        $fileInfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $fileInfo->file($defaultImage);
        
        return $this->response
            ->setContentType($mimeType)
            ->setBody(file_get_contents($defaultImage));
    }

    private function createDefaultImage($path, $message = "")
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        $image = imagecreate(600, 400);
        $bgColor = imagecolorallocate($image, 248, 249, 250);
        $borderColor = imagecolorallocate($image, 222, 226, 230);
        $textColor = imagecolorallocate($image, 108, 117, 125);
        
        imagefill($image, 0, 0, $bgColor);
        imagerectangle($image, 0, 0, 599, 399, $borderColor);
        
        // Add text
        $text = "Image Not Available";
        $fontSize = 5;
        $textWidth = imagefontwidth($fontSize) * strlen($text);
        $x = (600 - $textWidth) / 2;
        imagestring($image, $fontSize, $x, 180, $text, $textColor);
        
        // Add message
        if ($message) {
            $msgWidth = imagefontwidth(3) * strlen($message);
            $msgX = (600 - $msgWidth) / 2;
            imagestring($image, 3, $msgX, 220, $message, $textColor);
        }
        
        imagejpeg($image, $path, 85);
        imagedestroy($image);
    }
}