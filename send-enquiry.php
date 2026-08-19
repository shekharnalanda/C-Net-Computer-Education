<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(bool $success, string $message, int $status = 200): void {
    http_response_code($status);
    echo json_encode(['success' => $success, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Invalid request.', 405);
}

if (!empty($_POST['website'] ?? '')) {
    respond(true, 'Thank you. Your enquiry has been received.');
}

$token = (int)($_POST['form_token'] ?? 0);
$age = (int)(microtime(true) * 1000) - $token;
if ($token < 1 || $age < 1500 || $age > 86400000) {
    respond(false, 'Please refresh the page and try again.', 400);
}

function clean(string $value, int $max): string {
    $value = trim(strip_tags($value));
    $value = preg_replace('/[\r\n]+/', ' ', $value) ?? '';
    return mb_substr($value, 0, $max);
}

$name = clean((string)($_POST['name'] ?? ''), 80);
$phone = clean((string)($_POST['phone'] ?? ''), 20);
$emailRaw = trim((string)($_POST['email'] ?? ''));
$email = $emailRaw === '' ? '' : filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$city = clean((string)($_POST['city'] ?? ''), 80);
$course = clean((string)($_POST['course'] ?? ''), 80);
$studentMessage = clean((string)($_POST['message'] ?? ''), 1000);

if ($name === '' || $phone === '' || $course === '') {
    respond(false, 'Name, mobile number and course are required.', 422);
}
if (!preg_match('/^[0-9+ -]{10,15}$/', $phone)) {
    respond(false, 'Please enter a valid mobile number.', 422);
}
if ($emailRaw !== '' && $email === false) {
    respond(false, 'Please enter a valid email address.', 422);
}

$to = 'cnet@mciedu.com';
$subject = 'New Course Enquiry - ' . $course . ' - ' . $name;
$body = "New enquiry received from the C-Net Computer Education website.\n\n";
$body .= "Student Name: {$name}\n";
$body .= "Mobile Number: {$phone}\n";
$body .= "Email: " . ($email ?: 'Not provided') . "\n";
$body .= "City: " . ($city ?: 'Not provided') . "\n";
$body .= "Interested Course: {$course}\n";
$body .= "Message: " . ($studentMessage ?: 'Not provided') . "\n\n";
$body .= "Submitted: " . date('d M Y, h:i A') . "\n";
$body .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n";

$host = preg_replace('/[^a-z0-9.-]/i', '', $_SERVER['HTTP_HOST'] ?? 'cnetcomputer.mciedu.com');
$headers = [
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'From: C-Net Website <cnet@mciedu.com>',
    'Reply-To: ' . ($email ?: 'cnet@mciedu.com'),
    'X-Mailer: PHP/' . PHP_VERSION,
    'X-Originating-Host: ' . $host
];

$sent = @mail($to, $subject, $body, implode("\r\n", $headers));
if (!$sent) {
    respond(false, 'Enquiry could not be sent. Please call or WhatsApp us.', 500);
}

respond(true, 'धन्यवाद! आपकी enquiry भेज दी गई है। हमारी टीम जल्द संपर्क करेगी।');

