<?php
/**
 * File Upload Utilities
 * Handle image uploads for profiles and properties
 */

/**
 * Upload image file
 */
function uploadImage($file, $directory = 'uploads/general') {
    // Validate file upload
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    
    if (!in_array($fileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only images are allowed.'];
    }
    
    // Validate file size (max 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = __DIR__ . '/../../' . $directory;
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'path' => $directory . '/' . $filename
        ];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Upload multiple images
 */
function uploadMultipleImages($files, $directory = 'uploads/general') {
    $uploadedFiles = [];
    $errors = [];
    
    // Handle both single and multiple file uploads
    if (!is_array($files['name'])) {
        $result = uploadImage($files, $directory);
        if ($result['success']) {
            $uploadedFiles[] = $result['path'];
        } else {
            $errors[] = $result['message'];
        }
    } else {
        $fileCount = count($files['name']);
        
        for ($i = 0; $i < $fileCount; $i++) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            ];
            
            $result = uploadImage($file, $directory);
            
            if ($result['success']) {
                $uploadedFiles[] = $result['path'];
            } else {
                $errors[] = $result['message'];
            }
        }
    }
    
    return [
        'success' => !empty($uploadedFiles),
        'files' => $uploadedFiles,
        'errors' => $errors
    ];
}

/**
 * Delete image file
 */
function deleteImage($filepath) {
    $fullPath = __DIR__ . '/../../' . $filepath;
    
    if (file_exists($fullPath)) {
        return unlink($fullPath);
    }
    
    return false;
}
