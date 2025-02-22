<?php
require_once 'includes/auth.php';

// Perform logout
logoutUser();

// Set a session message
$_SESSION['message'] = "Successfully logged out.";

// Redirect to login page
header("Location: login.php");
exit();