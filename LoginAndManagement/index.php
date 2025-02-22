<?php
require_once 'includes/auth.php';
require_once 'includes/functions.php';

// Check if the user is already logged in
if (isLoggedIn()) {
    // Redirect to the dashboard
    header("Location: dashboard.php");
    exit();
} else {
    // Redirect to the login page
    header("Location: login.php");
    exit();
}
