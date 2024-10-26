<?php
// Start the session if it hasn't been started yet
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once 'functions.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function requireAdmin() {
    if (!isLoggedIn() || !isAdmin($_SESSION['user_id'])) {
        
        http_response_code(403);
        // $_SESSION['flash_message'] = "You need to log in to access this page.";
        echo json_encode(['status' => 'error', 'message' => 'Admin access required']);
        header('Location: login.php');
        exit();
    }
}

function loginUser($userId) {
    $_SESSION['user_id'] = $userId;
    
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT first_name, last_name, position, joining_date, profile_picture, status, can_read_messages, can_export_messages, can_delete_messages, officer_ranking FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->bind_result($firstName, $lastName, $position, $joiningDate, $profilePicture, $status, $canReadMessages, $canExportMessages, $canDeleteMessages, $officerRanking);
    $stmt->fetch();
    
    $_SESSION['user_first_name'] = $firstName;
    $_SESSION['user_last_name'] = $lastName;
    $_SESSION['user_position'] = $position;
    $_SESSION['user_joining_date'] = $joiningDate;
    $_SESSION['user_profile_picture'] = $profilePicture;
    $_SESSION['user_status'] = $status;
    $_SESSION['can_read_messages'] = $canReadMessages;
    $_SESSION['can_export_messages'] = $canExportMessages;
    $_SESSION['can_delete_messages'] = $canDeleteMessages; // New line added
    $_SESSION['total_point'] = $officerRanking;
    
    $stmt->close();
    $conn->close();
}

function logoutUser() {
    // Unset all of the session variables
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session
    session_destroy();
}

function authenticateUser($username, $password) {
    $conn = getDbConnection();
    $stmt = $conn->prepare("SELECT id, password, status FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userId, $hashedPassword, $status);
    $stmt->fetch();
    $stmt->close();
    
    if ($userId && password_verify($password, $hashedPassword)) {
        if ($status === 'disabled') {
            return ['success' => false, 'message' => "Login Restricted. Contact ISA president to get activated."];
        } else {
            loginUser($userId);
            return ['success' => true];
        }
    }
    
    $conn->close();
    return ['success' => false, 'message' => "Invalid username or password"];
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: dashboard.php");
        exit();
    }
}

// Include the functions file to have access to isAdmin()
require_once 'functions.php';