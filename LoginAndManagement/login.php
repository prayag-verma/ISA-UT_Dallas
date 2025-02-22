<?php
require_once 'includes/auth.php';

// Redirect if already logged in
redirectIfLoggedIn();

$pageTitle = 'ISA Officers - Login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = authenticateUser($username, $password);
    if ($result['success']) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="assets/images/isa-fab.ico" type="image/x-icon">
</head>
<body>
    <div class="container mt-5">
        </span><img src="https://isautd.org/assets/img/logo.png" class="img-fluid mx-auto d-block" alt="Responsive image" style="height:10%; width: 10%;"></img>
        <h2>ISA - Officer Login</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['message'] . '</div>';
            unset($_SESSION['message']);
        }
        if (isset($error)) {
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
        ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<?php include 'includes/footer.php'; ?>