<?php
$positions = getPositions($conn);
$degrees = getDegrees($conn);
$roles = getRoles($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_officer') {
    if ($_POST['password'] === $_POST['confirm_password']) {
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];
        $firstName = sanitizeInput($_POST['first_name']);
        $lastName = sanitizeInput($_POST['last_name']);
        $email = sanitizeInput($_POST['email']);
        $mobile = sanitizeInput($_POST['mobile']);
        $position_id = intval($_POST['position']);
        $degree_id = intval($_POST['degree']);
        $joiningDate = sanitizeInput($_POST['joining_date']);
        $expectedGradDate = sanitizeInput($_POST['expected_grad_date']);
        $role = strtolower(sanitizeInput($_POST['role']));
        $major = sanitizeInput($_POST['major']);
        $officerRanking = intval($_POST['officer_ranking']);
        $dob = sanitizeInput($_POST['dob']);

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $positionStmt = $conn->prepare("SELECT name FROM positions WHERE id = ?");
        $positionStmt->bind_param("i", $position_id);
        $positionStmt->execute();
        $positionStmt->bind_result($position);
        $positionStmt->fetch();
        $positionStmt->close();

        $degreeStmt = $conn->prepare("SELECT name FROM degrees WHERE id = ?");
        $degreeStmt->bind_param("i", $degree_id);
        $degreeStmt->execute();
        $degreeStmt->bind_result($degree);
        $degreeStmt->fetch();
        $degreeStmt->close();

        $stmt = $conn->prepare("INSERT INTO users (username, password, first_name, last_name, email, mobile, position, degree, joining_date, expected_grad_date, role, major, officer_ranking, dob) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssssss", $username, $hashedPassword, $firstName, $lastName, $email, $mobile, $position, $degree, $joiningDate, $expectedGradDate, $role, $major, $officerRanking, $dob);

        if ($stmt->execute()) {
            $successMessage = "New officer added successfully";
        } else {
            $errorMessage = "Error adding new officer: " . $conn->error;
        }
        $stmt->close();
    } else {
        $errorMessage = "Passwords do not match";
    }
}
?>

<h3 class="mt-3">Add New Officer</h3>
<?php if (isset($successMessage)): ?>
    <div class="alert alert-success"><?php echo $successMessage; ?></div>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
<?php endif; ?>
<form action="" method="post">
    <input type="hidden" name="action" value="add_officer">
    <div class="mb-3">
        <label for="username" class="form-label">Create Officer's Username</label>
        <input type="text" class="form-control" id="username" name="username" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <div class="mb-3">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
    </div>
    <div class="mb-3">
        <label for="position" class="form-label">Assign Position</label>
        <select class="form-select" id="position" name="position" required>
            <option value="">Select Position</option>
            <?php foreach ($positions as $position): ?>
                <option value="<?php echo $position['id']; ?>"><?php echo htmlspecialchars($position['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="role" class="form-label">Role</label>
        <select class="form-select" id="role" name="role" required>
            <option value="">Select Role</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?php echo htmlspecialchars($role); ?>">
                    <?php echo htmlspecialchars(ucwords($role === 'employee' ? 'ISA Officer' : $role)); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" class="form-control" id="first_name" name="first_name" required>
    </div>
    <div class="mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" class="form-control" id="last_name" name="last_name" required>
    </div>
    <div class="mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <input type="date" class="form-control" id="dob" name="dob" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="mobile" class="form-label">Mobile</label>
        <input type="tel" class="form-control" id="mobile" name="mobile" required>
    </div>
    <div class="mb-3">
        <label for="degree" class="form-label">Degree</label>
        <select class="form-select" id="degree" name="degree" required>
            <option value="">Select Degree</option>
            <?php foreach ($degrees as $degree): ?>
                <option value="<?php echo $degree['id']; ?>"><?php echo htmlspecialchars($degree['name']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="major" class="form-label">Major</label>
        <input type="text" class="form-control" id="major" name="major" required>
    </div>
    <div class="mb-3">
        <label for="joining_date" class="form-label">ISA Joining Date</label>
        <input type="date" class="form-control" id="joining_date" name="joining_date" required>
    </div>
    <div class="mb-3">
        <label for="expected_grad_date" class="form-label">Expected Grad Date</label>
        <input type="date" class="form-control" id="expected_grad_date" name="expected_grad_date" required>
    </div>
    <div class="mb-3">
        <label for="officer_ranking" class="form-label">Add Initial Points</label>
        <input type="number" class="form-control" id="officer_ranking" name="officer_ranking" required>
    </div>
    <button type="submit" class="btn btn-primary">Add Officer</button>
</form>