<?php
if (!defined('INCLUDED')) {
    exit('Direct access is not allowed.');
}

// Ensure database connection exists and is valid
if (!isset($conn) || !($conn instanceof mysqli)) {
    error_log("Database connection not available.");
    die("Database connection not available");
}

// Fetch officers' birthdays
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$query = "SELECT id, first_name, last_name, dob, position, status, email, mobile, joining_date, expected_grad_date, major, 
          DATEDIFF(DATE_ADD(dob, INTERVAL YEAR(CURDATE())-YEAR(dob) YEAR), CURDATE()) AS days_until_birthday 
          FROM users 
          WHERE status = 'active' 
          ORDER BY days_until_birthday ASC 
          LIMIT ? OFFSET ?";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param('ii', $perPage, $offset);
    $stmt->execute();

    // Use bind_result() instead of get_result()
    $stmt->bind_result($id, $first_name, $last_name, $dob, $position, $status, $email, $mobile, $joining_date, $expected_grad_date, $major, $days_until_birthday);
    
    $officers = [];
    while ($stmt->fetch()) {
        $officers[] = [
            'id' => $id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'dob' => $dob,
            'position' => $position,
            'status' => $status,
            'email' => $email,
            'mobile' => $mobile,
            'joining_date' => $joining_date,
            'expected_grad_date' => $expected_grad_date,
            'major' => $major,
            'days_until_birthday' => $days_until_birthday
        ];
    }

    $stmt->close();
} else {
    error_log("Query preparation failed: " . $conn->error);
    die("Query preparation failed: " . $conn->error);
}

// Fetch total count for pagination
$countQuery = "SELECT COUNT(*) as total FROM users WHERE status = 'active'";
if ($countStmt = $conn->prepare($countQuery)) {
    $countStmt->execute();
    
    // Use bind_result() for the count query as well
    $countStmt->bind_result($total);
    $countStmt->fetch();
    
    $totalPages = ceil($total / $perPage);
    $countStmt->close();
} else {
    error_log("Count query preparation failed: " . $conn->error);
    die("Count query preparation failed: " . $conn->error);
}

// Count birthdays within a week and a month
$weekQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'active' AND DATEDIFF(DATE_ADD(dob, INTERVAL YEAR(CURDATE())-YEAR(dob) YEAR), CURDATE()) BETWEEN 0 AND 7";
$monthQuery = "SELECT COUNT(*) as count FROM users WHERE status = 'active' AND DATEDIFF(DATE_ADD(dob, INTERVAL YEAR(CURDATE())-YEAR(dob) YEAR), CURDATE()) BETWEEN 0 AND 30";

$weekResult = $conn->query($weekQuery)->fetch_assoc();
$monthResult = $conn->query($monthQuery)->fetch_assoc();

$birthdaysWithinWeek = $weekResult['count'];
$birthdaysWithinMonth = $monthResult['count'];

?>

<h2>Officers' Birthdays</h2>
<p>Birthdays within a week: <?php echo $birthdaysWithinWeek; ?></p>
<p>Birthdays within a month: <?php echo $birthdaysWithinMonth; ?></p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Name</th>
            <th>DOB</th>
            <th>Position</th>
            <th>Status</th>
            <th>Email</th>
            <th>Mobile</th>
            <th>Joining Date</th>
            <th>Expected Grad Date</th>
            <th>Major</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($officers as $officer): ?>
            <tr>
                <td><?php echo htmlspecialchars($officer['first_name'] . ' ' . $officer['last_name']); ?></td>
                <td style="color: <?php 
                    if ($officer['days_until_birthday'] <= 7) echo 'green';
                    elseif ($officer['days_until_birthday'] <= 30) echo 'orange';
                    else echo 'black';
                ?>">
                    <?php echo htmlspecialchars($officer['dob']); ?>
                </td>
                <td><?php echo htmlspecialchars($officer['position']); ?></td>
                <td><?php echo htmlspecialchars($officer['status']); ?></td>
                <td><?php echo htmlspecialchars($officer['email']); ?></td>
                <td><?php echo htmlspecialchars($officer['mobile']); ?></td>
                <td><?php echo htmlspecialchars($officer['joining_date']); ?></td>
                <td><?php echo htmlspecialchars($officer['expected_grad_date']); ?></td>
                <td><?php echo htmlspecialchars($officer['major']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Pagination -->
<nav aria-label="Page navigation">
    <ul class="pagination">
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?submenu=officers_birthday&page=<?php echo $page - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo; Previous</span>
                </a>
            </li>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                <a class="page-link" href="?submenu=officers_birthday&page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        
        <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?submenu=officers_birthday&page=<?php echo $page + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">Next &raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
