<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

requireLogin();

$conn = getDbConnection();
$stmt = $conn->prepare("SELECT id FROM messages");
$stmt->execute();
$stmt->bind_result($id);

$ids = [];
while ($stmt->fetch()) {
    $ids[] = (int)$id;
}

header('Content-Type: application/json');
echo json_encode(['ids' => $ids]);

$stmt->close();
$conn->close();