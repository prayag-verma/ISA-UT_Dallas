<div class="d-flex justify-content-between align-items-center mb-3">
    <p>Active Users: <?php echo $totalActive; ?></p>
    <p>Retired Users: <?php echo $totalRetired; ?></p>
    <div>
        <button type="submit" name="disable_users" form="userControlForm" class="btn btn-danger">Disable User Login</button>
        <button type="submit" name="enable_users" form="userControlForm" class="btn btn-success">Enable User Login</button>
    </div>
</div>

<form id="userControlForm" method="post">
    <table class="table table-striped">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><input type="checkbox" name="user_ids[]" value="<?php echo htmlspecialchars($user['id']); ?>" class="user-checkbox"></td>
                    <td><?php echo htmlspecialchars(ucwords($user['first_name'])); ?></td>
                    <td><?php echo htmlspecialchars(ucwords($user['last_name'])); ?></td>
                    <td><?php echo htmlspecialchars(strtolower($user['email'])); ?></td>
                    <td><?php echo htmlspecialchars(ucwords($user['position'])); ?></td>
                    <td>
                        <span style="color: <?php echo ($user['status'] === 'active') ? 'green' : (($user['status'] === 'disabled') ? 'red' : 'black'); ?>;">
                            <?php echo htmlspecialchars(ucwords($user['status'])); ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="isa_settings.php?tab=user_control&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.getElementById('selectAll');
    const userCheckboxes = document.getElementsByClassName('user-checkbox');

    selectAll.addEventListener('change', function() {
        for (let checkbox of userCheckboxes) {
            checkbox.checked = this.checked;
            updateLocalStorage(checkbox);
        }
    });

    for (let checkbox of userCheckboxes) {
        checkbox.addEventListener('change', function() {
            updateLocalStorage(this);
        });

        // Check local storage on page load
        const isChecked = localStorage.getItem('userCheckbox_' + checkbox.value) === 'true';
        checkbox.checked = isChecked;
    }

    function updateLocalStorage(checkbox) {
        localStorage.setItem('userCheckbox_' + checkbox.value, checkbox.checked);
    }

    // Update "Select All" checkbox state
    function updateSelectAll() {
        selectAll.checked = Array.from(userCheckboxes).every(cb => cb.checked);
    }

    // Call this function initially and whenever a checkbox changes
    updateSelectAll();
    for (let checkbox of userCheckboxes) {
        checkbox.addEventListener('change', updateSelectAll);
    }
});
</script>
