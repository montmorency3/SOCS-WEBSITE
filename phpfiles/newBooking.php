<?php
//DOMinatrix- Nigel
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

// Print session variables to the console log
$sessionData = json_encode($_SESSION);
echo "<script>console.log('Session Data: ', $sessionData);</script>";

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

                // After successfully updating the professor's availability, handle the student's booking
                if (isset($_SESSION['userID'])) {
                    $studentID = $_SESSION['userID'];
                    $newBooking = [
                        'professorID' => $professorID,
                        'date' => $date,
                        'time' => $time
                    ];

                    // Check if the student already has appointments
                    $checkQuery = "SELECT Appointments FROM StudentAppointment WHERE StudentID = ?";
                    $checkStmt = $conn->prepare($checkQuery);
                    $checkStmt->bind_param("i", $studentID);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();

                    if ($checkResult->num_rows > 0) {
                        // If the student already has bookings, append the new booking to the existing array
                        $row = $checkResult->fetch_assoc();
                        $existingBookings = json_decode($row['Appointments'], true);

                        // Append new booking to the array
                        $existingBookings[] = $newBooking;

                        // Update the booking JSON in the database
                        $updateQuery = "UPDATE StudentAppointment SET Appointments = ? WHERE StudentID = ?";
                        $updateStmt = $conn->prepare($updateQuery);
                        $updateStmt->bind_param("si", json_encode($existingBookings), $studentID);
                        $updateStmt->execute();

                        if ($updateStmt->affected_rows > 0) {
                            echo "<script>console.log('New booking successfully added to StudentAppointments for student $studentID.');</script>";
                        } else {
                            echo "<script>console.log('Failed to update booking in StudentAppointments.');</script>";
                        }

                        $updateStmt->close();
                    } else {
                        // If the student does not have any bookings, insert a new record with the first booking
                        $insertQuery = "INSERT INTO StudentAppointment (StudentID, Appointments) VALUES (?, ?)";
                        $insertStmt = $conn->prepare($insertQuery);
                        $insertStmt->bind_param("is", $studentID, json_encode([$newBooking]));
                        $insertStmt->execute();

                        if ($insertStmt->affected_rows > 0) {
                            echo "<script>console.log('Booking successfully added to StudentAppointments for student $studentID.');</script>";
                        } else {
                            echo "<script>console.log('Failed to insert booking into StudentAppointments.');</script>";
                        }

                        $insertStmt->close();
                    }

                    $checkStmt->close();
                } else {
                    echo "<script>console.log('Student ID not found in session.');</script>";
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