<?php
// Database configuration
$host = "localhost";    // Database server
$dbname = "your_database";  // Database name
$username = "your_username"; // Database username
$password = "your_password"; // Database password

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input from form fields
    $studentID = $_POST['studentID'];
    $studentPassword = $_POST['password'];

    // Prevent SQL Injection
    $studentID = mysqli_real_escape_string($conn, $studentID);
    $studentPassword = mysqli_real_escape_string($conn, $studentPassword);

    // Query to check credentials
    $query = "SELECT * FROM StudentLogin WHERE StudentID = '$studentID' AND Password = '$studentPassword'";
    $result = $conn->query($query);

    // Check if login is successful
    if ($result && $result->num_rows > 0) {
        // Fetch user info
        $row = $result->fetch_assoc();
        $email = $row['Email'];
        
        // Successful login
        echo "Login successful! Welcome Student with Email: " . htmlspecialchars($email);
    } else {
        // Login failed
        echo "Invalid Student ID or Password.";
    }
}

// Close database connection
$conn->close();
?>