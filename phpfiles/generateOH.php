<?php
// Database connection
$host = "127.0.0.1";
$dbname = "your_database_name";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query for professor availability, names, and courses
$query = "
    SELECT 
        e.Name AS ProfessorName, 
        JSON_UNQUOTE(e.Courses) AS Courses, 
        pa.Availability
    FROM EmployeeInfo e
    JOIN ProfessorAvailability pa ON e.EmployeeID = pa.ProfessorID
";

$result = $conn->query($query);
$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availabilities = json_decode($row['Availability'], true);
        $courses = json_decode($row['Courses'], true);

        foreach ($availabilities as $availability) {
            foreach ($courses as $course) {
                $events[] = [
                    'event' => 'Scheduled Event',
                    'date_time' => $availability['date'] . ' - ' . $availability['time'],
                    'location' => $availability['location'],
                    'professor' => $row['ProfessorName'],
                    'course_code' => $course
                ];
            }
        }
    }
}
$conn->close();

// Output as JSON
header('Content-Type: application/json');
echo json_encode($events);