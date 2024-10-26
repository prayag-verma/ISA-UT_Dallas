<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

try {
    require_once '../includes/auth.php';
    require_once '../includes/db.php';
    require_once '../includes/functions.php';
    requireLogin();

    $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
    $perPage = isset($_GET['per_page']) ? intval($_GET['per_page']) : 10;
    $offset = ($page - 1) * $perPage;

    $conn = getDbConnection();
    
    // Get total count of messages
    $countStmt = $conn->prepare("SELECT COUNT(*) as total FROM messages");
    $countStmt->execute();
    $countStmt->bind_result($totalMessages);
    $countStmt->fetch();
    $countStmt->close();
    
    // Get messages for current page
    $query = "SELECT id, FirstName, LastName, Email, Phone, Purpose, Message, Submission_date 
              FROM messages 
              ORDER BY Submission_date DESC 
              LIMIT ? OFFSET ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $perPage, $offset);
    $stmt->execute();
    $stmt->bind_result($id, $firstName, $lastName, $email, $phone, $purpose, $message, $submissionDate);
    
    $messages = [];
    while ($stmt->fetch()) {
        $date = new DateTime($submissionDate, new DateTimeZone('UTC'));
        $date->setTimezone(new DateTimeZone('America/Chicago'));
        
        $messages[] = [
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
    }
    
    $totalPages = ceil($totalMessages / $perPage);
    
    echo json_encode([
        'status' => 'success',
        'messages' => $messages,
        'total_pages' => $totalPages,
        'current_page' => $page
    ]);

    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}