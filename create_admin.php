<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$username = 'admin';
$password = 'password';
$name = 'Prayag Verma';
$email = 'admin@example.com';
$role = 'admin';

$conn = getDbConnection();
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password, name, email, role) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $username, $hashedPassword, $name, $email, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully";
} else {
    echo "Error creating admin user: " . $conn->error;
}

$stmt->close();
$conn->close();