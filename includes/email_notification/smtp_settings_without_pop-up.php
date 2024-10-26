<?php
if (!defined('INCLUDED')) {
    exit('Direct access is not allowed.');
}
global $conn;

// Ensure database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Fetch current SMTP settings
$query = "SELECT * FROM smtp_settings WHERE id = 1";
$result = $conn->query($query);

if ($result === false) {
    die("Error fetching SMTP settings: " . $conn->error);
}

$smtpSettings = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_smtp'])) {
    $host = trim($_POST['smtp_host']);
    $port = (int)$_POST['smtp_port'];
    $username = trim($_POST['smtp_username']);
    $password = trim($_POST['smtp_password']);
    $fromEmail = trim($_POST['from_email']);
    $fromName = trim($_POST['from_name']);

    // Verify if required fields are not empty
    if (!empty($host) && !empty($port) && !empty($username) && !empty($password) && !empty($fromEmail) && !empty($fromName)) {
        // Update query
        $updateQuery = "UPDATE smtp_settings SET 
                        host = ?, 
                        port = ?, 
                        username = ?, 
                        password = ?, 
                        from_email = ?, 
                        from_name = ? 
                        WHERE id = 1";
        
        $stmt = $conn->prepare($updateQuery);

        if ($stmt === false) {
            die("Error preparing update statement: " . $conn->error);
        }

        // Correct binding of parameters for the query
        $stmt->bind_param("sissss", $host, $port, $username, $password, $fromEmail, $fromName);

        // Execute the statement
        if ($stmt->execute()) {
            $message = "SMTP settings updated successfully.";
            $messageType = "success";
        } else {
            // Capture any SQL error
            $message = "Error updating SMTP settings: " . $stmt->error;
            $messageType = "danger";
        }
        $stmt->close();
    } else {
        $message = "All fields are required.";
        $messageType = "danger";
    }

    // Refresh SMTP settings after update
    $result = $conn->query($query);
    if ($result === false) {
        die("Error re-fetching SMTP settings: " . $conn->error);
    }
    $smtpSettings = $result->fetch_assoc();
}
?>

<h2>SMTP Settings</h2>

<?php if (isset($message)): ?>
<div id="alertMessage" class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<form method="post" id="smtpForm">
    <div class="mb-3">
        <label for="smtp_host" class="form-label">SMTP Host</label>
        <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?php echo htmlspecialchars($smtpSettings['host'] ?? ''); ?>" required>
    </div>
    <div class="mb-3">
        <label for="smtp_port" class="form-label">SMTP Port</label>
        <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo htmlspecialchars($smtpSettings['port'] ?? ''); ?>" required>
    </div>
    <div class="mb-3">
        <label for="smtp_username" class="form-label">SMTP Username</label>
        <input type="text" class="form-control" id="smtp_username" name="smtp_username" value="<?php echo htmlspecialchars($smtpSettings['username'] ?? ''); ?>" required>
    </div>
    <div class="mb-3">
        <label for="smtp_password" class="form-label">SMTP Password</label>
        <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?php echo htmlspecialchars($smtpSettings['password'] ?? ''); ?>" required>
    </div>
    <div class="mb-3">
        <label for="from_email" class="form-label">From Email</label>
        <input type="email" class="form-control" id="from_email" name="from_email" value="<?php echo htmlspecialchars($smtpSettings['from_email'] ?? ''); ?>" required>
    </div>
    <div class="mb-3">
        <label for="from_name" class="form-label">From Name</label>
        <input type="text" class="form-control" id="from_name" name="from_name" value="<?php echo htmlspecialchars($smtpSettings['from_name'] ?? ''); ?>" required>
    </div>
    <button type="submit" name="update_smtp" class="btn btn-primary">Update SMTP Settings</button>
</form>
