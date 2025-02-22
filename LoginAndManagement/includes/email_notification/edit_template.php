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

// Fetch email categories for the dropdown
$categoryQuery = "SELECT * FROM email_categories ORDER BY name";
$categoryResult = $conn->query($categoryQuery);
$categories = array();
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row;
}

// Fetch officer positions for the dropdown
$positionQuery = "SELECT * FROM positions ORDER BY name";
$positionResult = $conn->query($positionQuery);
$positions = array();
while ($row = $positionResult->fetch_assoc()) {
    $positions[] = $row;
}

// Start the session if not already started
// session_start();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = $_POST['category_id'];
    $subject = trim($_POST['subject']);
    $body = trim($_POST['body']);

    // Check if user ID is set in session
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id']; // Replace with the actual way to get the user ID
    } else {
        error_log("User ID not set in session.");
        exit("User ID is required."); // Handle the case where user ID is not set
    }

    // Fetch user data
    $userData = getUserData($conn, $userId);

    // Replace placeholders in the email body
    $body = replacePlaceholders($body, $userData);

    // Update email template
    $stmt = $conn->prepare("UPDATE email_templates SET category_id = ?, subject = ?, body = ? WHERE id = ?");
    $stmt->bind_param("issi", $categoryId, $subject, $body, $templateId);
    if ($stmt->execute()) {
        $message = "Template updated successfully.";
        $messageType = "success";
    } else {
        $message = "Error updating template: " . $conn->error;
        $messageType = "danger";
    }
    $stmt->close();
}


// Fetch template data for editing
$query = "SELECT t.*, c.name as category_name FROM email_templates t
          JOIN email_categories c ON t.category_id = c.id
          WHERE t.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $templateId);
$stmt->execute();
$stmt->bind_result($id, $category_id, $receiver_position_id, $subject, $body, $trigger_auto_email, $scheduled_email, $created_by, $created_at, $updated_at, $category_name);
$stmt->fetch();
$stmt->close();

if (!$id) {
    echo "<div class='alert alert-danger'>Template not found.</div>";
    return;
}

// Placeholder options for the user to use in templates
$placeholders = [
    '{first_name}', '{last_name}', '{username}', '{password}', '{dob}', '{email}', '{mobile}', 
    '{degree}', '{major}', '{joining_date}', '{expected_grad_date}', '{officer_ranking}', 
    '{position}', '{profile_picture}', '{role}', '{status}', '{retirement_date}', '{isa_logo}'
];
?>

<h2>Edit Template</h2>

<?php if (isset($message)): ?>
<div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label for="category_id" class="form-label">Email Category</label>
        <select class="form-select" id="category_id" name="category_id" required>
            <?php foreach ($categories as $category): ?>
                <option value="<?php echo $category['id']; ?>" <?php echo $category['id'] == $category_id ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="subject" class="form-label">Email Subject</label>
        <input type="text" class="form-control" id="subject" name="subject" required value="<?php echo htmlspecialchars($subject); ?>">
    </div>

    <div class="mb-3">
        <label for="body" class="form-label">Email Message</label>
        <textarea class="form-control" id="body" name="body" rows="10" required><?php echo htmlspecialchars($body); ?></textarea>
        <small class="text-muted">Available placeholders: <?php echo implode(', ', $placeholders); ?></small>
    </div>

    <button type="submit" class="btn btn-primary">Update Template</button>
    <a href="email_notification.php?submenu=email_templates" class="btn btn-secondary">Cancel</a>
</form>

<?php
// Function to replace placeholders with actual user data
function replacePlaceholders($body, $userData) {
    $placeholders = [
        '{first_name}' => $userData['first_name'],
        '{last_name}' => $userData['last_name'],
        '{username}' => $userData['username'],
        '{password}' => $userData['password'],
        '{dob}' => $userData['dob'],
        '{email}' => $userData['email'],
        '{mobile}' => $userData['mobile'],
        '{degree}' => $userData['degree'],
        '{major}' => $userData['major'],
        '{joining_date}' => $userData['joining_date'],
        '{expected_grad_date}' => $userData['expected_grad_date'],
        '{officer_ranking}' => $userData['officer_ranking'],
        '{position}' => $userData['position'],
        '{profile_picture}' => $userData['profile_picture'],
        '{role}' => $userData['role'],
        '{status}' => $userData['status'],
        '{retirement_date}' => $userData['retirement_date'],
        '{isa_logo}' => '<img src="uploads/logo/isa_logo.png">' // Example logo path
    ];

    foreach ($placeholders as $key => $value) {
        $body = str_replace($key, $value, $body);
    }

    return $body;
}

// Check if the function is already declared before declaring it
if (!function_exists('getUserData')) {
    function getUserData($userId, $conn) {
        $query = "SELECT first_name, last_name, username, password, dob, email, mobile, degree, major, joining_date, 
                         expected_grad_date, officer_ranking, position, profile_picture, role, status, retirement_date 
                  FROM users WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();

        return $userData;
    }
}
?>