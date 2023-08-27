<?php

namespace App\Core\Support\Helpers;

class FileUpload
{
    public $field;
    public array $errors = [];
    public mixed $file;
    public $fc, $d;
    public string $name;
    public string|array $ext;
    public mixed $tmp;
    public mixed $size;
    private $i;
    public int $maxSize = 2000000;
    public array $allowedFileTypes = ['jpg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif'];
    private string $directory = "uploads"; // default upload folder
    public bool $required = false;

    public function __construct($field)
    {
        $this->field = $field;
        $this->checkInitialError();
        $this->file = $_FILES[$field];
        $this->size = $this->file['size'];
        $this->tmp = $this->file['tmp_name'];
        $this->name = '';
        $this->ext = pathinfo($this->file['name'], PATHINFO_EXTENSION);
    }

    public function name($n): static
    {
        $this->name = $n;
        return $this;
    }

    public function directory($d): static
    {
        $this->directory = $d;
        return $this;
    }

    public function get_dir(): string
    {
        if (!is_dir($this->directory)) {
            mkdir($this->directory, 0777, true);
        }
        return $this->directory;
    }

    public function get_name(): string
    {
        if (empty($this->name)) {
            $this->name = date("YmdHis");
        }

        $this->fc = $this->get_dir() . DIRECTORY_SEPARATOR . $this->name . "." . $this->ext;
        //check filename is exists or not
        if (file_exists($this->fc)) {
            //generate new name
            $this->i = 0;
            do {
                $this->name = $this->name . $this->i;
                $this->fc = $this->get_dir() . DIRECTORY_SEPARATOR . $this->name . "." . $this->ext;
                $this->i++;
            } while (file_exists($this->fc));
        }

        return $this->name;
    }

    private function destination(): string
    {
        $this->d = "";
        $this->d = $this->get_dir() . DIRECTORY_SEPARATOR;
        $this->d .= $this->get_name();
        $this->d .= "." . $this->ext;

        return $this->d;
    }

    public function validate(): array
    {
        $this->errors = [];
        if (empty($this->tmp) && $this->required) {
            $this->errors[$this->field] = "File is required";
        }

        //check size
        if (!empty($this->tmp) && $this->size > $this->maxSize) {
            $this->errors[$this->field] = "Exceeded file size limit of " . $this->formatBytes($this->maxSize);
        }

        //check if allowed type
        if (!empty($this->tmp) && empty($this->errors)) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $type = $finfo->file($this->tmp);
            if (array_search($type, $this->allowedFileTypes) === false) {
                $this->errors[$this->field] = "Not an allowed file type. Must be " . implode(', ', array_keys($this->allowedFileTypes));
            }
        }
        return $this->errors;
    }

    public function upload(): bool
    {
        return move_uploaded_file($this->tmp, $this->destination());
    }

    private function formatBytes(int $bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes, $precision) . $units[$pow];
    }

    private function checkInitialError(): void
    {
        if (!isset($_FILES[$this->field]) || is_array($_FILES[$this->field]['error'])) {
            throw new \RuntimeException("Something is wrong with the file.");
        }
    }
}