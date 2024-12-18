<?php
// Database connection
$host = "127.0.0.1";
$dbname = "phpmyadmin";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query for professor availability, names, and courses
$query = "
    SELECT 
        e.LastName AS ProfessorName, 
        JSON_UNQUOTE(e.Courses) AS Courses, 
        pa.Availability
    FROM EmployeeInfo e
    JOIN ProfessorAvailability pa ON e.EmployeeID = pa.ProfessorID
";

$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availabilities = json_decode($row['Availability'], true);
        $courses = json_decode($row['Courses'], true);

        foreach ($availabilities as $availability) {
            foreach ($courses as $course) {
                // Output the table row for each availability
                echo '<tr>';
                echo '<td>Scheduled Event</td>';
                echo '<td>' . htmlspecialchars($availability['date'] . ' - ' . $availability['time']) . '</td>';
                echo '<td>' . htmlspecialchars($availability['location']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ProfessorName']) . '</td>';
                echo '<td>' . htmlspecialchars($course) . '</td>';
                echo '<td><button class="book-btn" onclick="toggleBook(this)">Book</button></td>';
                echo '</tr>';
            }
        }
    }
} else {
    // No events found
    echo '<tr><td colspan="6">No events available.</td></tr>';
}

$conn->close();
?>