<?php
namespace App;

class FileUpload {
    private $pdo;
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    private $maxSize = 2 * 1024 * 1024; // 2MB

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function uploadFiles($files) {
        $uploadedFiles = [];

        foreach ($files['name'] as $key => $name) {
            $type = $files['type'][$key];
            $tmpName = $files['tmp_name'][$key];
            $error = $files['error'][$key];
            $size = $files['size'][$key];

            if ($error !== UPLOAD_ERR_OK) {
                throw new \Exception("Error uploading file $name");
            }

            if (!in_array($type, $this->allowedTypes)) {
                throw new \Exception("File type $type is not allowed");
            }

            if ($size > $this->maxSize) {
                throw new \Exception("File $name exceeds the maximum size of 2MB");
            }

            $newName = uniqid() . '_' . $name;
            $destination = __DIR__ . '/uploads/' . $newName;

            if (move_uploaded_file($tmpName, $destination)) {
                $this->saveFileToDatabase($newName, $type, $size);
                $uploadedFiles[] = $newName;
            } else {
                throw new \Exception("Failed to move uploaded file $name");
            }
        }

        return $uploadedFiles;
    }

    private function saveFileToDatabase($name, $type, $size) {
        $stmt = $this->pdo->prepare("INSERT INTO files (name, type, size) VALUES (:name, :type, :size)");
        $stmt->execute([':name' => $name, ':type' => $type, ':size' => $size]);
    }
}