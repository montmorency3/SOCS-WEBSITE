<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$host = "127.0.0.1";
$dbname = "phpmyadmin";  // Update to your database name
$username = "root";
$password = "";

// Create connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the student is logged in (you can add a check for valid session)
if (!isset($_SESSION['userID'])) {
    // Redirect to login page if no userID is found in session
    header("Location: login.php");
    exit();
}

// Get the student ID from the session
$studentID = $_SESSION['userID'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/private.css">

  <title>Office Hour Calendar</title>

  <!-- FullCalendar Library -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

  <style>
    /* Your existing CSS here */
  </style>
</head>
<body>
  <!-- Background Image -->
  <div class="background"></div>

  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <!-- Sidebar content here -->
  </aside>

  <!-- Calendar Container -->
  <div class="calendar-container">
    <div id="calendar"></div>
  </div>

  <?php
  // Query to fetch the appointments JSON for the logged-in student
  $query = "SELECT Appointments FROM StudentAppointment WHERE studentID = ?";
  $stmt = $conn->prepare($query);
  $stmt->bind_param("i", $studentID);
  $stmt->execute();
  $result = $stmt->get_result();

  // Fetch the row with appointments JSON
  $appointments = [];
  if ($row = $result->fetch_assoc()) {
      // Decode the JSON from the Appointments column
      $appointmentsData = json_decode($row['Appointments'], true); // Decode JSON to associative array

      // Process each appointment in the JSON array
      foreach ($appointmentsData as $appointment) {
          $date = $appointment['date']; // Appointment date
          $timeRange = $appointment['time']; // Appointment time range
          $professorID = $appointment['professorID']; // Professor ID

          // Query to get the professor's last name using professorID
          $professorQuery = "SELECT LastName FROM EmployeeInfo WHERE EmployeeID = ?";
          $professorStmt = $conn->prepare($professorQuery);
          $professorStmt->bind_param("s", $professorID);
          $professorStmt->execute();
          $professorResult = $professorStmt->get_result();
          $professorLastName = "";
          if ($professorRow = $professorResult->fetch_assoc()) {
              $professorLastName = $professorRow['LastName']; // Get the professor's last name
          }
          $professorStmt->close();

          // Split the time range into start and end times
          list($startTime, $endTime) = explode(" - ", $timeRange);

          // Convert the 12-hour format time to 24-hour format
          $startTime = date("H:i:s", strtotime($startTime));
          $endTime = date("H:i:s", strtotime($endTime));

          // Construct start and end times in ISO format
          $start = $date . 'T' . $startTime;  // Start time in ISO format 'YYYY-MM-DDTHH:MM:SS'
          $end = $date . 'T' . $endTime;      // End time in ISO format 'YYYY-MM-DDTHH:MM:SS'

          // Add the appointment to the array with professor's last name
          $appointments[] = [
              'title' => "Appointment with Professor $professorLastName",
              'start' => $start,
              'end' => $end
          ];
      }
  }

  $stmt->close();
  $conn->close();

  // Convert the appointments array to a JSON format
  $appointmentsJson = json_encode($appointments);
  ?>

  <!-- FullCalendar Initialization -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var calendarEl = document.getElementById('calendar');
      var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek', // Default to weekly view
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,timeGridWeek,timeGridDay' // Add Monthly View
        },
        slotMinTime: '08:00:00', // Start time of the day
        slotMaxTime: '20:00:00', // End time of the day
        allDaySlot: false,
        events: <?php echo $appointmentsJson; ?> // Inject the JSON data from PHP
      });
      calendar.render();
    });
  </script>

  <script src="assets/javascript/switchLanguageMenu.js"></script>
</body>
</html>