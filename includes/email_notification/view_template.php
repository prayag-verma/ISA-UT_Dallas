<?php
if (!defined('INCLUDED')) {
    exit('Direct access is not allowed.');
}
global $conn;

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>No template ID provided.</div>";
    return;
}

$templateId = (int)$_GET['id'];

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
$stmt->close();

if (!$id) {
    echo "<div class='alert alert-danger'>Template not found.</div>";
    return;
}
?>

<h2>View Template</h2>

<div class="card">
    <div class="card-body">
        <h5 class="card-title"><?php echo htmlspecialchars($subject); ?></h5>
        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($category_name); ?></h6>
        <p class="card-text"><?php echo nl2br(htmlspecialchars($body)); ?></p>
        <p class="card-text"><small class="text-muted">Created by: <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></small></p>
        <p class="card-text"><small class="text-muted">Created at: <?php echo htmlspecialchars($created_at); ?></small></p>
        <p class="card-text"><small class="text-muted">Updated at: <?php echo htmlspecialchars($updated_at); ?></small></p>
    </div>
</div>

<div class="mt-3">
    <a href="email_notification.php?submenu=email_templates" class="btn btn-secondary">Back to Templates</a>
    <a href="email_notification.php?submenu=edit_template&id=<?php echo $id; ?>" class="btn btn-primary">Edit Template</a>
</div>