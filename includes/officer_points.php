<?php
$allOfficers = getAllOfficers($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_points') {
    $officerId = $_POST['officer_id'];
    $pointChange = $_POST['point_change'];
    $changeType = $_POST['change_type'];

    $stmt = $conn->prepare("SELECT officer_ranking FROM users WHERE id = ?");
    $stmt->bind_param("i", $officerId);
    $stmt->execute();
    $stmt->bind_result($currentPoints);
    $stmt->fetch();
    $stmt->close();

    if ($changeType === 'add') {
        $newPoints = $currentPoints + $pointChange;
    } else {
        $newPoints = $currentPoints - $pointChange;
    }

    $stmt = $conn->prepare("UPDATE users SET officer_ranking = ? WHERE id = ?");
    $stmt->bind_param("ii", $newPoints, $officerId);
    if ($stmt->execute()) {
        $successMessage = "Points updated successfully";
        // Update the session if the current user's points were changed
        if ($officerId == $_SESSION['user_id']) {
            $_SESSION['total_point'] = $newPoints;
        }
    } else {
        $errorMessage = "Error updating points: " . $conn->error;
    }
    $stmt->close();
}
?>

<h3 class="mt-3">Officer's Point</h3>
<?php if (isset($successMessage)): ?>
    <div class="alert alert-success"><?php echo $successMessage; ?></div>
<?php endif; ?>
<?php if (isset($errorMessage)): ?>
    <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
<?php endif; ?>
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <form id="pointsForm" action="" method="post">
            <input type="hidden" name="action" value="update_points">
            <div class="mb-3">
                <label for="officer_id" class="form-label">Select Officer</label>
                <select class="form-select" id="officer_id" name="officer_id" required>
                    <option value="">Select Officer</option>
                    <?php foreach ($allOfficers as $officer): ?>
                        <option value="<?php echo $officer['id']; ?>" data-points="<?php echo $officer['officer_ranking']; ?>">
                            <?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label>Current Total Points: <span id="currentPoints" class="animated-number" style="color:green">0</span></label>
            </div>
            <div class="mb-3">
                <label for="change_type" class="form-label">Add/Minus Points</label>
                <select class="form-select" id="change_type" name="change_type" required>
                    <option value="add">Add Points</option>
                    <option value="minus">Minus Points</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="point_change" class="form-label">Number of Points</label>
                <input type="number" class="form-control" id="point_change" name="point_change" required min="1">
            </div>
            <button type="button" class="btn btn-primary" id="reviewButton">Review Changes</button>
        </form>
    </div>
</div>

<!-- Modal for Point Review -->
<div class="modal fade" id="pointReviewModal" tabindex="-1" aria-labelledby="pointReviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pointReviewModalLabel">Review Point Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="pointReviewMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitPointsButton">Submit</button>
            </div>
        </div>
    </div>
</div>

<style>
.animated-number {
    transition: all 0.5s ease-in-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const officerSelect = document.getElementById('officer_id');
    const changeTypeSelect = document.getElementById('change_type');
    const pointChangeInput = document.getElementById('point_change');
    const reviewButton = document.getElementById('reviewButton');
    const submitPointsButton = document.getElementById('submitPointsButton');
    const pointReviewModal = new bootstrap.Modal(document.getElementById('pointReviewModal'));
    const pointReviewMessage = document.getElementById('pointReviewMessage');
    const currentPointsDisplay = document.getElementById('currentPoints');

    officerSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const currentPoints = parseInt(selectedOption.getAttribute('data-points'));
        animateNumber(currentPointsDisplay, 0, currentPoints, 1000);
    });

    reviewButton.addEventListener('click', function() {
        const selectedOption = officerSelect.options[officerSelect.selectedIndex];
        const officerName = selectedOption.text;
        const currentPoints = parseInt(selectedOption.getAttribute('data-points'));
        const changeType = changeTypeSelect.value;
        const pointChange = parseInt(pointChangeInput.value);

        let newPoints;
        if (changeType === 'add') {
            newPoints = currentPoints + pointChange;
            pointReviewMessage.textContent = `${currentPoints} + ${pointChange} = ${newPoints} will be the updated Total Points for ${officerName}.`;
        } else {
            newPoints = currentPoints - pointChange;
            pointReviewMessage.textContent = `${currentPoints} - ${pointChange} = ${newPoints} will be the updated Total Points for ${officerName}.`;
        }

        pointReviewModal.show();
    });

    submitPointsButton.addEventListener('click', function() {
        document.getElementById('pointsForm').submit();
    });

    function animateNumber(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            element.innerHTML = Math.floor(progress * (end - start) + start);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }
});
</script>