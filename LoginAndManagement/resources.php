<?php
ob_start(); // Start output buffering

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isAdminOrTechOfficer() && !isTechnologyOfficer($_SESSION['user_position'])) {
    header("Location: dashboard.php");
    exit();
}

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $icon = sanitizeInput($_POST['icon']);
            $description = sanitizeInput($_POST['description']);
            $moreInfoText = sanitizeInput($_POST['more_info_text']);
            $moreInfoUrl = sanitizeInput($_POST['more_info_url']);

            $conn = getDbConnection();
            $stmt = $conn->prepare("INSERT INTO resources (icon, description, more_info_text, more_info_url) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $icon, $description, $moreInfoText, $moreInfoUrl);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            header("Location: resources.php");
            exit();
        }
        $pageTitle = 'Add New Resource';
        break;

    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $icon = sanitizeInput($_POST['icon']);
            $description = sanitizeInput($_POST['description']);
            $moreInfoText = sanitizeInput($_POST['more_info_text']);
            $moreInfoUrl = sanitizeInput($_POST['more_info_url']);

            $conn = getDbConnection();
            $stmt = $conn->prepare("UPDATE resources SET icon = ?, description = ?, more_info_text = ?, more_info_url = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $icon, $description, $moreInfoText, $moreInfoUrl, $id);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            header("Location: resources.php");
            exit();
        }
        $id = intval($_GET['id']);
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT * FROM resources WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $resource = $result->fetch_assoc();
        $stmt->close();
        $conn->close();
        $pageTitle = 'Edit Resource';
        break;

    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $conn = getDbConnection();
            $stmt = $conn->prepare("DELETE FROM resources WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            header("Location: resources.php");
            exit();
        }
        break;

    default:
        $conn = getDbConnection();
        $result = $conn->query("SELECT * FROM resources");
        $resources = array();
        while ($row = $result->fetch_assoc()) {
            $resources[] = $row;
        }
        $conn->close();
        $pageTitle = 'Manage Resources';
        break;
}

include 'includes/header.php';

switch ($action) {
    case 'add':
        include 'includes/add_resource_form.php';
        break;
    case 'edit':
        include 'includes/edit_resource_form.php';
        break;
    default:
        include 'includes/resources_list.php';
        break;
}

include 'includes/footer.php';

ob_end_flush(); // End output buffering and send output
?>