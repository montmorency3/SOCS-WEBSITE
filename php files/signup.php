<?php
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
    $studentID = $_POST['studentID'];
    $studentPassword = $_POST['password'];
    $studentEmail = $_POST['email'];

    // Input validation (basic example)
    if (!empty($studentID) && !empty($studentPassword) && !empty($studentEmail)) {
        // Prevent SQL injection
        $studentID = mysqli_real_escape_string($conn, $studentID);
        $studentPassword = mysqli_real_escape_string($conn, $studentPassword);
        $studentEmail = mysqli_real_escape_string($conn, $studentEmail);

        // Check if StudentID already exists
        $checkQuery = "SELECT * FROM StudentLogin WHERE StudentID = '$studentID'";
        $result = $conn->query($checkQuery);

        if ($result->num_rows > 0) {
            echo "StudentID already exists. Please choose a different ID.";
        } else {
            // Insert data into StudentLogin table
            $query = "INSERT INTO StudentLogin (StudentID, Password, Email) VALUES ('$studentID', '$studentPassword', '$studentEmail')";

            if ($conn->query($query) === TRUE) {
                echo "Signup successful! Your details have been added to the StudentLogin table.";
            } else {
                echo "Error: " . $query . "<br>" . $conn->error;
            }
        }
    } else {
        echo "All fields are required. Please fill in all the details.";
    }
}

// Close database connection
$conn->close();
?>