<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
requireLogin();

$pageTitle = 'Update Profile Picture';
include 'includes/header.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxFileSize = 2 * 1024 * 1024; // 2 MB

        if (!in_array($file['type'], $allowedTypes)) {
            $message = "Error: Only JPG, PNG, and GIF files are allowed.";
        } elseif ($file['size'] > $maxFileSize) {
            $message = "Error: File size must be less than 2 MB.";
        } else {
            $uploadDir = 'uploads/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $fileName = uniqid() . '_' . $file['name'];
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $conn = getDbConnection();
                $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
                $stmt->bind_param("si", $filePath, $_SESSION['user_id']);
                
                if ($stmt->execute()) {
                    $_SESSION['user_profile_picture'] = $filePath;
                    $message = "Profile picture updated successfully.";
                } else {
                    $message = "Error updating profile picture in the database.";
                }
                
                $stmt->close();
                $conn->close();
            } else {
                $message = "Error uploading file.";
            }
        }
    } else {
        $message = "Error: No file uploaded or upload error occurred.";
    }
}
?>

<div class="container mt-4">
    <h2>Update Profile Picture</h2>
    <?php if (!empty($message)): ?>
        <div class="alert <?php echo strpos($message, 'Error') === 0 ? 'alert-danger' : 'alert-success'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="profile_picture" class="form-label">Select a new profile picture (Max 2MB)</label>
            <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/jpeg,image/png,image/gif" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>