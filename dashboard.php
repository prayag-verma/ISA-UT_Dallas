<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
requireLogin();

$pageTitle = 'Dashboard';
include 'includes/header.php';

$conn = getDbConnection();

$isAdmin = isAdmin($_SESSION['user_id']);

if ($isAdmin) {
    // Fetch officer counts
    $activeOfficers = getTotalOfficersByStatus($conn, 'active');
    $deactiveOfficers = getTotalOfficersByStatus($conn, 'disabled');
    $retiredOfficers = getTotalOfficersByStatus($conn, 'retired');
}

// Fetch message trends
$messageTrends = getMessageTrends($conn);

// Fetch officer points for donut chart
$officerPoints = getOfficerPoints($conn);
if (empty($officerPoints)) {
    error_log("No officer points data retrieved or an error occurred.");
    $showOfficerPointsChart = false;
} else {
    $showOfficerPointsChart = true;
}

$conn->close();

function getTotalOfficersByStatus($conn, $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE status = ?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return $count;
}

function getMessageTrends($conn) {
    $stmt = $conn->prepare("SELECT DATE(submission_date) as date, COUNT(*) as count FROM messages GROUP BY DATE(submission_date) ORDER BY date DESC");
    $stmt->execute();
    $stmt->bind_result($date, $count);
    $trends = array();
    while ($stmt->fetch()) {
        $trends[] = array(
            'date' => $date,
            'count' => $count
        );
    }
    $stmt->close();
    return array_reverse($trends);
}

function getOfficerPoints($conn) {
    $query = "SELECT u.first_name, u.last_name, p.name as position, u.officer_ranking 
              FROM users u 
              LEFT JOIN positions p ON u.position = p.name 
              WHERE u.status = 'active' 
              ORDER BY u.officer_ranking DESC";
    
    $stmt = $conn->prepare($query);
    
    if ($stmt === false) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        return array(); // Return an empty array if prepare fails
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        return array(); // Return an empty array if execute fails
    }
    
    $stmt->bind_result($firstName, $lastName, $position, $totalPoint);
    $officers = array();
    while ($stmt->fetch()) {
        $officers[] = array(
            'name' => $firstName . ' ' . $lastName,
            'position' => $position,
            'point' => $totalPoint
        );
    }
    $stmt->close();
    return $officers;
}
?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">User Profile</h5>
                    <?php if (!empty($_SESSION['user_profile_picture'])): ?>
                        <img src="<?php echo htmlspecialchars($_SESSION['user_profile_picture']); ?>" alt="Profile Picture" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                    <?php endif; ?>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($_SESSION['user_first_name'] . ' ' . $_SESSION['user_last_name']); ?></p>
                    <p><strong>Position at ISA:</strong> <?php echo htmlspecialchars($_SESSION['user_position']); ?></p>
                    <p><strong>ISA Joining Date:</strong> <?php echo htmlspecialchars($_SESSION['user_joining_date']); ?></p>
                    <p><strong>Total Points:</strong> <span class="text-success font-weight-bold"><?php echo htmlspecialchars($_SESSION['officer_ranking']);?></span></p>
                    
                    <a href="update_profile_picture.php" class="btn btn-primary">Change Profile Picture</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <?php if ($isAdmin): ?>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Active Officers</h5>
                            <p class="card-text display-4"><?php echo $activeOfficers; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-warning text-dark">
                        <div class="card-body">
                            <h5 class="card-title">Inactive Officers</h5>
                            <p class="card-text display-4"><?php echo $deactiveOfficers; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 class="card-title">Retired Officers</h5>
                            <p class="card-text display-4"><?php echo $retiredOfficers; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="row mt-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Message Trends</h5>
                            <canvas id="messageTrendsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Officer Points</h5>
                            <?php if ($showOfficerPointsChart): ?>
                                <canvas id="officerPointsChart"></canvas>
                            <?php else: ?>
                                <p>No data available or an error occurred.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Message Trends Chart
    var messageTrendsCtx = document.getElementById('messageTrendsChart').getContext('2d');
    var messageTrendsData = <?php echo json_encode($messageTrends); ?>;
    
    new Chart(messageTrendsCtx, {
        type: 'line',
        data: {
            labels: messageTrendsData.map(item => item.date),
            datasets: [{
                label: 'Messages',
                data: messageTrendsData.map(item => item.count),
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Messages'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });

    // Officer Points Chart
    <?php if ($showOfficerPointsChart): ?>
    var officerPointsCtx = document.getElementById('officerPointsChart').getContext('2d');
    var officerPointsData = <?php echo json_encode($officerPoints); ?>;
    
    new Chart(officerPointsCtx, {
        type: 'doughnut',
        data: {
            labels: officerPointsData.map(item => item.name),
            datasets: [{
                data: officerPointsData.map(item => item.point),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const officer = officerPointsData[context.dataIndex];
                            return [
                                officer.name,
                                `Total Points: ${officer.point}`,
                                `ISA Position: ${officer.position || 'N/A'}`
                            ];
                        }
                    }
                }
            }
        }
    });
    <?php endif; ?>
});
</script>

<?php include 'includes/footer.php'; ?>