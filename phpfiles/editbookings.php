<?php
session_start();
$_SESSION['ProfessorID'] = 666; // Hardcoded for testing purposes

// Database connection
$host = "127.0.0.1";
$dbname = "phpmyadmin";
$username = "root";
$password = "";

// Establish connection
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Input variables
    $professorID = $_SESSION['ProfessorID'];
    $date = $_POST['date'];
    $startTime = $_POST['startTime'];
    $endTime = $_POST['endTime'];
    $location = $_POST['location'];

    // Validation: Check empty fields
    if (empty($date) || empty($startTime) || empty($endTime) || empty($location)) {
        $errorMessage = "Please fill out all required fields.";
    } else {
        // Prepare the new availability slot as JSON
        $newAvailability = json_encode([
            ["date" => $date, "time" => "$startTime - $endTime", "location" => $location]
        ]);

        // SQL Query with ON DUPLICATE KEY UPDATE
        $insertQuery = "INSERT INTO ProfessorAvailability (ProfessorID, Availability) VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE Availability = VALUES(Availability)";
        $stmt = $conn->prepare($insertQuery);
        if ($stmt) {
            $stmt->bind_param("is", $professorID, $newAvailability);
            if ($stmt->execute()) {
                $successMessage = "Office hours updated successfully!";
            } else {
                $errorMessage = "Error updating office hours: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errorMessage = "Database query failed: " . $conn->error;
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Office Hours</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        label { display: block; margin-top: 10px; }
        input, button { margin-top: 5px; padding: 8px; width: 100%; }
        button { background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background-color: #45a049; }
        .success-message { color: green; font-weight: bold; text-align: center; }
        .error-message { color: red; font-weight: bold; text-align: center; }
        form { max-width: 400px; margin: auto; }
    </style>
</head>
<body>
    <h2>Manage Office Hours</h2>

    <!-- Success or Error Messages -->
    <?php if (!empty($successMessage)): ?>
        <p class="success-message"><?php echo $successMessage; ?></p>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <p class="error-message"><?php echo $errorMessage; ?></p>
    <?php endif; ?>

    <!-- Form -->
    <form method="POST" action="">
        <label>Date:</label>
        <input type="date" name="date" required>

        <label>Start Time:</label>
        <input type="time" name="startTime" required>

        <label>End Time:</label>
        <input type="time" name="endTime" required>

        <label>Location:</label>
        <input type="text" name="location" placeholder="Enter Location" required>

        <button type="submit">Submit</button>
    </form>
</body>
</html>
