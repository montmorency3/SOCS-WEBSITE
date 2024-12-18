<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection
$host = "127.0.0.1";
$dbname = "phpmyadmin";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = file_get_contents("php://input");

    // Decode the JSON data
    $bookings = json_decode($data, true);

    // Status to set for all bookings
    $status = 'B'; // Booked status

    // Iterate over each booking and update the status
    foreach ($bookings as $booking) {
        $professorID = $booking['professorID'];
        $date = $booking['date'];
        $time = $booking['time'];

        // Check that all required fields are filled
        if (!empty($professorID) && !empty($date) && !empty($time)) {
            // Escape the inputs to prevent SQL injection
            $professorID = $conn->real_escape_string($professorID);
            $date = $conn->real_escape_string($date);
            $time = $conn->real_escape_string($time);

            // SQL query to retrieve the Availability JSON
            $query = "SELECT Availability FROM ProfessorAvailability WHERE ProfessorID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $professorID);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $availability = json_decode($row['Availability'], true);

                // Update the JSON in PHP
                $updated = false; // Flag to check if we update any slot
                foreach ($availability as &$slot) {
                    if ($slot['date'] == $date && $slot['time'] == $time) {
                        $slot['status'] = $status;
                        $updated = true;
                    }
                }

                // Only proceed with update if there was a change
                if ($updated) {
                    // Encode the updated JSON
                    $updatedAvailability = json_encode($availability);

                    // Update the Availability in the database
                    $updateQuery = "UPDATE ProfessorAvailability SET Availability = ? WHERE ProfessorID = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("ss", $updatedAvailability, $professorID);
                    $updateStmt->execute();

                    // Check if the update was successful
                    if ($updateStmt->affected_rows > 0) {
                        echo "<script>console.log('Booking updated successfully for Professor $professorID on $date at $time.');</script>";
                    } else {
                        echo "<script>console.log('No change detected or no matching availability found.');</script>";
                    }

                    // Close the update statement
                    $updateStmt->close();
                } else {
                    echo "<script>console.log('No matching availability found for this booking.');</script>";
                }

            } else {
                echo "<script>console.log('Professor ID $professorID not found in the database.');</script>";
            }

            // Close the select statement
            $stmt->close();
        }
    }

    echo "<script>console.log('All bookings submitted successfully!');</script>";
    $conn->close();
} else {
    echo "<script>console.log('Invalid request method.');</script>";
}
?>