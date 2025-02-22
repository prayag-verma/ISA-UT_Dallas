<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is admin or technology officer and active
if (!isAdminOrTechOfficer() && !isTechnologyOfficer($_SESSION['user_position'])) {
    header("Location: dashboard.php");
    exit();
}

$pageTitle = "ISA Main Page Management";
include 'includes/header.php';
?>

<h1>ISA Main Page Management</h1>
<nav class="nav flex-column">
    <a class="nav-link" href="events.php">Manage Events</a>
    <a class="nav-link" href="resources.php">Manage Resources</a>
    <a class="nav-link" href="gallery.php">Manage Gallery</a>
    <!-- Add more links for other sections as needed -->
</nav>

<?php include 'includes/footer.php'; ?>