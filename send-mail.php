<?php
// Configuration
$recipient_email = "imperviiuss@gmail.com"; 
$subject_prefix = "Сообщение с сайта: ";

// Process form data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form fields
    $name = isset($_POST['name']) ? strip_tags(trim($_POST['name'])) : '';
    $company = isset($_POST['company']) ? strip_tags(trim($_POST['company'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST['phone']) ? strip_tags(trim($_POST['phone'])) : '';
    $subject = isset($_POST['subject']) ? strip_tags(trim($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? strip_tags(trim($_POST['message'])) : '';
    $privacy = isset($_POST['privacy']) ? true : false;
    
    // Format subject based on selection
    $subject_text = "Запрос информации";
    switch ($subject) {
        case 'information':
            $subject_text = "Запрос информации о продукции";
            break;
        case 'price':
            $subject_text = "Запрос цены";
            break;
        case 'order':
            $subject_text = "Размещение заказа";
            break;
        case 'other':
            $subject_text = "Другое сообщение";
            break;
    }
    
    // Validate key fields
    $errors = array();
    if (empty($name)) {
        $errors[] = "Пожалуйста, укажите ваше имя.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Пожалуйста, укажите корректный email адрес.";
    }
    if (empty($phone)) {
        $errors[] = "Пожалуйста, укажите номер телефона.";
    }
    if (empty($message)) {
        $errors[] = "Пожалуйста, введите сообщение.";
    }
    if (!$privacy) {
        $errors[] = "Пожалуйста, подтвердите согласие на обработку персональных данных.";
    }
    
    // If no errors, send email
    if (empty($errors)) {
        // Prepare email content
        $email_content = "Имя: $name\n";
        if (!empty($company)) {
            $email_content .= "Компания: $company\n";
        }
        $email_content .= "Email: $email\n";
        $email_content .= "Телефон: $phone\n";
        $email_content .= "Тема: $subject_text\n\n";
        $email_content .= "Сообщение:\n$message\n";
        
        // Set email headers
        $email_headers = "From: $name <$email>\r\n";
        $email_headers .= "Reply-To: $email\r\n";
        
        // Send email
        if (mail($recipient_email, $subject_prefix . $subject_text, $email_content, $email_headers)) {
            // Success response
            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "message" => "Спасибо! Ваше сообщение отправлено."
            ]);
        } else {
            // Server error
            http_response_code(500);
            echo json_encode([
                "status" => "error",
                "message" => "Ошибка при отправке сообщения. Пожалуйста, попробуйте позже."
            ]);
        }
    } else {
        // Validation error
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => "Пожалуйста, исправьте следующие ошибки:",
            "errors" => $errors
        ]);
    }
} else {
    // Not a POST request
    http_response_code(403);
    echo json_encode([
        "status" => "error",
        "message" => "Неверный метод запроса."
    ]);
}
?>