<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once '../includes/auth.php';
    require_once '../includes/db.php';
    require_once '../includes/functions.php';
    requireLogin();

    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        throw new Exception('Invalid message ID');
    }

    $messageId = intval($_GET['id']);

    $conn = getDbConnection();
    
    $query = "SELECT id, FirstName, LastName, Email, Phone, Purpose, Message, Submission_date 
              FROM messages 
              WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $messageId);
    $stmt->execute();
    $stmt->bind_result($id, $firstName, $lastName, $email, $phone, $purpose, $message, $submissionDate);
    
    if (!$stmt->fetch()) {
        throw new Exception('Message not found');
    }

    $date = new DateTime($submissionDate, new DateTimeZone('UTC'));
    $date->setTimezone(new DateTimeZone('America/Chicago'));
    
    $messageData = [
        'id' => $id,
        'FirstName' => $firstName,
        'LastName' => $lastName,
        'Email' => $email,
        'Phone' => $phone,
        'Purpose' => $purpose,
        'Message' => $message,
        'Submission_date' => $submissionDate,
        'enquiry_date' => $date->format('Y-m-d'),
        'enquiry_time' => $date->format('h:i A')
    ];

    echo json_encode([
        'status' => 'success',
        'message' => $messageData
    ]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}