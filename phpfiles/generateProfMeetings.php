<?php
session_start();

// Database connection
$host = "127.0.0.1";
$dbname = "phpmyadmin";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the professor is logged in
if (!isset($_SESSION['userID'])) {
    echo "You need to log in first.";
    exit();
}

// Get the logged-in professor's ID from the session
$professorID = $_SESSION['userID'];

// Query for the professor's availability with status 'B' (Booked)
$query = "
    SELECT 
        el.LastName AS ProfessorName, 
        pa.Availability
    FROM EmployeeLogin el
    JOIN ProfessorAvailability pa ON el.EmployeeID = pa.ProfessorID
    WHERE el.EmployeeID = '$professorID'
";

$result = $conn->query($query);

// Array to track time slots to avoid duplicates
$seenTimeSlots = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availabilities = json_decode($row['Availability'], true);

        // Loop through the available time slots and filter for the booked ones (status 'B')
        foreach ($availabilities as $availability) {
            // Only proceed if the time slot hasn't been seen before
            $timeSlot = $availability['date'] . ' ' . $availability['time'];
            if (in_array($timeSlot, $seenTimeSlots)) {
                continue; // Skip this time slot if it's already been processed
            }
            $seenTimeSlots[] = $timeSlot; // Mark the time slot as seen

            // Check if the status of the appointment is 'B' (Booked)
            if ($availability['status'] !== 'B') {
                continue; // Skip this availability if it's not 'B' (Booked)
            }

            // Output the table row for each booked availability with only Event, Date & Time, Location
            echo '<tr>';
            echo '<td>Booked Event</td>';
            echo '<td>' . htmlspecialchars($availability['date'] . ' - ' . $availability['time']) . '</td>';
            echo '<td>' . htmlspecialchars($availability['location']) . '</td>';
            echo '</tr>';
        }
    }
} else {
    // No booked events found
    echo '<tr><td colspan="3">No booked events for this professor.</td></tr>';
}

$conn->close();
?>