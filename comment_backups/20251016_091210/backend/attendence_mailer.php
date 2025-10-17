<?php

// A safe way to display errors for debugging (disable in a live production environment)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Require the Composer autoloader
require '../vendor/autoload.php';

// --- Configuration ---
// Replace with your Gmail credentials
define('GMAIL_USER', 'vedanshpratapsingh@gmail.com'); // Your full Gmail address
define('GMAIL_APP_PASSWORD', 'urba ytmc eaim nufs'); // The 16-character App Password you generated

// --- Load JSON Data ---
$jsonData = '';
$sourceMessage = '';


// Priority 2: Check for a local JSON file if no POST data is found

$studentsJsonPath = __DIR__ . '/students.json';
if (file_exists($studentsJsonPath)) {
    $jsonData = file_get_contents($studentsJsonPath);
    $sourceMessage = "<h1>Processing JSON data from students.json file...</h1>";
    echo "<p>Loaded students.json from: $studentsJsonPath</p>";
}
else {
    http_response_code(400); // Bad Request
    $absPath = realpath($studentsJsonPath);
    die("Error: No JSON data found. Tried: $studentsJsonPath (resolved: $absPath). Please either provide a 'students.json' file or send data via a POST request.");
}

echo $sourceMessage;

// Decode the JSON data into a PHP associative array
$students = json_decode($jsonData, true);

// Check if JSON was decoded successfully
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    die("Error: Invalid JSON data provided. Decoding failed with error: " . json_last_error_msg());
}

// Instantiate PHPMailer
$mail = new PHPMailer(true); // `true` enables exceptions

try {
    // --- Server Settings ---
    // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output for troubleshooting
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = GMAIL_USER;
    $mail->Password   = GMAIL_APP_PASSWORD; // Use the App Password here
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // --- Sender Information ---
    $mail->setFrom(GMAIL_USER, 'School Attendance System');

    // --- Loop through each student record ---
    foreach ($students as $student) {
        // We only want to email students who were absent
        if (isset($student['attendance'][0]['status']) && $student['attendance'][0]['status'] === 'Absent') {

            // Get student details for personalization
            $studentName = $student['name'] ?? 'Student';
            $studentEmail = $student['email'] ?? null;
            $absenceDate = $student['attendance'][0]['date'] ?? 'a recent date';

            if (empty($studentEmail)) {
                echo "<p style='color:orange;'>Skipping {$studentName}: No email address found.</p>";
                continue; // Skip to the next student
            }

            try {
                // --- Recipient ---
                // Clear all addresses from the previous iteration before adding a new one
                $mail->clearAddresses();
                $mail->addAddress($studentEmail, $studentName);

                // --- Email Content ---
                $mail->isHTML(true); // Set email format to HTML
                $mail->Subject = 'Important: Absence Notification for ' . $studentName;

                $mail->Body = "
                    <html>
                    <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
                        <h2>Attendance Notification</h2>
                        <p>Dear {$studentName},</p>
                        <p>This is an automated notification from the school attendance system. Our records show that you were marked as <strong>absent</strong> from school on <strong>{$absenceDate}</strong>.</p>
                        <p>If you believe this is an error, please contact the school office as soon as possible.</p>
                        <p>Thank you.</p>
                        <p><strong>School Administration</strong></p>
                    </body>
                    </html>";
                
                $mail->AltBody = "Dear {$studentName},\n\nThis is an automated notification. Our records show that you were marked as absent from school on {$absenceDate}.\nIf you believe this is an error, please contact the school office.\n\nThank you,\nSchool Administration";

                // Send the email
                $mail->send();
                echo "<p style='color:green;'>Message sent successfully to {$studentName} ({$studentEmail})</p>";

            } catch (Exception $e) {
                // Catch errors for individual emails
                echo "<p style='color:red;'>Message could not be sent to {$studentName} ({$studentEmail}). Mailer Error: {$mail->ErrorInfo}</p>";
            }
        }
    }

    echo "<h3>Processing complete.</h3>";

} catch (Exception $e) {
    // Catch errors for the initial connection or configuration
    echo "<p style='color:red;'>A critical error occurred. Could not configure email server. Mailer Error: {$mail->ErrorInfo}</p>";
}

?>