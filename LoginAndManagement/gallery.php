<?php
ob_start(); // Start output buffering

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isAdminOrTechOfficer() && !isTechnologyOfficer($_SESSION['user_position'])) {
    header("Location: dashboard.php");
    exit();
}

include 'includes/header.php';

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $year = sanitizeInput($_POST['year']);
            $semester = sanitizeInput($_POST['semester']);
            $order = intval($_POST['order']);

            $imagePath = uploadAndCompressImage($_FILES['image'], 'uploads/gallery/');

            if ($imagePath) {
                $conn = getDbConnection();
                $stmt = $conn->prepare("INSERT INTO gallery (year, semester, image_path, `order`) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $year, $semester, $imagePath, $order);
                $stmt->execute();
                $stmt->close();
                $conn->close();

                header("Location: gallery.php");
                exit();
            } else {
                $error = "Failed to upload image.";
            }
        }
        include 'includes/add_gallery_form.php';
        break;

    case 'edit':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $year = sanitizeInput($_POST['year']);
            $semester = sanitizeInput($_POST['semester']);
            $order = intval($_POST['order']);

            $conn = getDbConnection();
            $stmt = $conn->prepare("UPDATE gallery SET year = ?, semester = ?, `order` = ? WHERE id = ?");
            $stmt->bind_param("ssii", $year, $semester, $order, $id);
            $stmt->execute();

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $imagePath = uploadAndCompressImage($_FILES['image'], 'uploads/gallery/');
                if ($imagePath) {
                    $stmt = $conn->prepare("UPDATE gallery SET image_path = ? WHERE id = ?");
                    $stmt->bind_param("si", $imagePath, $id);
                    $stmt->execute();
                }
            }

            $stmt->close();
            $conn->close();

            header("Location: gallery.php");
            exit();
        }
        $id = intval($_GET['id']);
        $conn = getDbConnection();
        $stmt = $conn->prepare("SELECT id, year, semester, image_path, `order` FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->bind_result($id, $year, $semester, $image_path, $order);
        $stmt->fetch();
        $galleryItem = array(
            'id' => $id,
            'year' => $year,
            'semester' => $semester,
            'image_path' => $image_path,
            'order' => $order
        );
        $stmt->close();
        $conn->close();

        include 'includes/edit_gallery_form.php';
        break;

    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id']);
            $conn = getDbConnection();
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $conn->close();

            header("Location: gallery.php");
            exit();
        }
        break;

    default:
        $conn = getDbConnection();
        $result = $conn->query("SELECT id, year, semester, image_path, `order` FROM gallery ORDER BY year DESC, semester DESC, `order` ASC");
        $galleryItems = array();
        while ($row = $result->fetch_assoc()) {
            $galleryItems[] = $row;
        }
        $conn->close();

        include 'includes/gallery_list.php';
        break;
}

function uploadAndCompressImage($file, $uploadDir) {
    $targetDir = $uploadDir;
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileName = uniqid() . '_' . basename($file["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowTypes = array('jpg', 'png', 'jpeg', 'gif');
    if (in_array(strtolower($fileType), $allowTypes)) {
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
            else
                return false;
            
            imagejpeg($image, $targetFilePath, 60);
            imagedestroy($image);
            
            return $targetFilePath;
        }
    }
    return false;
}

include 'includes/footer.php';

ob_end_flush(); // End output buffering and send output
?>