<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
requireAdmin();

$pageTitle = 'Update Officer Details';
include 'includes/header.php';

$conn = getDbConnection();

// Fetch necessary data for dropdowns
$degrees = getDegrees($conn);
$positions = getPositions($conn);
$roles = getRoles($conn);

// Fetch all officers for dropdown
$allOfficers = getAllOfficers($conn);

// Fetch unique majors from users table
$majorsResult = $conn->query("SELECT DISTINCT major FROM users WHERE major IS NOT NULL AND major != '' ORDER BY major");
$majors = [];
while ($row = $majorsResult->fetch_assoc()) {
    $majors[] = $row['major'];
}

// Fetch all unique roles from the users table
$rolesResult = $conn->query("SELECT DISTINCT role FROM users ORDER BY role");
$allRoles = [];
while ($row = $rolesResult->fetch_assoc()) {
    $allRoles[] = $row['role'];
}

$updateMessage = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_officer'])) {
        $result = updateOfficer($conn, $_POST);
        $updateMessage = $result ? 'Officer details updated successfully.' : 'Failed to update officer details.';
        
        // Refresh the officer data after update
        $allOfficers = getAllOfficers($conn);
    }
}

$conn->close();
?>

<div class="container mt-4">
    <h2>Update Officer Details</h2>

    <?php if (!empty($updateMessage)): ?>
        <div class="alert <?php echo strpos($updateMessage, 'successfully') !== false ? 'alert-success' : 'alert-danger'; ?>">
            <?php echo $updateMessage; ?>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <select id="officer-select" class="form-select mb-3">
            <option value="">Select an officer</option>
            <?php foreach ($allOfficers as $officer): ?>
                <option value="<?php echo $officer['id']; ?>"><?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <form id="update-officer-form" method="post" style="display: none;">
        <input type="hidden" id="officer_id" name="officer_id">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
        </div>
        <div class="row"> 
            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="dob" class="form-label">Date of Birth</label>
                <input type="date" class="form-control" id="dob" name="dob">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="mobile" class="form-label">Mobile</label>
                <input type="tel" class="form-control" id="mobile" name="mobile">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="degree" class="form-label">Degree</label>
                <select class="form-select" id="degree" name="degree">
                    <?php foreach ($degrees as $degree): ?>
                        <option value="<?php echo htmlspecialchars($degree['name']); ?>"><?php echo htmlspecialchars($degree['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="major" class="form-label">Major</label>
                <select class="form-select" id="major" name="major">
                    <?php foreach ($majors as $major): ?>
                        <option value="<?php echo htmlspecialchars($major); ?>"><?php echo htmlspecialchars($major); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="joining_date" class="form-label" style="color: green;">ISA Joining Date</label>
                <input type="date" class="form-control" id="joining_date" name="joining_date">
            </div>
            <div class="col-md-6 mb-3">
                <label for="expected_grad_date" class="form-label" style="color: red;">Expected Graduation Date</label>
                <input type="date" class="form-control" id="expected_grad_date" name="expected_grad_date">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="officer_ranking" class="form-label">Total Points</label>
                <input type="number" class="form-control" id="officer_ranking" name="officer_ranking">
            </div>
            <div class="col-md-6 mb-3">
                <label for="position" class="form-label">Position</label>
                <select class="form-select" id="position" name="position">
                    <?php foreach ($positions as $position): ?>
                        <option value="<?php echo htmlspecialchars($position['name']); ?>"><?php echo htmlspecialchars($position['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role">
                    <?php foreach ($allRoles as $role): ?>
                        <option value="<?php echo htmlspecialchars($role); ?>">
                            <?php echo htmlspecialchars(ucfirst($role)); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="status" class="form-label">Status</label>
                <p id="status" class="form-control-plaintext"></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button type="submit" name="update_officer" class="btn btn-success">Update Officer</button>
            </div>
            <div class="col-md-6 text">
                <button type="button" id="reset-form" class="btn btn-danger">Select Another Officer</button>
            </div>
        </div>
    </form>

    <!-- Modal for disabled officer -->
    <div class="modal fade" id="disabledOfficerModal" tabindex="-1" aria-labelledby="disabledOfficerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="disabledOfficerModalLabel">Disabled Officer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    The selected officer is currently <i style="color: red;">disabled</i>. Please <i style="color: green;">activate</i> this officer first.<br><br> Go to  
                    <strong>ISA Settings</strong> → <strong>Enable/Disable Officer</strong>, and then retry.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const officerSelect = document.getElementById('officer-select');
    const updateForm = document.getElementById('update-officer-form');
    const resetButton = document.getElementById('reset-form');
    let allOfficers = <?php echo json_encode($allOfficers); ?>;

    function updateOfficerData(officerId) {
        fetch(`get_officer_details.php?id=${officerId}`)
            .then(response => response.json())
            .then(data => {
                const officerIndex = allOfficers.findIndex(officer => officer.id == officerId);
                if (officerIndex !== -1) {
                    allOfficers[officerIndex] = data;
                    if (data.status === 'disabled') {
                        const disabledOfficerModal = new bootstrap.Modal(document.getElementById('disabledOfficerModal'));
                        disabledOfficerModal.show();
                    }
                    populateForm(data);
                    updateForm.style.display = 'block';
                }
            })
            .catch(error => console.error('Error:', error));
    }

    if (officerSelect) {
        officerSelect.addEventListener('change', function() {
            const selectedId = this.value;
            if (selectedId) {
                updateOfficerData(selectedId);
            } else {
                updateForm.style.display = 'none';
                updateForm.reset();
            }
        });
    }

    if (resetButton) {
        resetButton.addEventListener('click', function() {
            officerSelect.value = '';
            updateForm.style.display = 'none';
            updateForm.reset();
        });
    }

    function populateForm(officer) {
        for (const key in officer) {
            const element = document.getElementById(key);
            if (element) {
                if (element.tagName === 'SELECT') {
                    const option = element.querySelector(`option[value="${officer[key]}"]`);
                    if (option) {
                        option.selected = true;
                    } else {
                        console.warn(`Option with value "${officer[key]}" not found for ${key}`);
                    }
                } else if (key === 'status') {
                    element.textContent = officer[key].charAt(0).toUpperCase() + officer[key].slice(1);
                    element.style.color = getStatusColor(officer[key]);
                } else {
                    element.value = officer[key];
                }
            } else {
                console.warn(`Element with id "${key}" not found`);
            }
        }
        document.getElementById('officer_id').value = officer.id;
    }

    function getStatusColor(status) {
        switch (status) {
            case 'active': return 'green';
            case 'disabled': return 'orange';
            case 'retired': return 'red';
            default: return 'black';
        }
    }

    // Trigger change event on page load if an officer is pre-selected
    if (officerSelect.value) {
        officerSelect.dispatchEvent(new Event('change'));
    }
});
</script>

<style>
.form-control, .form-select {
    max-width: 400px;
}
</style>

<?php include 'includes/footer.php'; ?>