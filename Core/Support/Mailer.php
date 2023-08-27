<?php

declare(strict_types=1);

namespace App\Core\Support;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class Mailer {
    protected $mailer;

    public function __construct() {
        $this->mailer = new PHPMailer(true); // 'true' enables exceptions
        // Configure your PHPMailer settings here
    }

    public function sendEmailWithLayout($to, $subject, $template, $data, $fromEmail, $fromName) {
        try {
            // Server settings
            $this->mailer->isSMTP();
            $this->mailer->SMTPAuth = true;
            $this->mailer->SMTPSecure = 'tls'; // Use 'ssl' if needed
            $this->mailer->Host = 'smtp.example.com'; // Your SMTP server host
            $this->mailer->Port = 587; // SMTP server port

            // Authentication
            $this->mailer->Username = 'your_username';
            $this->mailer->Password = 'your_password';

            // Sender information
            $this->mailer->setFrom($fromEmail, $fromName);

            // Recipient
            $this->mailer->addAddress($to);

            // Load email template and layout
            $emailContent = $this->loadTemplate($template, $data);
            $layout = $this->loadLayout($emailContent);

            // Email content
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $layout;

            // Send email
            $this->mailer->send();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function loadTemplate($template, $data) {
        // Load and process your email template using your view class
        ob_start();
        require "path_to_your_views_folder/{$template}.php";
        $content = ob_get_clean();
        return $content;
    }

    private function loadLayout($content) {
        // Load and process your email layout using your view class
        ob_start();
        require "path_to_your_views_folder/email_layout.php";
        $layout = ob_get_clean();
        return str_replace('{{content}}', $content, $layout);
    }
}
