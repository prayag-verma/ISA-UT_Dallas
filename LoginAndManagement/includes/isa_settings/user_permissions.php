<br></br>
<form id="userPermissionsForm" method="post">
    <div class="mb-3">
        <label for="userSelect" class="form-label">Select User</label>
        <select class="form-select" id="userSelect" name="user_id" required>
            <option value="">Select a user</option>
            <?php foreach ($nonAdminUsers as $user): ?>
                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="userDetails" style="display: none;">
        <h4>User Details</h4>
        <p><strong>Username:</strong> <span id="username"></span></p>
        <p><strong>First Name:</strong> <span id="firstName"></span></p>
        <p><strong>Last Name:</strong> <span id="lastName"></span></p>

        <h4>Permissions</h4>
        <div class="mb-3">
            <h5>Message Permissions</h5>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="canReadMessages" name="can_read_messages">
                <label class="form-check-label" for="canReadMessages">Can Read Messages</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="canExportMessages" name="can_export_messages">
                <label class="form-check-label" for="canExportMessages">Can Export Messages</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="canDeleteMessages" name="can_delete_messages">
                <label class="form-check-label" for="canDeleteMessages">Can Delete Messages</label>
            </div>
        </div>

        <!-- Add more permission categories here in the future -->

        <button type="submit" name="update_permissions" class="btn btn-primary">Update Permissions</button>
    </div>
</form>

<script>
document.getElementById('userSelect').addEventListener('change', function() {
    const userId = this.value;
    const userDetails = document.getElementById('userDetails');
    
    if (userId) {
        fetch(`api/get_user_details.php?id=${userId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('username').textContent = data.username;
                document.getElementById('firstName').textContent = data.first_name;
                document.getElementById('lastName').textContent = data.last_name;
                document.getElementById('canReadMessages').checked = data.can_read_messages == 1;
                document.getElementById('canExportMessages').checked = data.can_export_messages == 1;
                document.getElementById('canDeleteMessages').checked = data.can_delete_messages == 1;
                userDetails.style.display = 'block';
            })
            .catch(error => console.error('Error:', error));
    } else {
        userDetails.style.display = 'none';
    }
});
</script>