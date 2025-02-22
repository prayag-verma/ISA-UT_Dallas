<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
requireAdmin();

$pageTitle = 'User Management';
include 'includes/header.php';

$conn = getDbConnection();

function getActiveOfficers($conn, $page = 1, $perPage = 10) {
    $offset = ($page - 1) * $perPage;
    $stmt = $conn->prepare("
        SELECT id, username, first_name, last_name, email, mobile, position, degree, joining_date, expected_grad_date, officer_ranking, dob, major 
        FROM users 
        WHERE (role = 'admin' OR role = 'advisor' OR role = 'employee') AND status = 'active' 
        ORDER BY officer_ranking DESC, last_name DESC, first_name DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->bind_param("ii", $perPage, $offset);
    $stmt->execute();
    $stmt->bind_result($id, $username, $first_name, $last_name, $email, $mobile, $position, $degree, $joining_date, $expected_grad_date, $officer_ranking, $dob, $major);
    
    $officers = array();
    while ($stmt->fetch()) {
        $officers[] = array(
            'id' => $id,
            'username' => $username,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'mobile' => $mobile,
            'position' => $position,
            'degree' => $degree,
            'joining_date' => $joining_date,
            'expected_grad_date' => $expected_grad_date,
            'officer_ranking' => $officer_ranking,
            'dob' => $dob,
            'major' => $major
        );
    }
    $stmt->close();
    return $officers;
}

function getTotalActiveOfficers($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getTotalDisabledOfficers($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE status = 'disabled'");
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getTotalRetiredOfficers($conn) {
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'employee' AND status = 'retired'");
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'all-officers';

switch ($activeTab) {
    case 'all-officers':
        include 'includes/isa_officers.php';
        break;
    case 'add-officer':
        include 'includes/add_officer.php';
        break;
    case 'change-pwd':
        include 'includes/change_password.php';
        break;
    case 'retire-officer':
        include 'includes/retire_officer.php';
        break;
    case 'retired-officers':
        include 'includes/retired_officers.php';
        break;
    case 'officer-points':
        include 'includes/officer_points.php';
        break;
    default:
        include 'includes/isa_officers.php';
        break;
}

$conn->close();
include 'includes/footer.php';
?>