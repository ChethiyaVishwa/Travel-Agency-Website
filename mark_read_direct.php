<?php
// Direct endpoint to mark messages as read without session dependencies
require_once 'admin/config.php';

// Allow cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Content-Type: application/json');

// Get user_id from request
$user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : (isset($_POST['user_id']) ? (int)$_POST['user_id'] : null);

// Default response
$response = [
    'success' => false,
    'message' => 'Invalid request',
    'affected_rows' => 0
];

if (!$user_id) {
    $response['message'] = 'Missing user_id parameter';
    echo json_encode($response);
    exit;
}

// Direct SQL update for user viewing admin messages
$sql = "UPDATE chat_messages SET is_read = 1 WHERE user_id = ? AND is_admin = 1 AND is_read = 0";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    $result = mysqli_stmt_execute($stmt);
    
    if ($result) {
        $affected_rows = mysqli_stmt_affected_rows($stmt);
        $response = [
            'success' => true,
            'message' => "Successfully marked $affected_rows messages as read",
            'affected_rows' => $affected_rows
        ];
    } else {
        $response['message'] = 'SQL execution failed: ' . mysqli_error($conn);
    }
    
    mysqli_stmt_close($stmt);
} else {
    $response['message'] = 'Failed to prepare SQL statement: ' . mysqli_error($conn);
}

echo json_encode($response); 