<?php
//Enable Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SESSION['ProfessorID'] = 111; // Hardcoded for testing purposes

// Database connection
$host = "127.0.0.1";
$dbname = "phpmyadmin"; //update
$username = "root";
$password = "";

// Establish connection
$conn = new mysqli($host, $username, $password, $dbname);


$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $professorID = isset($_SESSION['ProfessorID']) ? $_SESSION['ProfessorID'] : null;
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : null;
    $endTime = isset($_POST['endTime']) ? $_POST['endTime'] : null;
    $location = isset($_POST['location']) ? $_POST['location'] : null;

    // Validate required fields
    if (!$date || !$startTime || !$endTime || !$location) {
        die("All fields are required. Please fill in the form correctly.");
    }

    // Sanitize input to prevent XSS
    $date = htmlspecialchars($date);
    $startTime = htmlspecialchars($startTime);
    $endTime = htmlspecialchars($endTime);
    $location = htmlspecialchars($location);

    // Prepare the INSERT query
    $sql = "INSERT INTO ProfessorAvailability (ProfessorID, Availability) 
            VALUES ('$professorID', '[{\"date\":\"$date\",\"time\":\"$startTime - $endTime\",\"location\":\"$location\"}]')
            ON DUPLICATE KEY UPDATE Availability = '[{\"date\":\"$date\",\"time\":\"$startTime - $endTime\",\"location\":\"$location\"}]'";

    // Execute the query and handle success or failure
    if ($conn->query($sql) === TRUE) {
        echo "Office hours updated successfully!";
        echo "<br><br>";
        echo "Submitted Data:";
        echo "<br>Date: " . $date;
        echo "<br>Time: " . $startTime . " - " . $endTime;
        echo "<br>Location: " . $location;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

}
        $conn->close();
        ?>

?>
