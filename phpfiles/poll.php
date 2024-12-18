<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$host = "localhost";
$dbname = "phpmyadmin"; // Update to your DB name
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
    // Collect form data
    $pollTitle = $_POST['pollTitle'] ?? null;
    $date1 = $_POST['date1'] ?? null;
    $time1 = $_POST['time1'] ?? null;
    $date2 = $_POST['date2'] ?? null;
    $time2 = $_POST['time2'] ?? null;
    $date3 = $_POST['date3'] ?? null;
    $time3 = $_POST['time3'] ?? null;
    $date4 = $_POST['date4'] ?? null;
    $time4 = $_POST['time4'] ?? null;
    $course = $_POST['course'] ?? null;

    // Validate required fields
    if (!$pollTitle || !$date1 || !$time1 || !$date2 || !$time2 || !$date3 || !$time3 || !$date4 || !$time4 || !$course) {
        die("All fields are required. Please fill in the form correctly.");
    }

    // Sanitize input to prevent SQL injection
    $pollTitle = htmlspecialchars($pollTitle);
    $date1 = htmlspecialchars($date1);
    $time1 = htmlspecialchars($time1);
    $date2 = htmlspecialchars($date2);
    $time2 = htmlspecialchars($time2);
    $date3 = htmlspecialchars($date3);
    $time3 = htmlspecialchars($time3);
    $date4 = htmlspecialchars($date4);
    $time4 = htmlspecialchars($time4);
    $course = htmlspecialchars($course);

    // Prepare the INSERT query
    $sql = "INSERT INTO Polls (poll_title, date1, time1, votes1, date2, time2, votes2, date3, time3, votes3, date4, time4, votes4, course) 
            VALUES (?, ?, ?, 0, ?, ?, 0, ?, ?, 0, ?, ?, 0, ?)";

    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $pollTitle, $date1, $time1, $date2, $time2, $date3, $time3, $date4, $time4, $course);

    if ($stmt->execute()) {
        // Redirect to another PHP file, e.g., 'viewpoll.php'
        header("Location: viewpoll.php");
        exit(); // Ensure script stops execution after redirect
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
}
$conn->close();
?>