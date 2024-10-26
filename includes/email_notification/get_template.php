<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_GET['id'])) {
    exit('No template ID provided');
}

$templateId = (int)$_GET['id'];
$conn = getDbConnection();

$query = "SELECT t.id, t.category_id, t.subject, t.body, t.created_by, t.created_at, t.updated_at, 
                 c.name as category_name, u.first_name, u.last_name 
          FROM email_templates t
          JOIN email_categories c ON t.category_id = c.id
          JOIN users u ON t.created_by = u.id
          WHERE t.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $templateId);
$stmt->execute();
$stmt->bind_result($id, $category_id, $subject, $body, $created_by, $created_at, $updated_at, $category_name, $first_name, $last_name);
$stmt->fetch();

$template = array(
    'id' => $id,
    'category_id' => $category_id,
    'subject' => $subject,
    'body' => $body,
    'created_by' => $created_by,
    'created_at' => $created_at,
    'updated_at' => $updated_at,
    'category_name' => $category_name,
    'first_name' => $first_name,
    'last_name' => $last_name
);

header('Content-Type: application/json');
echo json_encode($template);

$stmt->close();
$conn->close();