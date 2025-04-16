<?php
// Use PHPMailer instead of mail() function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form fields
    $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';
    
    // Validate key fields
    $errors = array();
    if (empty($name)) {
        $errors[] = "Пожалуйста, укажите ваше имя.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Пожалуйста, укажите корректный email адрес.";
    }
    
    // If no errors, send email
    if (empty($errors)) {
        $mail = new PHPMailer(true);
        
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = getenv('SMTP_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('SMTP_USERNAME');
            $mail->Password = getenv('SMTP_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            
            // Recipients
            $mail->setFrom($email, $name);
            $mail->addAddress('info@gilza-kz.kz');
            
            // Content
            $mail->isHTML(false);
            $mail->Subject = 'Сообщение с сайта: Форма контакта';
            $mail->Body = "Имя: $name\nEmail: $email\n\nСообщение:\n$message";
            
            $mail->send();
            echo json_encode(["status" => "success"]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $mail->ErrorInfo]);
        }
    } else {
        // Validation error
        echo json_encode(["status" => "error", "errors" => $errors]);
    }
}
?>
