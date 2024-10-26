<?php
require_once 'db.php';

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}

function isAdmin($userId = null) {
    if ($userId === null) {
        $userId = $_SESSION['user_id'] ?? null;
    }
    if (!$userId) {
        return false;
    }
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($role);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
    return $role === 'admin';
}

function isAdminLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    return isAdmin($_SESSION['user_id']);
}

function getUserData($conn, $userId) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $meta = $stmt->result_metadata();
    $fields = array();
    $userData = array();
    while ($field = $meta->fetch_field()) {
        $fields[] = &$userData[$field->name];
    }
    call_user_func_array(array($stmt, 'bind_result'), $fields);
    $stmt->fetch();
    $stmt->close();
    return $userData;
}

if (!function_exists('getDegrees')) {
    function getDegrees($conn) {
        $result = $conn->query("SELECT * FROM degrees ORDER BY name");
        $degrees = [];
        while ($row = $result->fetch_assoc()) {
            $degrees[] = $row;
        }
        return $degrees;
    }
}
if (!function_exists('getPositions')) {
    function getPositions($conn) {
    $result = $conn->query("SELECT * FROM positions ORDER BY name");
    $positions = array();
    while ($row = $result->fetch_assoc()) {
        $positions[] = $row;
    }
    return $positions;

    }
}

if (!function_exists('getRoles')) {
    function getRoles($conn) {
        $result = $conn->query("SELECT name FROM roles ORDER BY name");
    $roles = array();
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['name'];
    }
    return $roles;
    }
}

function updateProfile($conn, $userId, $postData) {
    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, email = ?, mobile = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $postData['first_name'], $postData['last_name'], $postData['email'], $postData['mobile'], $userId);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}


function getAllOfficers($conn) {
    $result = $conn->query("SELECT id, username, first_name, last_name, email, mobile, position, dob, degree, major, joining_date, expected_grad_date, officer_ranking, role, status FROM users ORDER BY first_name, last_name");
    $officers = [];
    while ($row = $result->fetch_assoc()) {
        $officers[] = $row;
    }
    return $officers;
}
// get Retired Officers function
function getRetiredOfficers($conn) {
    $stmt = $conn->prepare("SELECT id, first_name, last_name, position, email, retirement_date FROM users WHERE status = 'retired'");
    $stmt->execute();
    $stmt->bind_result($id, $first_name, $last_name, $position, $email, $retirement_date);
    
    $officers = array();
    while ($stmt->fetch()) {
        $officers[] = array(
            'id' => $id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'position' => $position,
            'email' => $email,
            'retirement_date' => $retirement_date
        );
    }
    $stmt->close();
    return $officers;
}

function updateOfficer($conn, $postData) {
    // Start building the SQL query, excluding the 'status' field
    $sql = "UPDATE users SET 
        username = ?, 
        first_name = ?, 
        last_name = ?, 
        dob = ?, 
        email = ?, 
        mobile = ?, 
        degree = ?, 
        major = ?, 
        joining_date = ?, 
        expected_grad_date = ?, 
        officer_ranking = ?, 
        position = ?, 
        role = ?";

    // Create an array to hold parameters
    $params = [
        $postData['username'], 
        $postData['first_name'], 
        $postData['last_name'], 
        $postData['dob'], 
        $postData['email'], 
        $postData['mobile'], 
        $postData['degree'], 
        $postData['major'], 
        $postData['joining_date'], 
        $postData['expected_grad_date'], 
        $postData['officer_ranking'], 
        $postData['position'], 
        $postData['role']
    ];

    // Check if 'status' is set in the postData and not empty
    if (isset($postData['status']) && !empty($postData['status'])) {
        $sql .= ", status = ?";
        $params[] = $postData['status']; // Add 'status' to the parameters
    }

    // Complete the query with the WHERE clause
    $sql .= " WHERE id = ?";

    // Add 'officer_id' to the parameters
    $params[] = $postData['officer_id'];

    // Prepare the SQL statement
    $stmt = $conn->prepare($sql);

    // Build the type string for bind_param (e.g., "ssssssssssissi" for all the fields)
    $types = str_repeat('s', count($params) - 1) . 'i'; // 'i' for officer_id (the last parameter)

    // Use call_user_func_array to bind parameters dynamically
    $stmt->bind_param($types, ...$params);

    // Execute the query
    $result = $stmt->execute();

    // Close the statement
    $stmt->close();

    return $result;
}

function changePassword($conn, $userId, $postData) {
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($currentPassword);
    $stmt->fetch();
    $stmt->close();

    if (password_verify($postData['current_password'], $currentPassword)) {
        if ($postData['new_password'] === $postData['confirm_password']) {
            $newPasswordHash = password_hash($postData['new_password'], PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $newPasswordHash, $userId);
            $result = $stmt->execute();
            $stmt->close();
            return $result;
        }
    }
    return false;
}

function isAdminOrTechOfficer() {
    return (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') || 
           (isset($_SESSION['user_position']) && stripos($_SESSION['user_position'], 'technology officer') !== false);
}

function isActiveUser() {
    return isset($_SESSION['user_status']) && $_SESSION['user_status'] === 'active';
}
// Add any additional functions that were in your original file but not included above

?>