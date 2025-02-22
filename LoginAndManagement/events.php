<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Check if user is admin or technology officer and active
if (!isAdminOrTechOfficer() && !isTechnologyOfficer($_SESSION['user_position'])) {
    header("Location: dashboard.php");
    exit();
}

$action = $_GET['action'] ?? 'list';

// Handle actions that might result in redirection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($action) {
        case 'add':
            handleAddEvent();
            break;
        case 'edit':
            handleEditEvent();
            break;
        case 'delete':
            handleDeleteEvent();
            break;
    }
}

$pageTitle = 'Manage Events';
include 'includes/header.php';

switch ($action) {
    case 'add':
        include 'includes/add_event_form.php';
        break;
    case 'edit':
        $event = getEventDetails();
        include 'includes/edit_event_form.php';
        break;
    default:
        $events = getAllEvents();
        include 'includes/events_list.php';
        break;
}

include 'includes/footer.php';

function handleAddEvent() {
    $name = sanitizeInput($_POST['name']);
    $category = sanitizeInput($_POST['category']);
    $description = sanitizeInput($_POST['description']);
    $date = sanitizeInput($_POST['date']);

    $imagePath = uploadAndCompressImage($_FILES['image'], 'uploads/events/');

    $conn = getDbConnection();
    $stmt = $conn->prepare("INSERT INTO events (name, category, image_path, description, date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $category, $imagePath, $description, $date);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $_SESSION['success_message'] = "Event added successfully!";
    header("Location: events.php");
    exit();
}

function handleEditEvent() {
    $id = intval($_POST['id']);
    $name = sanitizeInput($_POST['name']);
    $category = sanitizeInput($_POST['category']);
    $description = sanitizeInput($_POST['description']);
    $date = sanitizeInput($_POST['date']);

    $conn = getDbConnection();
    $stmt = $conn->prepare("UPDATE events SET name = ?, category = ?, description = ?, date = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $name, $category, $description, $date, $id);
    $stmt->execute();

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imagePath = uploadAndCompressImage($_FILES['image'], 'uploads/events/');
        $stmt = $conn->prepare("UPDATE events SET image_path = ? WHERE id = ?");
        $stmt->bind_param("si", $imagePath, $id);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    $_SESSION['success_message'] = "Event updated successfully!";
    header("Location: events.php");
    exit();
}

function handleDeleteEvent() {
    $id = intval($_POST['id']);
    $conn = getDbConnection();
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    $_SESSION['success_message'] = "Event deleted successfully!";
    header("Location: events.php");
    exit();
}

function getEventDetails() {
    $id = intval($_GET['id']);
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    
    // Bind result variables
    $stmt->bind_result($id, $name, $category, $image_path, $description, $date);
    
    // Fetch values
    if ($stmt->fetch()) {
        $event = array(
            'id' => $id,
            'name' => $name,
            'category' => $category,
            'image_path' => $image_path,
            'description' => $description,
            'date' => $date
        );
    } else {
        $event = null;
    }
    
    $stmt->close();
    $conn->close();
    return $event;
}

function getAllEvents() {
    $conn = getDbConnection();
    $result = $conn->query("SELECT * FROM events ORDER BY date DESC");
    $events = array();
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
    $conn->close();
    return $events;
}

function uploadAndCompressImage($file, $uploadDir) {
    $targetDir = $uploadDir;
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array($fileType, $allowTypes)) {
        // Upload file to server
        if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
            // Compress image
            $info = getimagesize($targetFilePath);
            if ($info['mime'] == 'image/jpeg') 
                $image = imagecreatefromjpeg($targetFilePath);
            elseif ($info['mime'] == 'image/gif') 
                $image = imagecreatefromgif($targetFilePath);
            elseif ($info['mime'] == 'image/png') 
                $image = imagecreatefrompng($targetFilePath);
            
            imagejpeg($image, $targetFilePath, 60);
            
            return $targetFilePath;
        }
    }
    return false;
}
?>