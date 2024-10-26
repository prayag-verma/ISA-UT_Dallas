<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

requireAdmin();

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Invalid request: No officer ID provided');
    }

    $officerId = intval($_GET['id']);
    $conn = getDbConnection();
    
    $stmt = $conn->prepare("SELECT id, username, first_name, last_name, email, mobile, position, dob, degree, major, joining_date, expected_grad_date, officer_ranking, status, retirement_date, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $officerId);
    $stmt->execute();
    $stmt->bind_result($id, $username, $firstName, $lastName, $email, $mobile, $position, $dob, $degree, $major, $joiningDate, $expectedGradDate, $officerRanking, $status, $retirementDate, $role);
    
    if ($stmt->fetch()) {
        $officerData = [
            'id' => $id,
            'username' => $username,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'mobile' => $mobile,
            'position' => $position,
            'dob' => $dob,
            'degree' => $degree,
            'major' => $major,
            'joining_date' => $joiningDate,
            'expected_grad_date' => $expectedGradDate,
            'officer_ranking' => $officerRanking,
            'status' => $status,
            'retirement_date' => $retirementDate,
            'role' => $role
        ];

        echo json_encode($officerData);
    } else {
        throw new Exception('Officer not found');
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}