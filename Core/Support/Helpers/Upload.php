<?php

declare(strict_types=1);

namespace App\Core\Support\Helpers;

use Aws\S3\S3Client;

class Upload
{
    private string $field;
    private array $errors = [];
    private array $file;
    private int $maxSize = 2000000;
    private array $allowedFileTypes = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
    private string $directory = "uploads"; // default upload folder
    private bool $required = false;
    private string $customName = '';
    private string $cloudStorageType = 'local'; // Default to local storage
    private string $s3BucketName = 'your-s3-bucket-name'; // Update this with your bucket name

    public function __construct(array $file, string $field)
    {
        $this->file = $file;
        $this->field = $field;
        $this->validateInitial();
    }

    public function setName(string $customName): void
    {
        $this->customName = $customName;
    }

    public function setMaxSize(int $maxSize): void
    {
        $this->maxSize = $maxSize;
    }

    public function setAllowedFileTypes(array $allowedFileTypes): void
    {
        $this->allowedFileTypes = $allowedFileTypes;
    }

    public function setUploadDirectory(string $directory): void
    {
        $this->directory = $directory;
    }

    public function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    public function validate(): array
    {
        $this->errors = [];
        $this->validateRequired();
        $this->validateSize();
        $this->validateFileType();

        return $this->errors;
    }

    public function upload(): bool
    {
        if (!$this->errors) {
            $destination = $this->getUploadDestination();
            return move_uploaded_file($this->file['tmp_name'], $destination);
        }

        return false;
    }

    private function formatBytes(int $bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes, $precision) . $units[$pow];
    }

    private function validateRequired(): void
    {
        if (empty($this->file['tmp_name']) && $this->required) {
            $this->errors[$this->field] = "File is required";
        }
    }

    private function validateSize(): void
    {
        if (!empty($this->file['tmp_name']) && $this->file['size'] > $this->maxSize) {
            $this->errors[$this->field] = "Exceeded file size limit of " . $this->formatBytes($this->maxSize);
        }
    }

    private function validateFileType(): void
    {
        if (!empty($this->file['tmp_name']) && empty($this->errors)) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $type = $finfo->file($this->file['tmp_name']);
            if (!in_array($type, $this->allowedFileTypes)) {
                $allowedTypes = implode(', ', array_values($this->allowedFileTypes));
                $this->errors[$this->field] = "Not an allowed file type. Must be $allowedTypes";
            }
        }
    }

    private function getFinalFileName(): string
    {
        if (!empty($this->customName)) {
            return $this->customName . '.' . $this->file['ext'];
        }
        return $this->file['name'];
    }

    private function getUploadDirectory(): string
    {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
        return $this->directory;
    }

    private function getUploadDestination(): string
    {
        $destination = $this->getUploadDirectory() . DIRECTORY_SEPARATOR;
        $destination .= $this->getFinalFileName();

        return $destination;
    }

    private function validateInitial(): void
    {
        if (!isset($this->file[$this->field]) || is_array($this->file[$this->field]['error'])) {
            throw new \RuntimeException("Something is wrong with the file.");
        }

        $this->file['ext'] = pathinfo($this->file[$this->field]['name'], PATHINFO_EXTENSION);
    }


    // For cloud storage upload.

    public function setCloudStorageType(string $cloudStorageType): void
    {
        $this->cloudStorageType = $cloudStorageType;
    }

    public function setS3BucketName(string $s3BucketName): void
    {
        $this->s3BucketName = $s3BucketName;
    }

    public function uploadToCloud(): bool
    {
        if ($this->cloudStorageType === 's3') {
            return $this->uploadToS3();
        }

        return false;
    }

    private function uploadToS3(): bool
    {
        $s3 = new S3Client([
            'version' => 'latest',
            'region' => 'your-aws-region',
            'credentials' => [
                'key' => 'your-aws-access-key',
                'secret' => 'your-aws-secret-key',
            ],
        ]);

        $destination = $this->getFinalFileName();

        try {
            $result = $s3->putObject([
                'Bucket' => $this->s3BucketName,
                'Key' => $destination,
                'Body' => fopen($this->file['tmp_name'], 'rb'),
                'ACL' => 'public-read',
            ]);

            return isset($result['ObjectURL']);
        } catch (\Aws\S3\Exception\S3Exception $e) {
            return false;
        }
    }
}
