function showNotification(message, type = 'success') {
    alert(message); // Replace with a more sophisticated notification system
}

function submitForm(formId, url, callback) {
    $(formId).submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let data = JSON.parse(response);
                showNotification(data.message, data.status);
                if (callback) callback(data);
            },
            error: function(xhr, status, error) {
                showNotification('An error occurred: ' + error, 'error');
            }
        });
    });
}

function loadMessages() {
    $.ajax({
        url: 'api/get_messages.php',
        type: 'GET',
        success: function(response) {
            let messages = JSON.parse(response);
            let tableBody = $('#messagesTable tbody');
            tableBody.empty();
            messages.forEach(function(message) {
                let row = `<tr>
                    <td><input type="checkbox" name="message_ids[]" value="${message.id}"></td>
                    <td>${message.subject}</td>
                    <td>${message.content}</td>
                    <td>${message.created_at}</td>`;
                if (isAdmin) {
                    row += `<td><button class="btn btn-danger btn-sm deleteMessage" data-id="${message.id}">Delete</button></td>`;
                }
                row += '</tr>';
                tableBody.append(row);
            });
        },
        error: function(xhr, status, error) {
            showNotification('Failed to load messages: ' + error, 'error');
        }
    });
}


// Continuing from where we left off in assets/js/main.js

function exportMessages() {
    let selectedIds = $('input[name="message_ids[]"]:checked').map(function() {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        showNotification('Please select at least one message to export', 'error');
        return;
    }

    $.ajax({
        url: 'api/export_messages.php',
        type: 'POST',
        data: { ids: selectedIds },
        success: function(response) {
            // Trigger file download
            let blob = new Blob([response], { type: 'text/csv' });
            let link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'messages_export.csv';
            link.click();
        },
        error: function(xhr, status, error) {
            showNotification('Export failed: ' + error, 'error');
        }
    });
}

$(document).ready(function() {
    // Login form submission
    submitForm('#loginForm', 'api/login.php', function(response) {
        if (response.status === 'success') {
            window.location.href = 'dashboard.php';
        }
    });






    // Register employee form submission
    submitForm('#registerForm', 'api/register_employee.php');

    // Change password form submission
    submitForm('#changePasswordForm', 'api/change_password.php');

    // Load employees for the change password form
    if ($('#employeeId').length) {
        $.ajax({
            url: 'api/get_employees.php',
            type: 'GET',
            success: function(response) {
                let employees = JSON.parse(response);
                let select = $('#employeeId');
                employees.forEach(function(employee) {
                    select.append($('<option>', {
                        value: employee.id,
                        text: employee.name
                    }));
                });
            },
            error: function(xhr, status, error) {
                showNotification('Failed to load employees: ' + error, 'error');
            }
        });
    }







    // Profile form submission
    submitForm('#profileForm', 'api/update_profile.php');

    // Load messages on the messages page
    if ($('#messagesTable').length) {
        loadMessages();
    }

    // Export button click handler
    $('#exportBtn').click(exportMessages);

    // Select all checkbox
    $('#selectAll').change(function() {
        $('input[name="message_ids[]"]').prop('checked', this.checked);
    });

    // Delete message (for admin)
    $(document).on('click', '.deleteMessage', function() {
        let messageId = $(this).data('id');
        if (confirm('Are you sure you want to delete this message?')) {
            $.ajax({
                url: 'api/delete_message.php',
                type: 'POST',
                data: { id: messageId },
                success: function(response) {
                    let data = JSON.parse(response);
                    showNotification(data.message, data.status);
                    if (data.status === 'success') {
                        loadMessages(); // Reload messages after deletion
                    }
                },
                error: function(xhr, status, error) {
                    showNotification('Failed to delete message: ' + error, 'error');
                }
            });
        }
    });
});