<?php
// Include database configuration and helper functions
require_once 'config.php';
// Include PHPMailer classes
require_once '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Check if admin is logged in
require_admin_login();

// Start the session if one doesn't exist already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Process the email response submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    if (!isset($_POST['email']) || !isset($_POST['subject']) || !isset($_POST['message']) || !isset($_POST['message_id'])) {
        $_SESSION['email_action'] = [
            'success' => false,
            'message' => 'Missing required fields for email response.'
        ];
        header("Location: contact_messages.php");
        exit;
    }
    
    // Get form data
    $to_email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars($_POST['subject']);
    $message = $_POST['message']; // Don't sanitize here as we'll use PHPMailer's features
    $message_id = (int)$_POST['message_id'];
    
    // Admin email
    $admin_email = "adventuretravelsrilanka@gmail.com";
    
    // Validate email
    if (!filter_var($to_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['email_action'] = [
            'success' => false,
            'message' => 'Invalid email address.'
        ];
        header("Location: contact_messages.php");
        exit;
    }
    
    // Format email message with HTML
    $email_body = "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
            }
            .email-container {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
                background-color: #f9f9f9;
            }
            .header {
                border-bottom: 2px solid #176C65;
                padding-bottom: 10px;
                margin-bottom: 20px;
                text-align: center;
            }
            .footer {
                margin-top: 30px;
                padding-top: 10px;
                border-top: 1px solid #ddd;
                font-size: 0.9em;
                color: #777;
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <h2>Adventure Travel</h2>
            </div>
            <p>" . nl2br($message) . "</p>
            <p>Best regards,<br>Adventure Travel Team</p>
            <div class='footer'>
                <p>This is a response to your recent inquiry. If you have any further questions, please feel free to contact us via WhatsApp at +94 71 538 0080.</p>
                <p>&copy; " . date('Y') . " Adventure Travel. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'adventuretravelsrilanka@gmail.com';
        $mail->Password = 'bvyogoramvtwezsr'; // App password provided
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Recipients
        $mail->setFrom('adventuretravelsrilanka@gmail.com', 'Adventure Travel');
        $mail->addAddress($to_email);
        $mail->addReplyTo('adventuretravelsrilanka@gmail.com', 'Adventure Travel');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $email_body;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $message));
        
        $email_sent = $mail->send();
        
        // If email is successfully sent, mark the message as responded in the database
        if ($email_sent) {
            // Update the contact message status to responded
            $update_query = "UPDATE contact_messages SET is_responded = 1, responded_at = NOW(), is_read = 1, read_at = NOW() WHERE message_id = ?";
            $stmt = mysqli_prepare($conn, $update_query);
            mysqli_stmt_bind_param($stmt, "i", $message_id);
            $db_update = mysqli_stmt_execute($stmt);
            
            $_SESSION['email_action'] = [
                'success' => true,
                'message' => 'Your response has been sent successfully.'
            ];
        }
    } catch (Exception $e) {
        $_SESSION['email_action'] = [
            'success' => false,
            'message' => 'Failed to send email: ' . $mail->ErrorInfo
        ];
    }
    
    // Redirect back to contact messages page
    header("Location: contact_messages.php");
    exit;
} else {
    // If accessed directly without POST data, redirect to contact messages page
    header("Location: contact_messages.php");
    exit;
}
?>