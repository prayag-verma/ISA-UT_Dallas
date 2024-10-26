<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure the user is logged in
requireLogin();

// Check if the user has permission to export messages
if (!isAdmin($_SESSION['user_id']) && !$_SESSION['can_export_messages']) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'You do not have permission to export messages.']);
    exit();
}

// Get the message IDs to export
$messageIds = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];

if (empty($messageIds)) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'No messages selected for export.']);
    exit();
}

$conn = getDbConnection();

// Prepare the query
$placeholders = implode(',', array_fill(0, count($messageIds), '?'));
$query = "SELECT id, FirstName, LastName, Email, Phone, Purpose, Message, Submission_date 
          FROM messages 
          WHERE id IN ($placeholders)
          ORDER BY Submission_date DESC";

$stmt = $conn->prepare($query);

if ($stmt === false) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare query: ' . $conn->error]);
    exit();
}

// Bind the message IDs
$types = str_repeat('i', count($messageIds));
$stmt->bind_param($types, ...$messageIds);

if (!$stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Failed to execute query: ' . $stmt->error]);
    exit();
}

// Bind the result variables
$stmt->bind_result($id, $firstName, $lastName, $email, $phone, $purpose, $message, $submissionDate);

// Prepare CSV data with the new columns
$csvData = [
    ['First Name', 'Last Name', 'Email', 'Phone', 'Purpose', 'Message', 'Submission Date', 'Submission Time']
];

while ($stmt->fetch()) {
    // Split the submission date into date and time components
    $dateTime = new DateTime($submissionDate);
    $submissionDateOnly = $dateTime->format('Y-m-d'); // Extract only the date
    $submissionTimeOnly = $dateTime->format('h:i:s A'); // Extract time in 12-hour format with AM/PM

    $csvData[] = [
        $firstName,
        $lastName,
        $email,
        $phone,
        $purpose,
        $message,
        $submissionDateOnly,
        $submissionTimeOnly
    ];
}

$stmt->close();
$conn->close();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="exported_messages.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Output CSV data
$output = fopen('php://output', 'w');
foreach ($csvData as $row) {
    fputcsv($output, $row);
}
fclose($output);

exit();
