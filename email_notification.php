<?php
define('INCLUDED', true);
ob_start();

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Ensure user is logged in and has proper permissions
requireLogin();
if (!isAdmin($_SESSION['user_id']) && !isTechnologyOfficer($_SESSION['user_position'])) {
    header("Location: dashboard.php");
    exit();
}

// Initialize $conn
$conn = null;

// Handle different submenus
$submenu = $_GET['submenu'] ?? 'officers_birthday';

include 'includes/header.php';

echo "<div class='container mt-4'>";

// Establish the database connection only when needed for the submenu
switch ($submenu) {
    case 'officers_birthday': // handling the 'Officer's Birthday' submenu
        $conn = getDbConnection();
        require 'includes/email_notification/officers_birthday.php';
        break;
    case 'birthday_history': // handling the 'Birthday Email Sent History' submenu
        $conn = getDbConnection();
        require 'includes/email_notification/birthday_history.php';
        break;
    case 'email_categories': // handling the 'Email Categories' submenu
        $conn = getDbConnection();
        require 'includes/email_notification/email_categories.php';
        break;
    case 'email_templates': // handling the 'Email Templates' submenu
        $conn = getDbConnection();
        require 'includes/email_notification/email_templates.php';
        break;
    case 'smtp_settings': // handling the 'Email SMTP Setting' submenu
        $conn = getDbConnection();
        require 'includes/email_notification/smtp_settings.php';
        break;
    case 'view_template': // handling the 'View Template' submenu
        $conn = getDbConnection();
        include 'includes/email_notification/view_template.php';
        break;
    case 'edit_template': // handling the 'Edit Template' submenu
        $conn = getDbConnection();
        include 'includes/email_notification/edit_template.php';
        break;
    case 'add_template':  // handling the '+ New Template' submenu
        $conn = getDbConnection();
        include 'includes/email_notification/add_template.php';
        break;
    default:
        $conn = getDbConnection();
        echo "Invalid submenu";
}

echo "</div>";

include 'includes/footer.php';

// Close the connection only if it's a valid mysqli object
if ($conn instanceof mysqli) {
    $conn->close();
}

ob_end_flush();
?>
