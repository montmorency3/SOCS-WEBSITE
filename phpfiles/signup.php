<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database configuration
$host = "127.0.0.1";       
$dbname = "phpmyadmin"; 
$username = "root"; 
$password = ""; 

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input from the form
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $ID = $_POST['ID'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role = $_POST['role'];  // Capture role (student or professor)

    // Password validation
    $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($pattern, $password)) {
        echo "<script>alert('Password must have at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character.'); window.history.back();</script>";
        exit();
    }

    // Input validation
    if (!empty($firstName) && !empty($lastName) && !empty($ID) && !empty($password) && !empty($email) && !empty($role)) {
        
        // Convert first and last name to lowercase and construct the valid email prefix
        $expectedPrefix = strtolower($firstName . '.' . $lastName);

        // Validate email format based on the role
        if ($role == 'student') {
            // Student: email must match first and last name with @mail.mcgill.ca
            if (preg_match("/^" . preg_quote($expectedPrefix, '/') . "@mail\.mcgill\.ca$/", $email)) {
                // Sanitize inputs to prevent SQL injection
                $ID = mysqli_real_escape_string($conn, $ID);
                $email = mysqli_real_escape_string($conn, $email);
                
                // Hash the password for security
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Check if StudentID already exists
                $checkQuery = "SELECT * FROM StudentLogin WHERE StudentID = '$ID'";
                $result = $conn->query($checkQuery);

                if ($result->num_rows > 0) {
                    echo "StudentID already exists. Please choose a different ID.";
                } else {
                    // Insert student data into StudentLogin table
                    $query = "INSERT INTO StudentLogin (StudentID, Password, Email) VALUES ('$ID', '$hashedPassword', '$email')";

                    if ($conn->query($query) === TRUE) {
                        echo "Signup successful! Your details have been added to the StudentLogin table.";
                    } else {
                        echo "Error: " . $query . "<br>" . $conn->error;
                    }
                }
            } else {
                echo "Invalid email format for student. Use: firstname.lastname@mail.mcgill.ca.";
            }
        } elseif ($role == 'employee') {
            // Employee: email must match first and last name with @mcgill.ca
            if (preg_match("/^" . preg_quote($expectedPrefix, '/') . "@mcgill\.ca$/", $email)) {
                // Sanitize inputs to prevent SQL injection
                $ID = mysqli_real_escape_string($conn, $ID);
                $email = mysqli_real_escape_string($conn, $email);
                
                // Hash the password for security
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Check if EmployeeID already exists
                $checkQuery = "SELECT * FROM EmployeeLogin WHERE EmployeeID = '$ID'";
                $result = $conn->query($checkQuery);

                if ($result->num_rows > 0) {
                    echo "EmployeeID already exists. Please choose a different ID.";
                } else {
                    // Insert professor data into EmployeeLogin table
                    $query = "INSERT INTO EmployeeLogin (EmployeeID, Password, Email) VALUES ('$ID', '$hashedPassword', '$email')";

                    if ($conn->query($query) === TRUE) {
                        echo "Signup successful! Your details have been added to the EmployeeLogin table.";
                    } else {
                        echo "Error: " . $query . "<br>" . $conn->error;
                    }
                }
            } else {
                echo "Invalid email format for professor. Use: firstname.lastname@mcgill.ca.";
            }
        }
    } else {
        echo "All fields are required. Please fill in all the details.";
    }
}

// Close database connection
$conn->close();
?>