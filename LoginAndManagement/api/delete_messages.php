<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

try {
    requireLogin();

    // Check if user has permission to delete messages
    if (!isAdmin($_SESSION['user_id']) && !$_SESSION['can_delete_messages']) {
        throw new Exception('You do not have permission to delete messages.');
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['ids']) && is_array($input['ids'])) {
        $messageIds = array_map('intval', $input['ids']);
        
        if (empty($messageIds)) {
            throw new Exception('No valid message IDs provided');
        }

        $conn = getDbConnection();
        
        // Use IN clause with imploded array
        $idList = implode(',', $messageIds);
        $deleteQuery = "DELETE FROM messages WHERE id IN ($idList)";
        
        $result = $conn->query($deleteQuery);
        
        if ($result === false) {
            throw new Exception("Delete failed: " . $conn->error);
        }

        $deletedCount = $conn->affected_rows;
        $conn->close();

        echo json_encode(['status' => 'success', 'message' => "$deletedCount message(s) deleted successfully"]);
    } else {
        throw new Exception('Invalid request: Incorrect method or missing/invalid ids');
    }
} catch (Exception $e) {
    error_log("Error in delete_messages.php: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}