<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';
requireLogin();

$currentUserId = $_SESSION['user_id'];
$isAdmin = isAdmin($currentUserId);

$pageTitle = 'Messages';
include 'includes/header.php';

$conn = getDbConnection();

// Check user permissions
$canReadMessages = $isAdmin || $_SESSION['can_read_messages'];
$canExportMessages = $isAdmin || $_SESSION['can_export_messages'];
$canDeleteMessages = $isAdmin || $_SESSION['can_delete_messages'];

if (!$canReadMessages) {
    header("Location: dashboard.php");
    exit();
}
?>

<div class="container mt-5">
    <h2>Messages</h2>
    <div class="mb-3">
        <?php if ($canExportMessages): ?>
            <button id="exportBtn" class="btn btn-primary mb-3">Export Selected</button>
        <?php endif; ?>
        
        <?php if ($canDeleteMessages): ?>
            <button id="deleteBtn" class="btn btn-danger mb-3">Delete Selected</button>
        <?php endif; ?>
    </div>
    <div class="mb-3">
        <input type="checkbox" id="selectAll"> 
        <label for="selectAll">Select All Messages</label>
        <span id="selectedCount">(0 selected)</span>
    </div>
    <table id="messagesTable" class="table table-bordered">
        <thead class="table-header">
            <tr>
                <th>Select</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Mobile Number</th>
                <th>Subject</th>
                <th>Enquiry Message</th>
                <th>Enquiry Date</th>
                <th>Enquiry Time</th>
            </tr>
        </thead>
        <tbody>
            <!-- Messages will be loaded here via AJAX -->
        </tbody>
    </table>
    <nav aria-label="Message navigation">
        <ul class="pagination" id="pagination">
            <!-- Pagination will be added here -->
        </ul>
    </nav>
</div>

<!-- Modal for viewing message -->
<div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Message Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <!-- Bootstrap's data-dismiss is critical -->
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- Message details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> <!-- data-dismiss should dismiss modal -->
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let allMessageIds = [];
let currentPage = 1;
let selectedMessageIds = new Set();
const messagesPerPage = 10;
let isAllSelected = false;

$(document).ready(function() {
    loadAllMessageIds();
    loadMessages(currentPage);

    // Close modal when 'X' button is clicked
    $('.modal .close').click(function() {
        $('#messageModal').modal('hide');
    });

    // Close modal when 'Close' button is clicked
    $('.modal .btn-secondary').click(function() {
        $('#messageModal').modal('hide');
    });

    $('#selectAll').change(function() {
        isAllSelected = this.checked;
        $('input[name="message_ids[]"]').prop('checked', isAllSelected);
        updateSelectedCount();
    });

    $('#messagesTable').on('change', 'input[name="message_ids[]"]', function() {
        const messageId = parseInt($(this).val());
        if (this.checked) {
            selectedMessageIds.add(messageId);
        } else {
            selectedMessageIds.delete(messageId);
        }
        updateSelectedCount();
    });

    $('#exportBtn').click(function() {
        exportMessages();
    });

    $('#deleteBtn').click(function() {
        deleteSelectedMessages();
    });
});

function loadAllMessageIds() {
    $.ajax({
        url: 'api/get_all_message_ids.php',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            allMessageIds = response.ids;
            updateSelectedCount();
        },
        error: function(xhr, status, error) {
            console.error('Error loading all message IDs:', error);
        }
    });
}

function loadMessages(page) {
    $.ajax({
        url: 'api/get_messages.php',
        type: 'GET',
        data: { page: page, per_page: messagesPerPage },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                displayMessages(response.messages);
                updatePagination(response.total_pages, page);
            } else {
                console.error('Error loading messages:', response.message);
                $('#messagesTable tbody').html('<tr><td colspan="9">Error loading messages: ' + response.message + '</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            $('#messagesTable tbody').html('<tr><td colspan="9">Error loading messages. Check console for details.</td></tr>');
        }
    });
}

function displayMessages(messages) {
    let tableBody = $('#messagesTable tbody');
    tableBody.empty();
    
    messages.forEach(function(message) {
        let row = `<tr>
            <td><input type="checkbox" name="message_ids[]" value="${message.id}" ${selectedMessageIds.has(parseInt(message.id)) ? 'checked' : ''}></td>
            <td>${message.FirstName}</td>
            <td>${message.LastName}</td>
            <td>${message.Email}</td>
            <td>${message.Phone}</td>
            <td>${message.Purpose}</td>
            <td><button class="btn btn-sm btn-info view-message" data-id="${message.id}">View Message</button></td>
            <td>${message.enquiry_date}</td>
            <td>${message.enquiry_time}</td>
        </tr>`;
        tableBody.append(row);
    });
    updateSelectedCount();

    // Add click event for view message buttons
    $('.view-message').click(function() {
        const messageId = $(this).data('id');
        viewMessage(messageId);
    });
}

function updatePagination(totalPages, currentPage) {
    let pagination = $('#pagination');
    pagination.empty();

    // Previous button
    pagination.append(`
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a>
        </li>
    `);

    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        pagination.append(`
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `);
    }

    // Next button
    pagination.append(`
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a>
        </li>
    `);

    // Add click event for pagination
    $('.page-link').click(function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page > 0 && page <= totalPages) {
            currentPage = page;
            loadMessages(currentPage);
        }
    });
}

function updateSelectedCount() {
    let selectedCount = isAllSelected ? allMessageIds.length : selectedMessageIds.size;
    $('#selectedCount').text(`(${selectedCount} selected)`);
    $('#selectAll').prop('checked', isAllSelected);
}

function getSelectedMessageIds() {
    return isAllSelected ? allMessageIds : Array.from(selectedMessageIds);
}

function exportMessages() {
    let selectedIds = getSelectedMessageIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one message to export.');
        return;
    }

    window.location.href = 'api/export_messages.php?ids=' + selectedIds.join(',');
}

function deleteSelectedMessages() {
    let selectedIds = getSelectedMessageIds();
    if (selectedIds.length === 0) {
        alert('Please select at least one message to delete.');
        return;
    }

    if (confirm('Are you sure you want to delete the selected messages?')) {
        $.ajax({
            url: 'api/delete_messages.php',
            type: 'POST',
            data: JSON.stringify({ ids: selectedIds }),
            contentType: 'application/json',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    alert(response.message);
                    loadMessages(currentPage);
                    loadAllMessageIds();
                    selectedMessageIds.clear();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', status, error);
                alert('An error occurred while deleting messages. Check the console for more details.');
            }
        });
    }
}

function viewMessage(messageId) {
    $.ajax({
        url: 'api/get_message_details.php',
        type: 'GET',
        data: { id: messageId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                $('#messageModalBody').html(response.message.Message);
                $('#messageModal').modal('show');
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', status, error);
            alert('An error occurred while fetching the message details. Check the console for more details.');
        }
    });
}
</script>
<?php include 'includes/footer.php'; ?>