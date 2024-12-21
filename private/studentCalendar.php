<?php
//DOMinatrix: Nigel,Natasha
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "127.0.0.1";
$dbname = "phpmyadmin";  
$username = "root";
$password = "";

// Create connection to the database
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_SESSION['userID'])) {
   
    header("Location: login.php");
    exit();
}


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
      $appointmentsData = json_decode($row['Appointments'], true); 

      
      foreach ($appointmentsData as $appointment) {
          $date = $appointment['date']; 
          $timeRange = $appointment['time']; 
          $professorID = $appointment['professorID']; 

          $professorQuery = "SELECT LastName FROM EmployeeInfo WHERE EmployeeID = ?";
          $professorStmt = $conn->prepare($professorQuery);
          $professorStmt->bind_param("s", $professorID);
          $professorStmt->execute();
          $professorResult = $professorStmt->get_result();
          $professorLastName = "";
          if ($professorRow = $professorResult->fetch_assoc()) {
              $professorLastName = $professorRow['LastName']; 
          }
          $professorStmt->close();

          list($startTime, $endTime) = explode(" - ", $timeRange);

          $startTime = date("H:i:s", strtotime($startTime));
          $endTime = date("H:i:s", strtotime($endTime));

         
          $start = $date . 'T' . $startTime;  
          $end = $date . 'T' . $endTime;      

       
          $appointments[] = [
              'title' => "Appointment with Professor $professorLastName",
              'start' => $start,
              'end' => $end
          ];
      }
  }

  $stmt->close();
  $conn->close();

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