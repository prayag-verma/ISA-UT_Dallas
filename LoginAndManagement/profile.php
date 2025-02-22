<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
requireLogin();

$pageTitle = 'My Profile';
include 'includes/header.php';

$conn = getDbConnection();
$userId = $_SESSION['user_id'];

// Fetch user data
$userData = getUserData($conn, $userId);

$profileUpdateMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $result = updateProfile($conn, $userId, $_POST);
        $profileUpdateMessage = $result ? 'Profile updated successfully.' : 'Failed to update profile.';
        $userData = getUserData($conn, $userId); // Refresh user data
    }
}

$conn->close();
?>

<div class="container mt-4">
    <h2>My Profile</h2>

    <?php if (!empty($profileUpdateMessage)): ?>
        <div class="alert <?php echo strpos($profileUpdateMessage, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?> mt-3">
            <?php echo $profileUpdateMessage; ?>
        </div>
    <?php endif; ?>

    <form method="post" class="mt-4">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($userData['first_name']); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($userData['last_name']); ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label">Mobile</label>
                <input type="tel" class="form-control" id="mobile" name="mobile" value="<?php echo htmlspecialchars($userData['mobile']); ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="position" class="form-label">ISA Position</label>
                <input type="text" class="form-control" id="position" value="<?php echo htmlspecialchars($userData['position']); ?>" readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label for="joining_date" class="form-label">ISA Joining Date</label>
                <input type="date" class="form-control" id="joining_date" name="joining_date" value="<?php echo htmlspecialchars($userData['joining_date']); ?>" readonly>
            </div>
        </div>
        <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
    </form>
</div>

<style>
.form-control, .form-select {
    max-width: 400px;
}
</style>

<?php include 'includes/footer.php'; ?>