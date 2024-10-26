<?php
if (!defined('INCLUDED')) {
    exit('Direct access is not allowed.');
}

global $conn;

// Check if $conn is properly initialized
if (!isset($conn) || !($conn instanceof mysqli)) {
    error_log("Database connection not available.");
    die("Database connection not available.");
}

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Fetch email categories with pagination
$query = "SELECT id, name FROM email_categories ORDER BY name LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $perPage, $offset);
$stmt->execute();
$stmt->bind_result($id, $name);

// Fetch categories
$categories = [];
while ($stmt->fetch()) {
    $categories[] = [
        'id' => $id,
        'name' => $name
    ];
}
$stmt->close();

// Get total count for pagination
$countQuery = "SELECT COUNT(*) FROM email_categories";
$countStmt = $conn->prepare($countQuery);
$countStmt->execute();
$countStmt->bind_result($totalCategories);
$countStmt->fetch();
$countStmt->close();

$totalPages = ceil($totalCategories / $perPage);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_category'])) {
        $name = trim($_POST['category_name']);
        if (!empty($name)) {
            $stmt = $conn->prepare("INSERT INTO email_categories (name) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute()) {
                $message = "Category added successfully.";
                $messageType = "success";
            } else {
                $message = "Error adding category: " . $conn->error;
                $messageType = "danger";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['edit_category'])) {
        $id = $_POST['category_id'];
        $name = trim($_POST['category_name']);
        if (!empty($name)) {
            $stmt = $conn->prepare("UPDATE email_categories SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
            if ($stmt->execute()) {
                $message = "Category updated successfully.";
                $messageType = "success";
            } else {
                $message = "Error updating category: " . $conn->error;
                $messageType = "danger";
            }
            $stmt->close();
        }
    } elseif (isset($_POST['delete_categories'])) {
        $deleteIds = $_POST['delete_ids'] ?? [];
        if (!empty($deleteIds)) {
            $placeholders = implode(',', array_fill(0, count($deleteIds), '?'));
            $deleteQuery = "DELETE FROM email_categories WHERE id IN ($placeholders)";
            $stmt = $conn->prepare($deleteQuery);
            $types = str_repeat('i', count($deleteIds));
            $stmt->bind_param($types, ...$deleteIds);
            if ($stmt->execute()) {
                $message = "Selected categories have been deleted successfully.";
                $messageType = "success";
            } else {
                $message = "Error deleting categories: " . $conn->error;
                $messageType = "danger";
            }
            $stmt->close();
        }
    }
    
    // Refresh categories after modifications
    header("Location: email_notification.php?submenu=email_categories&page=$page");
    exit();
}
?>

<h2>Email Categories</h2>

<?php if (isset($message)): ?>
<div id="alertMessage" class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
    <?php echo $message; ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between mb-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
        + Category
    </button>
    <button type="button" class="btn btn-danger" id="deleteCategoryBtn" disabled>Delete Category</button>
</div>

<form id="categoryForm" method="post">
    <table class="table table-striped">
        <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th>Category Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $category): ?>
                <tr>
                    <td><input type="checkbox" name="delete_ids[]" value="<?php echo $category['id']; ?>"></td>
                    <td><?php echo htmlspecialchars($category['name']); ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary edit-category" data-id="<?php echo $category['id']; ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>">
                            Edit
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<!-- Pagination -->
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?submenu=email_categories&page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo; Previous</span>
                </a>
            </li>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?submenu=email_categories&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?submenu=email_categories&page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">Next &raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCategoryModalLabel">Add New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="category_name" name="category_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCategoryModalLabel">Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" id="edit_category_id" name="category_id">
                    <div class="mb-3">
                        <label for="edit_category_name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="edit_category_name" name="category_name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="edit_category" class="btn btn-primary">Update Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the selected categories?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="categoryForm" name="delete_categories" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('categoryForm');
    const selectAll = document.getElementById('selectAll');
    const checkboxes = form.querySelectorAll('input[type="checkbox"][name="delete_ids[]"]');
    const editButtons = document.querySelectorAll('.edit-category');
    const deleteCategoryBtn = document.getElementById('deleteCategoryBtn');
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCategoryModal'));

    function updateDeleteButtonState() {
        deleteCategoryBtn.disabled = ![...checkboxes].some(cb => cb.checked);
    }

    // Select all checkboxes
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateDeleteButtonState();
    });

    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateDeleteButtonState);
    });

    // Show delete confirmation modal
    deleteCategoryBtn.addEventListener('click', function() {
        deleteModal.show();
    });

    // Edit category button handler
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_category_name').value = name;
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        });
    });

    // Smooth scrolling for pagination
    document.querySelectorAll('.pagination a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            window.history.pushState({}, '', href);
            loadPage(href);
        });
    });

    function loadPage(url) {
        fetch(url)
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.querySelector('.container');
                document.querySelector('.container').innerHTML = newContent.innerHTML;
                window.scrollTo({top: 0, behavior: 'smooth'});
            });
    }
});
</script>

