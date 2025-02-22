<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

requireAdmin();

$conn = getDbConnection();
$stmt = $conn->prepare("SELECT id, name FROM users WHERE role = 'employee'");
$stmt->execute();
$stmt->bind_result($id, $name);

$employees = array();
while ($stmt->fetch()) {
    $employees[] = array(
        'id' => $id,
        'name' => $name
    );
}

$stmt->close();
$conn->close();

echo json_encode($employees);