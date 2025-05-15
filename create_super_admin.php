<?php
// create_super_admin.php

// Include the database connection file
require 'config.php'; // This will include the $pdo connection

// Define the Super Admin credentials
$email = 'newadmin1@example.com';
$password = 'NewSuperPass1234'; // Choose a secure password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

// Set the role for Super Admin (assumed role_id = 5)
$roleId = 5;

// Prepare the SQL query to insert the new Super Admin
$sql = "INSERT INTO users (email, password_hash, role_id, created_at) VALUES (?, ?, ?, NOW())"; // Correct column name: password_hash

try {
    // Prepare the statement
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(1, $email);
    $stmt->bindParam(2, $hashedPassword);
    $stmt->bindParam(3, $roleId);

    // Execute the query
    if ($stmt->execute()) {
        echo "New Super Admin created successfully.";
    } else {
        // If there's an error executing the query, show the error info
        echo "Error creating Super Admin: " . $stmt->errorInfo()[2];
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Close the statement and connection
$stmt->closeCursor();
?>
