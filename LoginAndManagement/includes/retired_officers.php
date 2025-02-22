<?php
$retiredOfficers = getRetiredOfficers($conn);
$totalRetiredOfficers = getTotalRetiredOfficers($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reinstate_officer') {
    $stmt = $conn->prepare("UPDATE users SET status = 'active', retirement_date = NULL WHERE id = ?");
    $stmt->bind_param("i", $_POST['officer_id']);
    if ($stmt->execute()) {
        $successMessage = "Officer reinstated successfully";
        $totalActiveOfficers = getTotalActiveOfficers($conn);
        $totalRetiredOfficers = getTotalRetiredOfficers($conn);
        $retiredOfficers = getRetiredOfficers($conn); // Refresh the list
    } else {
        $errorMessage = "Error reinstating officer: " . $conn->error;
    }
    $stmt->close();
}
?>

<h3 class="mt-3">Retired Officers</h3>
<h6 class="mt-3"><span class="badge bg-danger"> Retired Officers : <?php echo $totalRetiredOfficers; ?> </span></h6>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>Position</th>
            <th>Email</th>
            <th>Retired Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($retiredOfficers as $officer): ?>
        <tr>
            <td><?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?></td>
            <td><?php echo htmlspecialchars($officer['position']); ?></td>
            <td><?php echo htmlspecialchars($officer['email']); ?></td>
            <td><?php echo htmlspecialchars($officer['retirement_date']); ?></td>
            <td>
                <button type="button" class="btn btn-info btn-sm view-details" data-officer-id="<?php echo $officer['id']; ?>">View Details</button>
                <form action="" method="post" class="d-inline">
                    <input type="hidden" name="action" value="reinstate_officer">
                    <input type="hidden" name="officer_id" value="<?php echo $officer['id']; ?>">
                    <button type="submit" class="btn btn-success btn-sm">Reinstate Officer</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal for Officer Details -->
<div class="modal fade" id="officerDetailsModal" tabindex="-1" aria-labelledby="officerDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary" id="officerDetailsModalLabel">Officer Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="officerDetailsContent">
                <!-- Officer details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.view-details').click(function() {
        var officerId = $(this).data('officer-id');
        $.ajax({
            url: 'get_officer_details.php',
            type: 'GET',
            data: { id: officerId },
            dataType: 'json',
            success: function(response) {
                var detailsHtml = "<h5><strong>Officer Name:</strong> " + response.first_name + " " + response.last_name + "</h5>" +
                    "<p><strong>Username:</strong> " + response.username + "</p>" +
                    "<p><strong>Email:</strong> " + response.email + "</p>" +
                    "<p><strong>Mobile:</strong> " + response.mobile + "</p>" +
                    "<p><strong>Position:</strong> " + response.position + "</p>" +
                    "<p><strong>Date of Birth:</strong> " + response.dob + "</p>" +
                    "<p><strong>Degree:</strong> " + response.degree + "</p>" +
                    "<p><strong>Major:</strong> " + response.major + "</p>" +
                    "<p><strong>ISA Joining Date:</strong> " + response.joining_date + "</p>" +
                    "<p><strong>Expected Graduation Date:</strong> " + response.expected_grad_date + "</p>" +
                    "<p><strong>Officer's Points:</strong> " + response.officer_ranking + "</p>" +
                    "<p><strong>Status:</strong> " + response.status + "</p>" +
                    "<p><strong>Retired Date:</strong> " + response.retirement_date + "</p>";

                $('#officerDetailsContent').html(detailsHtml);
                $('#officerDetailsModal').modal('show');
            },
            error: function() {
                alert('Error fetching officer details');
            }
        });
    });

    $('form[action=""]').submit(function(e) {
        if ($(this).find('input[name="action"]').val() === 'reinstate_officer') {
            if (!confirm('Are you sure you want to reinstate this officer?')) {
                e.preventDefault();
            }
        }
    });
});
</script>