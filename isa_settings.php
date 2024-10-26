<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

requireAdmin();

// Use the global connection
$conn = $GLOBALS['conn'];
if (!$conn || $conn->connect_error) {
    die("Connection failed: " . ($conn ? $conn->connect_error : "Couldn't establish connection"));
}

$tab = $_GET['tab'] ?? 'upload_logo';
$pageTitle = 'ISA Setting ➺ ' . ucwords(str_replace('_', ' ', $tab));

$message = '';
$messageType = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($tab) {
        case 'upload_logo':
            handleLogoUpload($conn);
            break;
        case 'officer_position':
            handleOfficerPosition($conn);
            break;
        case 'degree':
            handleDegree($conn);
            break;
        case 'user_control':
            handleUserControl($conn);
            break;
    }
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;

// Fetch data
$positions = getPositions($conn, $page, $perPage);
$totalPositions = getTotalPositions($conn);
$totalPages = ceil($totalPositions / $perPage);

$degrees = getDegrees($conn, $page, $perPage);
$totalDegrees = getTotalDegrees($conn);
$totalPagesDegrees = ceil($totalDegrees / $perPage);

$users = getUsers($conn, $page, $perPage);
$totalUsers = getTotalUsers($conn);
$totalActive = getTotalActiveUsers($conn);
$totalRetired = getTotalRetiredUsers($conn);

include 'includes/header.php';

    function handleLogoUpload($conn) {
        // Implement logo upload logic here
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB

            if (!in_array($_FILES['logo']['type'], $allowedTypes)) {
                echo "<div class='alert alert-danger'>Invalid file type. Only JPG, PNG, and GIF are allowed.</div>";
                return;
            }

            if ($_FILES['logo']['size'] > $maxSize) {
                echo "<div class='alert alert-danger'>File is too large. Maximum size is 2MB.</div>";
                return;
            }

            $uploadDir = 'uploads/logo/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $fileName = uniqid() . '_' . $_FILES['logo']['name'];
            $filePath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES['logo']['tmp_name'], $filePath)) {
                $stmt = $conn->prepare("UPDATE site_settings SET logo_path = ? WHERE id = 1");
                $stmt->bind_param("s", $filePath);
                $stmt->execute();
                $stmt->close();
                echo "<div class='alert alert-success'>Logo uploaded successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to upload logo.</div>";
            }
        }
    }
    // Fetch all non-admin users
    function getNonAdminUsers($conn) {
        if ($conn = getDbConnection()) {
            if ($stmt = $conn->prepare("SELECT id, username, first_name, last_name FROM users WHERE role != 'admin' ORDER BY first_name, last_name")) {
                $stmt->execute();
                $stmt->bind_result($id, $username, $first_name, $last_name);
                $users = [];
                while ($stmt->fetch()) {
                    $users[] = [
                        'id' => $id,
                        'username' => $username,
                        'first_name' => $first_name,
                        'last_name' => $last_name
                    ];
                }
                $stmt->close();
                return $users;
            } else {
                error_log("Failed to prepare statement in getNonAdminUsers: " . $conn->error);
            }
        } else {
            error_log("Database connection not available in getNonAdminUsers.");
        }
        return [];
    }
        
        // function to get user permissions
        function getUserPermissions($conn, $userId) {
            if ($stmt = $conn->prepare("SELECT can_read_messages, can_export_messages, can_delete_messages FROM users WHERE id = ?")) {
                $stmt->bind_param("i", $userId);
                $stmt->execute();
                $result = $stmt->get_result();
                $permissions = $result->fetch_assoc();
                $stmt->close();
                return $permissions;
            } else {
                error_log("Failed to prepare statement in getUserPermissions: " . $conn->error);
                return [];
            }
        }
        
        // function to update user permissions
        function updateUserPermissions($conn, $userId, $permissions) {
            if ($stmt = $conn->prepare("UPDATE users SET can_read_messages = ?, can_export_messages = ?, can_delete_messages = ? WHERE id = ?")) {
                $stmt->bind_param("iiii", $permissions['can_read_messages'], $permissions['can_export_messages'], $permissions['can_delete_messages'], $userId);
                $result = $stmt->execute();
                $stmt->close();
                return $result;
            } else {
                error_log("Failed to prepare statement in updateUserPermissions: " . $conn->error);
                return false;
            }
        }
        
        $message = '';
        $messageType = '';
    function handleOfficerPosition($conn) {
        global $message, $messageType;
        if (isset($_POST['add_position'])) {
            $position = sanitizeInput($_POST['position']);
            $stmt = $conn->prepare("INSERT INTO positions (name) VALUES (?)");
            $stmt->bind_param("s", $position);
            if ($stmt->execute()) {
                $message = "New position '$position' has been added.";
                $messageType = 'success';
            } else {
                $message = "Failed to add position '$position'. Error: " . $stmt->error;
                $messageType = 'danger';
            }
            $stmt->close();
        } elseif (isset($_POST['delete_position'])) {
            $positionId = intval($_POST['position_id']);
            $stmt = $conn->prepare("DELETE FROM positions WHERE id = ?");
            $stmt->bind_param("i", $positionId);
            if ($stmt->execute()) {
                $message = "Position has been removed.";
                $messageType = 'success';
            } else {
                $message = "Failed to remove position. Error: " . $stmt->error;
                $messageType = 'danger';
            }
            $stmt->close();
        }
    }

    function getPositions($conn, $page, $perPage) {
        if ($stmt = $conn->prepare("SELECT id, name FROM positions ORDER BY name LIMIT ? OFFSET ?")) {
            $offset = ($page - 1) * $perPage;
            $stmt->bind_param("ii", $perPage, $offset);
            $stmt->execute();
            $stmt->bind_result($id, $name);
            $positions = array();
            while ($stmt->fetch()) {
                $positions[] = array('id' => $id, 'name' => $name);
            }
            $stmt->close();
            return $positions;
        } else {
            error_log("Failed to prepare statement in getPositions: " . $conn->error);
            return [];
        }
    }

    function getTotalPositions($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM positions");
        return $result->fetch_assoc()['count'];
    }
    function getTotalDegrees($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM degrees");
        return $result->fetch_assoc()['count'];
    }

    function handleDegree($conn) {
        if (isset($_POST['add_degree'])) {
            $degree = sanitizeInput($_POST['degree']);
            $stmt = $conn->prepare("INSERT INTO degrees (name) VALUES (?)");
            $stmt->bind_param("s", $degree);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>New degree '$degree' has been added.</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to add degree '$degree'. Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        } elseif (isset($_POST['delete_degree'])) {
            $degreeId = intval($_POST['degree_id']);
            $stmt = $conn->prepare("DELETE FROM degrees WHERE id = ?");
            $stmt->bind_param("i", $degreeId);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Degree has been removed.</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to remove degree. Error: " . $stmt->error . "</div>";
            }
            $stmt->close();
        }
    }

    function handleUserControl($conn) {
        if (isset($_POST['disable_users']) || isset($_POST['enable_users'])) {
            $userIds = $_POST['user_ids'] ?? [];
            $newStatus = isset($_POST['disable_users']) ? 'disabled' : 'active';
            if (!empty($userIds)) {
                $placeholders = implode(',', array_fill(0, count($userIds), '?'));
                $types = str_repeat('i', count($userIds));
                $stmt = $conn->prepare("UPDATE users SET status = ? WHERE id IN ($placeholders)");
                $stmt->bind_param("s" . $types, $newStatus, ...$userIds);
                if ($stmt->execute()) {
                    $action = $newStatus === 'disabled' ? 'disabled' : 'enabled';
                    echo "<div class='alert alert-success'>Selected users have been $action.</div>";
                } else {
                    echo "<div class='alert alert-danger'>Failed to update user status. Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
        }
    }

    function getDegrees($conn, $page, $perPage) {
        $offset = ($page - 1) * $perPage;
        $stmt = $conn->prepare("SELECT id, name FROM degrees ORDER BY name LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $perPage, $offset);
        $stmt->execute();
        $stmt->bind_result($id, $name);
        $degrees = array();
        while ($stmt->fetch()) {
            $degrees[] = array('id' => $id, 'name' => $name);
        }
        $stmt->close();
        return $degrees;
    }
    // Get user User Control Users
    function getUsers($conn, $page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    $stmt = $conn->prepare("
        SELECT u.id, u.first_name, u.last_name, u.email, u.status, p.name as position_name
        FROM users u
        LEFT JOIN positions p ON u.position = p.id
        ORDER BY u.id 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $perPage, $offset);
    $stmt->execute();
    $stmt->bind_result($id, $first_name, $last_name, $email, $status, $position_name);
    $users = array();
    while ($stmt->fetch()) {
        $users[] = array(
            'id' => $id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'status' => $status,
            'position' => $position_name // Changed from 'name' to 'position'
        );
    }
    $stmt->close();
    return $users;
}
    

    function getTotalUsers($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        return $result->fetch_assoc()['count'];
    }

    function getTotalActiveUsers($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
        return $result->fetch_assoc()['count'];
    }

    function getTotalRetiredUsers($conn) {
        $result = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'retired'");
        return $result->fetch_assoc()['count'];
    }
// $conn->close();
?>

<div class="container mt-4">
    <h4><?php echo $pageTitle; ?></h4>

    <?php
    switch ($tab) {
    case 'upload_logo':
        include 'includes/isa_settings/upload_logo.php';
        break;
    case 'officer_position':
        include 'includes/isa_settings/officer_position.php';
        break;
    case 'degree':
        include 'includes/isa_settings/degree.php';
        break;
    case 'user_control':
        include 'includes/isa_settings/user_control.php';
        break;
    case 'user_permissions':
    if (isAdmin($_SESSION['user_id'])) {
        $nonAdminUsers = getNonAdminUsers($conn);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_permissions'])) {
            $userId = $_POST['user_id'];
            $permissions = [
                'can_read_messages' => isset($_POST['can_read_messages']) ? 1 : 0,
                'can_export_messages' => isset($_POST['can_export_messages']) ? 1 : 0,
                'can_delete_messages' => isset($_POST['can_delete_messages']) ? 1 : 0,
            ];
            if (updateUserPermissions($conn, $userId, $permissions)) {
                $message = "Permissions updated successfully.";
                $messageType = 'success';
            } else {
                $message = "Failed to update permissions.";
                $messageType = 'danger';
            }
        }
        include 'includes/isa_settings/user_permissions.php';
    } else {
        echo "<div class='alert alert-danger'>You don't have permission to access this page.</div>";
    }
    break;
    }
    ?>
</div>
<!-- Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="messageModalLabel">Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="messageModalBody"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showMessage(message, type) {
    const modalBody = document.getElementById('messageModalBody');
    modalBody.textContent = message;
    modalBody.className = `alert alert-${type}`;
    const modal = new bootstrap.Modal(document.getElementById('messageModal'));
    modal.show();
}
</script>

<?php
if ($message) {
    echo "<script>showMessage(" . json_encode($message) . ", " . json_encode($messageType) . ");</script>";
}
?>
<?php include 'includes/footer.php'; ?>