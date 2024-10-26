<?php
if (!defined('INCLUDED')) {
    exit('Direct access is not allowed.');
}
global $conn;

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/Exception.php';
require 'src/PHPMailer.php';
require 'src/SMTP.php';

// Fetch officer positions for the dropdown
$positionsQuery = "SELECT id, name FROM positions";
$positionsResult = $conn->query($positionsQuery);
$positions = [];

while ($row = $positionsResult->fetch_assoc()) {
    $positions[] = $row;
}

// Fetch email categories for the dropdown
$categoriesQuery = "SELECT id, name FROM email_categories";
$categoriesResult = $conn->query($categoriesQuery);
$categories = [];

while ($row = $categoriesResult->fetch_assoc()) {
    $categories[] = $row;
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $subject = $_POST['subject'];
    $body = $_POST['body'];
    $receiver_position_id = ($_POST['receiver_position_id'] === "all") ? null : $_POST['receiver_position_id'];
    $created_by = $_SESSION['user_id'];
    $trigger_auto_email = $_POST['trigger_auto_email'];
    $scheduled_email = ($trigger_auto_email === 'Yes') ? $_POST['scheduled_email'] : null;

    // Insert template into the database
    $insertQuery = "INSERT INTO email_templates (category_id, receiver_position_id, subject, body, created_by, trigger_auto_email, scheduled_email) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param('iississ', $category_id, $receiver_position_id, $subject, $body, $created_by, $trigger_auto_email, $scheduled_email);
    
    if ($stmt->execute()) {
        $message = "Template created successfully.";
        $messageType = "success";

        // Only send emails if trigger_auto_email is set to 'Yes'
        if ($trigger_auto_email === 'Yes') {
            sendEmails($conn, $receiver_position_id, $subject, $body, $scheduled_email);
        }
    } else {
        $message = "Error creating template: " . $conn->error;
        $messageType = "danger";
    }
    $stmt->close();
}

// Placeholder options for user to use in templates
$placeholders = [
    '{first_name}', '{last_name}', '{username}', '{password}', '{dob}', '{email}', '{mobile}', 
    '{degree}', '{major}', '{joining_date}', '{expected_grad_date}', '{officer_ranking}', 
    '{position}', '{profile_picture}', '{role}', '{status}', '{retirement_date}', '{isa_logo}'
];

?>
<h2>Add New Email Template</h2>

<?php if (isset($message)): ?>
    <div class="alert alert-<?php echo $messageType; ?>"><?php echo $message; ?></div>
<?php endif; ?>

<form method="POST" action="">
    <div class="mb-3">
        <label for="category_id" class="form-label">Email Category</label>
        <select name="category_id" id="category_id" class="form-control" required>
            <?php if (count($categories) > 0): ?>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="">No categories available</option>
            <?php endif; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="subject" class="form-label">Email Subject</label>
        <input type="text" name="subject" id="subject" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="body" class="form-label">Email Body</label>
        <textarea name="body" id="body" class="form-control" rows="6" required></textarea>
        <small class="text-muted">Available placeholders: <?php echo implode(', ', $placeholders); ?></small>
    </div>

    <div class="mb-3">
        <label for="receiver_position_id" class="form-label">Send To</label>
        <select name="receiver_position_id" id="receiver_position_id" class="form-control">
            <option value="all">All Officers</option>
            <?php foreach ($positions as $position): ?>
                <option value="<?php echo $position['id']; ?>"><?php echo htmlspecialchars($position['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="trigger_auto_email" class="form-label">Trigger Auto Email</label>
        <select name="trigger_auto_email" id="trigger_auto_email" class="form-control" onchange="toggleScheduledEmail(this.value);">
            <option value="No">Disable Auto Email</option>
            <option value="Yes">Send Auto Email</option>
        </select>
    </div>

    <div class="mb-3" id="scheduled_email_div" style="display: none;">
        <label for="scheduled_email" class="form-label">Scheduled Email</label>
        <select name="scheduled_email" class="form-control">
            <option value="Daily">Daily</option>
            <option value="Weekly">Weekly</option>
            <option value="Bi-Weekly">Bi-Weekly</option>
            <option value="Monthly">Monthly</option>
            <option value="Annually">Annually</option>
            <option value="Birthday Wishes">Birthday Wishes</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Create Template</button>
</form>

<?php
// Placeholder replacement function
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
        '{isa_logo}' => '<img src="uploads/logo/66fce15a4ed79_isa-fab.png">' // Example logo path
    ];

    foreach ($placeholders as $key => $value) {
        $body = str_replace($key, $value, $body);
    }

    return $body;
}

// Updated sendEmails function
function sendEmails($conn, $receiver_position_id, $subject, $templateBody, $scheduled_email) {
    // Fetch SMTP settings
    $smtpQuery = "SELECT * FROM smtp_settings WHERE id = 1";
    $smtpResult = $conn->query($smtpQuery);
    
    if ($smtpResult === false) {
        echo "Error fetching SMTP settings: " . $conn->error;
        return;
    }
    
    $smtpSettings = $smtpResult->fetch_assoc();

    // Check if SMTP settings were retrieved successfully
    if (!$smtpSettings) {
        echo "No SMTP settings found.";
        return;
    }

    // Determine which users to send emails to based on the scheduling option
    $usersQuery = "SELECT * FROM users WHERE status = 'active'";
    
    if ($receiver_position_id !== null) {
        $usersQuery .= " AND position = ?";
    }
    
    switch ($scheduled_email) {
        case 'Daily':
            // Send to all active users every day
            break;
        case 'Weekly':
            $usersQuery .= " AND DAYOFWEEK(CURDATE()) = 2";
            break;
        case 'Bi-Weekly':
            $usersQuery .= " AND WEEK(CURDATE()) % 2 = 0 AND DAYOFWEEK(CURDATE()) = 2";
            break;
        case 'Monthly':
            $usersQuery .= " AND DAY(CURDATE()) = 1";
            break;
        case 'Annually':
            $usersQuery .= " AND MONTH(CURDATE()) = 1 AND DAY(CURDATE()) = 1";
            break;
        case 'Birthday Wishes':
            $usersQuery .= " AND DATE_FORMAT(dob, '%m-%d') = DATE_FORMAT(CURDATE(), '%m-%d')";
            break;
    }

    $stmt = $conn->prepare($usersQuery);
    if ($stmt === false) {
        echo "Error preparing query: " . $conn->error;
        return;
    }

    if ($receiver_position_id !== null) {
        $stmt->bind_param("i", $receiver_position_id);
    }

    if (!$stmt->execute()) {
        echo "Error executing query: " . $stmt->error;
        return;
    }
    
    // Bind result variables
    $stmt->bind_result($id, $username, $password, $first_name, $last_name, $dob, $email, $mobile, $degree, $major, $joining_date, $expected_grad_date, $officer_ranking, $position, $profile_picture, $role, $status, $retirement_date, $can_read_messages, $can_export_messages, $can_delete_messages);

    // Send emails to the users
    while ($stmt->fetch()) {
        $user = array(
            'id' => $id,
            'username' => $username,
            'password' => $password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'dob' => $dob,
            'email' => $email,
            'mobile' => $mobile,
            'degree' => $degree,
            'major' => $major,
            'joining_date' => $joining_date,
            'expected_grad_date' => $expected_grad_date,
            'officer_ranking' => $officer_ranking,
            'position' => $position,
            'profile_picture' => $profile_picture,
            'role' => $role,
            'status' => $status,
            'retirement_date' => $retirement_date
        );

        $personalizedBody = replacePlaceholders($templateBody, $user);

        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $smtpSettings['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtpSettings['username'];
            $mail->Password   = $smtpSettings['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $smtpSettings['port'];

            // Recipients
            $mail->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $personalizedBody;

            $mail->send();
            echo "Message sent to " . $email . "<br>";
        } catch (Exception $e) {
            echo "Message could not be sent to " . $email . ". Mailer Error: {$mail->ErrorInfo}<br>";
        }
    }
    $stmt->close();
}
?>

<script>
function toggleScheduledEmail(value) {
    document.getElementById('scheduled_email_div').style.display = value === 'Yes' ? 'block' : 'none';
}
</script>