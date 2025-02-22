<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);

    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO users (username, password, name, email, role) VALUES (?, ?, ?, ?, 'employee')");
    $stmt->bind_param("ssss", $username, $password, $name, $email);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Employee registered successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to register employee']);
    }

    $stmt->close();
    $conn->close();
}