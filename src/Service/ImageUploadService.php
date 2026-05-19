<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadService
{
    private $uploadsDir;

    public function __construct(string $uploadsDir)
    {
        $this->uploadsDir = $uploadsDir;
    }

    public function uploadImage(UploadedFile $file, string $type): string
    {
        $fileName = uniqid() . '.' . $file->guessExtension();
        $file->move($this->uploadsDir . '/' . $type, $fileName);
        
        return '/uploads/' . $type . '/' . $fileName;
    }

    public function deleteImage(string $path): void
    {
        $fullPath = $this->uploadsDir . '/../' . ltrim($path, '/');
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
