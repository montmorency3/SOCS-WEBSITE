<?php
session_start();

// Debugging: Print session variables to check if userID is set

// Database connection
$host = "127.0.0.1";
$dbname = "phpmyadmin";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the student is logged in
if (!isset($_SESSION['userID'])) {
    echo "You need to log in first.";
    exit();
}

// Get the logged-in student's ID from the session
$studentID = $_SESSION['userID'];

// Query to get the courses the student is enrolled in
$queryStudentCourses = "
    SELECT Courses 
    FROM StudentInfo 
    WHERE StudentID = '$studentID'
";


$resultCourses = $conn->query($queryStudentCourses);
$courses = [];

if ($resultCourses && $resultCourses->num_rows > 0) {
    $row = $resultCourses->fetch_assoc();
    // Decode the JSON array of courses
    $courses = json_decode($row['Courses'], true);
} else {
    echo "No courses found for the student.";
    exit();
}

// Query for professor availability, names, and courses, filtered by the student's courses
$query = "
    SELECT 
        e.LastName AS ProfessorName, 
        JSON_UNQUOTE(e.Courses) AS Courses, 
        pa.Availability
    FROM EmployeeInfo e
    JOIN ProfessorAvailability pa ON e.EmployeeID = pa.ProfessorID
";

$result = $conn->query($query);

// Array to track time slots to avoid duplicates
$seenTimeSlots = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availabilities = json_decode($row['Availability'], true);
        $professorCourses = json_decode($row['Courses'], true);

        // Loop through the available time slots and filter based on student's enrolled courses
        foreach ($availabilities as $availability) {
            // Only proceed if the time slot hasn't been seen before
            $timeSlot = $availability['date'] . ' ' . $availability['time'];
            if (in_array($timeSlot, $seenTimeSlots)) {
                continue; // Skip this time slot if it's already been processed
            }
            $seenTimeSlots[] = $timeSlot; // Mark the time slot as seen

            // Collect courses that the student is enrolled in and that the professor teaches
            $coursesAtThisTime = [];
            foreach ($professorCourses as $course) {
                if (in_array($course, $courses)) {
                    $coursesAtThisTime[] = $course; // Add the course to the list
                }
            }

            // If the student is enrolled in any courses the professor teaches at this time
            if (!empty($coursesAtThisTime)) {
                // Output the table row for each availability with concatenated courses
                echo '<tr>';
                echo '<td>Scheduled Event</td>';
                echo '<td>' . htmlspecialchars($availability['date'] . ' - ' . $availability['time']) . '</td>';
                echo '<td>' . htmlspecialchars($availability['location']) . '</td>';
                echo '<td>' . htmlspecialchars($row['ProfessorName']) . '</td>';
                echo '<td>' . htmlspecialchars(implode(', ', $coursesAtThisTime)) . '</td>'; // Concatenate the courses
                echo '<td><button class="book-btn" onclick="toggleBook(this)">Book</button></td>';
                echo '</tr>';
            }
        }
    }
} else {
    // No events found
    echo '<tr><td colspan="6">No events available for the courses you are enrolled in.</td></tr>';
}

$conn->close();
?>