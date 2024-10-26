<?php
$allOfficers = getAllOfficers($conn);
$totalActiveOfficers = getTotalActiveOfficers($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'retire_officer') {
    $stmt = $conn->prepare("UPDATE users SET status = 'retired', retirement_date = ? WHERE id = ?");
    $stmt->bind_param("si", $_POST['retirement_date'], $_POST['retire_officer_id']);
    if ($stmt->execute()) {
        $successMessage = "Officer retired successfully";
    } else {
        $errorMessage = "Error retiring officer: " . $conn->error;
    }
    $stmt->close();
}
?>

<h3 class="mt-3">Retire Officer Now</h3>
<h6 class="mt-3"><span class="badge bg-success"> Active Officers : <?php echo $totalActiveOfficers; ?> </span></h6>
<?php if (isset($successMessage)): ?>
    <div class="alert alert-success"><?php echo $successMessage; ?></div>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
<?php endif; ?>
<form action="" method="post">
    <input type="hidden" name="action" value="retire_officer">
    <div class="mb-3">
        <label for="retire_officer_id" class="form-label">Select Officer to Retire</label>
        <select class="form-select" id="retire_officer_id" name="retire_officer_id" required>
            <option value="">Select Officer</option>
            <?php foreach ($allOfficers as $officer): ?>
                <?php if ($officer['status'] !== 'retired'): ?>
                    <option value="<?php echo $officer['id']; ?>"><?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="retirement_date" class="form-label">Retirement Date</label>
        <input type="date" class="form-control" id="retirement_date" name="retirement_date" required>
    </div>
    <button type="submit" class="btn btn-danger">Retire This Officer</button>
</form>