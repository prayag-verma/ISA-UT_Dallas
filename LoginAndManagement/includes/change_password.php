<?php
// Use the existing getAllOfficers function
$allOfficers = getAllOfficers($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    if ($_POST['new_password'] === $_POST['confirm_new_password']) {
        $hashedPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $_POST['officer_id']);
        if ($stmt->execute()) {
            $successMessage = "Password changed successfully";
        } else {
            $errorMessage = "Error changing password: " . $conn->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "Passwords do not match";
    }
}
?>

<h3 class="mt-3">Change Password</h3>
<?php if (isset($successMessage)): ?>
    <div class="alert alert-success"><?php echo $successMessage; ?></div>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
<?php endif; ?>
<form id="changePasswordForm" action="" method="post">
    <input type="hidden" name="action" value="change_password">
    <div class="mb-3">
        <label for="officer_id" class="form-label">Select Officer</label>
        <select class="form-select" id="officer_id" name="officer_id" required>
            <option value="">Select Officer</option>
            <?php foreach ($allOfficers as $officer): ?>
            <option value="<?php echo $officer['id']; ?>" data-status="<?php echo $officer['status']; ?>">
                <?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div id="passwordFields" style="display: none;">
        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="mb-3">
            <label for="confirm_new_password" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Change Password</button>
    </div>
</form>

<!-- Modal for Disabled/Retired User -->
<div class="modal fade" id="userStatusModal" tabindex="-1" aria-labelledby="userStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userStatusModalLabel">User Status Warning</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="userStatusMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="proceedButton">Proceed</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const officerSelect = document.getElementById('officer_id');
    const passwordFields = document.getElementById('passwordFields');
    const userStatusModal = new bootstrap.Modal(document.getElementById('userStatusModal'));
    const userStatusMessage = document.getElementById('userStatusMessage');
    const proceedButton = document.getElementById('proceedButton');

    officerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const status = selectedOption.getAttribute('data-status');

        if (status === 'disabled' || status === 'retired') {
            userStatusMessage.textContent = `The selected user is ${status}. Do you want to proceed with changing their password?`;
            userStatusModal.show();
        } else {
            showPasswordFields();
        }
    });

    proceedButton.addEventListener('click', function() {
        userStatusModal.hide();
        showPasswordFields();
    });

    function showPasswordFields() {
        passwordFields.style.display = 'block';
    }
});
</script>