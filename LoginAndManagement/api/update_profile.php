<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $mobile = sanitizeInput($_POST['mobile']);
    $position = sanitizeInput($_POST['position']);

    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, mobile = ?, position = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $email, $mobile, $position, $_SESSION['user_id']);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update profile']);
    }

    $stmt->close();
    $conn->close();
}