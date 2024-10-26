<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $employeeId = intval($_POST['employeeId']);
    $newPassword = password_hash($_POST['newPassword'], PASSWORD_DEFAULT);

    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $employeeId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to change password']);
    }

    $stmt->close();
    $conn->close();
}