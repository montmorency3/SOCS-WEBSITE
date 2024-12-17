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

    // Input validation
    if (!empty($firstName) && !empty($lastName) && !empty($ID) && !empty($password) && !empty($email) && !empty($role)) {
        
        // Convert first and last name to lowercase and construct the valid email prefix for students
        $expectedStudentPrefix = strtolower($firstName . '.' . $lastName);
        // Construct valid professor email prefix
        $expectedProfessorPrefix = strtolower($firstName . '.' . $lastName);

        // Validate email format based on the role
        if ($role == 'student') {
            // Student: email must match first and last name with @mail.mcgill.ca or @mcgill.ca domain
            if (preg_match("/^" . preg_quote($expectedStudentPrefix, '/') . "@mail\.mcgill\.ca|$/", $email)) {
                // Sanitize inputs to prevent SQL injection
                $ID = mysqli_real_escape_string($conn, $ID);
                $password = mysqli_real_escape_string($conn, $password);
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
                echo "Invalid email format for student. Your email must match your first and last name in lowercase, e.g., john.doe@mail.mcgill.ca.";
            }
        } elseif ($role == 'employee') {
            // Professor: email must match first and last name with @mcgill.ca domain
            if (preg_match("/^" . preg_quote($expectedProfessorPrefix, '/') . "@mcgill\.ca$/", $email)) {
                // Sanitize inputs to prevent SQL injection
                $ID = mysqli_real_escape_string($conn, $ID);
                $password = mysqli_real_escape_string($conn, $password);
                $email = mysqli_real_escape_string($conn, $email);
                
                // Hash the password for security
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                // Check if EmployeeID (for professors) already exists
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
                echo "Invalid email format for professor. Your email must match your first and last name in lowercase, e.g., john.doe@mcgill.ca.";
            }
        }
    } else {
        echo "All fields are required. Please fill in all the details.";
    }
}

// Close database connection
$conn->close();
?>