<?php
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Base64 encoded 'anarart@alstrade.ch'
// Using your real hosted mailbox for both To and From ensures server delivery
$my_email = base64_decode('YW5hcmFydEBhbHN0cmFkZS5jaA==');

// Sanitize incoming form inputs
$name    = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
$company = filter_input(INPUT_POST, 'company', FILTER_SANITIZE_SPECIAL_CHARS);
$country = filter_input(INPUT_POST, 'country', FILTER_SANITIZE_SPECIAL_CHARS);
$email   = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone   = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_SPECIAL_CHARS);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

// Check required fields
if (!$name || !$email || !$company || !$phone || !$message) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$email_subject = "ALStrade Website Enquiry: " . ($subject ?: 'General Inquiry');

// Construct email body
$body  = "New contact form submission from ALStrade website:\n\n";
$body .= "Full Name: " . $name . "\n";
$body .= "Company:   " . $company . "\n";
$body .= "Country:   " . ($country ?: 'Not provided') . "\n";
$body .= "Email:     " . $email . "\n";
$body .= "Phone:     " . $phone . "\n";
$body .= "Subject:   " . $subject . "\n\n";
$body .= "Message:\n" . $message . "\n";

// Set From to your valid hosted address, Reply-To to the visitor's email
$headers  = "From: " . $my_email . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Send email
if (mail($my_email, $email_subject, $body, $headers)) {
    echo json_encode(['status' => 'success']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Server failed to send email']);
}
