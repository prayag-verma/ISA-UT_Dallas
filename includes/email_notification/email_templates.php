<?php
if (!defined('INCLUDED')) {
    exit('Direct access is not allowed.');
}
global $conn;

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Fetch email templates with pagination
$query = "SELECT t.id, t.category_id, t.subject, t.body, t.created_by, t.created_at, t.updated_at, 
                 t.trigger_auto_email, c.name as category_name, u.first_name, u.last_name 
          FROM email_templates t
          JOIN email_categories c ON t.category_id = c.id
          JOIN users u ON t.created_by = u.id
          ORDER BY t.created_at DESC
          LIMIT ?, ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $offset, $perPage);
$stmt->execute();
$stmt->bind_result($id, $category_id, $subject, $body, $created_by, $created_at, $updated_at, $trigger_auto_email, $category_name, $first_name, $last_name);

$templates = array();
while ($stmt->fetch()) {
    $templates[] = array(
        'id' => $id,
        'category_id' => $category_id,
        'subject' => $subject,
        'body' => $body,
        'created_by' => $created_by,
        'created_at' => $created_at,
        'updated_at' => $updated_at,
        'trigger_auto_email' => $trigger_auto_email,
        'category_name' => $category_name,
        'first_name' => $first_name,
        'last_name' => $last_name
    );
}
$stmt->close();

// Get total number of templates
$totalQuery = "SELECT COUNT(*) as total FROM email_templates";
$totalResult = $conn->query($totalQuery);
$totalTemplates = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalTemplates / $perPage);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_templates'])) {
        $deleteIds = $_POST['delete_ids'] ?? [];
        if (!empty($deleteIds)) {
            $placeholders = implode(',', array_fill(0, count($deleteIds), '?'));
            $deleteQuery = "DELETE FROM email_templates WHERE id IN ($placeholders)";
            $stmt = $conn->prepare($deleteQuery);
            $types = str_repeat('i', count($deleteIds));
            $stmt->bind_param($types, ...$deleteIds);
            if ($stmt->execute()) {
                $message = "Selected templates have been deleted successfully.";
                $messageType = "success";
            } else {
                $message = "Error deleting templates: " . $conn->error;
                $messageType = "danger";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['toggle_auto_email'])) {
        $templateId = $_POST['template_id'];
        $newStatus = $_POST['status'];
        
        $updateQuery = "UPDATE email_templates SET trigger_auto_email = ? WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $newStatus, $templateId);
        if ($stmt->execute()) {
            if ($newStatus === 'Yes') {
                $message = "Auto emailing enabled";
                $messageType = "success";
            } else {
                $message = "Auto emailing disabled";
                $messageType = "warning";
            }
        } else {
            $message = "Error updating template status: " . $conn->error;
            $messageType = "danger";
        }
        $stmt->close();

        // If it's an AJAX request, send a JSON response
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['message' => $message, 'messageType' => $messageType]);
            exit;
        }
    }
}
?>

<h2>Email Templates</h2>

<?php if (isset($message)): ?>
<div id="alertMessage" class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="mb-3">
    <a href="email_notification.php?submenu=add_template" class="btn btn-primary">+ New Template</a>
    <button type="button" class="btn btn-danger" id="deleteTemplateBtn" disabled>Delete Template</button>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th><input type="checkbox" id="selectAll"></th>
            <th>Category</th>
            <th>Subject</th>
            <th>Created By</th>
            <th>Created Date</th>
            <th>Updated Date</th>
            <th>Auto Email</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($templates as $template): ?>
            <tr>
                <td><input type="checkbox" name="template_ids[]" value="<?php echo $template['id']; ?>"></td>
                <td><?php echo htmlspecialchars($template['category_name']); ?></td>
                <td><?php echo htmlspecialchars($template['subject']); ?></td>
                <td><?php echo htmlspecialchars($template['first_name'] . ' ' . $template['last_name']); ?></td>
                <td><?php echo htmlspecialchars($template['created_at']); ?></td>
                <td><?php echo htmlspecialchars($template['updated_at']); ?></td>
                <td>
                    <label class="switch">
                        <input type="checkbox" role="switch" class="auto-email-toggle" 
                               data-template-id="<?php echo $template['id']; ?>"
                               <?php echo $template['trigger_auto_email'] === 'Yes' ? 'checked' : ''; ?>>
                        <span class="slider round"></span>
                    </label>
                    <span class="status-text">
                        <?php echo $template['trigger_auto_email'] === 'Yes' ? 'On' : 'Off'; ?>
                    </span>
                </td>
                <td>
                    <a href="email_notification.php?submenu=view_template&id=<?php echo $template['id']; ?>" class="btn btn-sm btn-info">View</a>
                    <a href="email_notification.php?submenu=edit_template&id=<?php echo $template['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pagination -->
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?submenu=email_templates&page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo; Previous</span>
                </a>
            </li>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?submenu=email_templates&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?submenu=email_templates&page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">Next &raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<style>
/* Switch styles */
.switch {
    position: relative;
    display: inline-block;
    width: 50px; /* Adjust the width as needed */
    height: 24px; /* Reduce the height */
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 24px; /* Adjust for rounded corners */
}

.slider:before {
    position: absolute;
    content: "";
    height: 20px; /* Reduce the height */
    width: 20px; /* Width of the slider knob */
    left: 2px; /* Adjust for better alignment */
    bottom: 2px; /* Adjust for better alignment */
    background-color: white;
    transition: .4s;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px); /* Ensure it stays within the boundaries */
}

.slider.round {
    border-radius: 34px; /* Rounded corners for the switch */
}

.slider.round:before {
    border-radius: 50%;
}

/* Style for the "On" and "Off" status text */
.status-text {
    margin-left: 10px; /* Space between the switch and the text */
    font-weight: bold;
    display: inline-block;
    line-height: 24px; /* Match the height of the switch */
}

/* Ensures a better alignment for the switch */
.auto-email-toggle {
    vertical-align: middle;
}

/* Optional: You can add hover effects for better UX */
.switch:hover .slider {
    background-color: #b3c7e6; /* Lighten the background color on hover */
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle the select all checkbox
    const selectAllCheckbox = document.getElementById('selectAll');
    const templateCheckboxes = document.querySelectorAll('input[name="template_ids[]"]');
    
    selectAllCheckbox.addEventListener('change', function() {
        templateCheckboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        document.getElementById('deleteTemplateBtn').disabled = !selectAllCheckbox.checked;
    });

    // Enable/disable delete button based on checkbox selection
    templateCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const anyChecked = Array.from(templateCheckboxes).some(checkbox => checkbox.checked);
            document.getElementById('deleteTemplateBtn').disabled = !anyChecked;
            selectAllCheckbox.checked = Array.from(templateCheckboxes).every(checkbox => checkbox.checked);
        });
    });

    // Handle the toggle switch for auto email
    const toggleSwitches = document.querySelectorAll('.auto-email-toggle');
    toggleSwitches.forEach(switchElement => {
        switchElement.addEventListener('change', function() {
            const templateId = this.getAttribute('data-template-id');
            const newStatus = this.checked ? 'Yes' : 'No';
            
            // Update the status text immediately
            const statusTextElement = this.closest('td').querySelector('.status-text');
            statusTextElement.textContent = this.checked ? 'On' : 'Off';

            // Perform AJAX request to update the status
            fetch('email_notification.php?submenu=email_templates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest' // To indicate AJAX request
                },
                body: new URLSearchParams({
                    'toggle_auto_email': true,
                    'template_id': templateId,
                    'status': newStatus
                })
            })
            .then(response => response.json())
            .then(data => {
                // Handle response data
                const alertMessage = document.createElement('div');
                alertMessage.className = `alert alert-${data.messageType} alert-dismissible fade show`;
                alertMessage.role = "alert";
                alertMessage.innerHTML = data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                document.body.insertBefore(alertMessage, document.body.firstChild);
            })
            .catch(error => {
                console.error('Error updating auto email status:', error);
            });
        });
    });

    // Handle delete template action
    const deleteTemplateBtn = document.getElementById('deleteTemplateBtn');
    deleteTemplateBtn.addEventListener('click', function() {
        const selectedTemplates = Array.from(templateCheckboxes)
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);

        if (selectedTemplates.length > 0) {
            const confirmDelete = confirm("Are you sure you want to delete the selected templates?");
            if (confirmDelete) {
                const formData = new URLSearchParams();
                formData.append('delete_templates', true);
                formData.append('delete_ids[]', selectedTemplates);

                fetch('email_notification.php?submenu=email_templates', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // To indicate AJAX request
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Handle response data
                    const alertMessage = document.createElement('div');
                    alertMessage.className = `alert alert-${data.messageType} alert-dismissible fade show`;
                    alertMessage.role = "alert";
                    alertMessage.innerHTML = data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                    document.body.insertBefore(alertMessage, document.body.firstChild);

                    // Reload the page immediately after the message is displayed
                    location.reload(); // Immediately reloads the page
                })
                .catch(error => {
                    console.error('Error deleting templates:', error);
                });
            }
        } else {
            alert("Please select at least one template to delete.");
        }
    });
});
</script>