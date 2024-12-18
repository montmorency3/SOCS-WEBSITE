<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start the session
session_start();

// Check if the user is logged in and has the role of 'professor'
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'employee') {
    echo "<script>
            alert('You must be logged in as a professor to access this page.');
            window.location.href = '../public/login.html'; // Redirect to login page
          </script>";
    exit();
}

// Retrieve professor's userID
$professorID = $_SESSION['userID'];

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
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $course = isset($_POST['course']) ? $_POST['course'] : null;
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $startTime = isset($_POST['startTime']) ? $_POST['startTime'] : null;
    $endTime = isset($_POST['endTime']) ? $_POST['endTime'] : null;
    $location = isset($_POST['location']) ? $_POST['location'] : null;

    // Validate required fields
    if (!$title || !$course || !$date || !$startTime || !$endTime || !$location) {
        die("All fields are required. Please fill in the form correctly.");
    }

    // Sanitize input to prevent XSS
    $title = htmlspecialchars($title);
    $course = htmlspecialchars($course);
    $date = htmlspecialchars($date);
    $startTime = htmlspecialchars($startTime);
    $endTime = htmlspecialchars($endTime);
    $location = htmlspecialchars($location);

    // New booking object
    $newBooking = [
        "title" => $title,
        "course" => $course,
        "date" => $date,
        "time" => "$startTime - $endTime",
        "location" => $location
    ];

    // Check if the professor already has a row in the table
    $sql = "SELECT Availability FROM ProfessorAvailability WHERE ProfessorID = '$professorID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // If row exists, fetch current Availability
        $row = $result->fetch_assoc();
        $currentAvailability = json_decode($row['Availability'], true); // Decode JSON array

        // Append the new booking
        $currentAvailability[] = $newBooking;
    } else {
        // If no row exists, initialize with the new booking
        $currentAvailability = [$newBooking];
    }

    // Re-encode the updated availability to JSON
    $updatedAvailability = json_encode($currentAvailability);

    $sql = "INSERT INTO ProfessorAvailability (ProfessorID, Availability) 
            VALUES (?, ?) 
            ON DUPLICATE KEY UPDATE Availability = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $professorID, $updatedAvailability, $updatedAvailability);

    if ($stmt->execute()) {
        echo "<script>alert('Office hours updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating office hours: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Bookings</title>
  <style>
    /* Import Poppins Font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      background-color: #FDFEFD;
      color: #27455A;
      display: flex;
      flex-direction: column;
    }

    /* Background Image */
    .background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      background: url('../private/Images/Trottier.png') no-repeat center center/cover;
      filter: brightness(0.8);
      z-index: -1;
    }

    /* Sidebar */
    .sidebar {
      background-color: rgba(39, 69, 90, 0.9);
      color: white;
      width: 250px;
      height: 100vh;
      position: fixed;
      padding: 20px;
      z-index: 1;
    }

    .sidebar h3 {
      font-weight: 600;
      margin-bottom: 20px;
      text-transform: uppercase;
    }
    
    .sidebar button {
      background-color: white;
      color: #27455A;
      border: none;
      padding: 10px;
      border-radius: 5px;
      cursor: pointer;
      width: 100%;
      font-weight: 400;
      margin-bottom: 20px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }
    
    /* Hover Effect */
      .sidebar button:hover {
      background-color: #f4f4f4;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar li {
      margin-bottom: 15px;
      font-weight: 300;
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
    }
    
    .sidebar li:hover{
      transform: translateX(5px);
    }

    .sidebar hr {
      border: 0.5px solid #FDFEFD;
      margin-top: 10px;
    }

    .sidebar p {
      margin-top: 20px;
      font-weight: 400;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    /* Main Content */
    .main-content {
      margin-left: 270px;
      padding: 30px;
      width: calc(95% - 270px);

    }

    .main-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .main-header h1 {
      font-size: 2rem;
      font-weight: 600;
      margin: 0;
      color: white;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      max-width: 800px;
      margin: 0 auto;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 10px;
      font-weight: 600;
      color: #2C3E50;
    }

    .form-group input, .form-group select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      font-family: 'Poppins', sans-serif;
    }

    /* Two columns for time inputs */
    .time-row {
      display: flex;
      justify-content: space-between;
      gap: 20px;
    }

    .time-row .form-group {
      flex: 1;
    }

    .button-group {
      text-align: right;
    }

    .button-group button {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1rem;
      font-family: 'Poppins', sans-serif;
      margin-left: 10px;
    }

    .btn-save {
      background-color: #2C3E50;
      color: white;
    }

    .btn-save:hover {
      background-color: #34495E;
    }

    .btn-cancel {
      background-color: #BDC3C7;
      color: white;
    }

    .btn-cancel:hover {
      background-color: #95A5A6;
    }


    

      @media (max-width: 600px) {
  body {
    display: flex;
    flex-direction: column;
  }

  .sidebar {
    width: 100%; /* Full width for sidebar */
    position: relative; /* Remove fixed positioning to stack */
    height: auto; /* Adjust height to content */
    padding: 0px;
    margin: 0px;
  }

  .main-content {
    margin-left: 0; /* Remove left margin to prevent offset */
    width: 100%; /* Full width for the main content */
    margin: 0px;
    padding: 0px;
  }
}
  </style>
</head>
<body>
  <!-- Background Image -->
  <div class="background"></div>

  <!-- Sidebar -->
  <aside class="sidebar">
    <h3>Manage Bookings</h3>
    <button onclick="location.href='editbookings.php'">CREATE OFFICE HOUR</button>
    <ul>
    <li>
      <a href="../private/ProfessorDashboard.php" style="text-decoration: none; color: inherit;">
        🏠 My Dashboard
      </a>
    </li>
    <li>
      <a href="../private/ProfessorCalendar.html" style="text-decoration: none; color: inherit;">
        🗓 View Calendar
      </a>
    </li>
    <li>
      <a href="../private/CreatePoll.html" style="text-decoration: none; color: inherit;">
        📊 Create Poll
      </a>
    </li>
    <li><a href="viewpoll.php" style="text-decoration: none; color: inherit;">📊 View Poll</a></li>
    <li>
      <a href="editbookings.php" style="text-decoration: none; color: inherit;">
        ⚙ Manage Bookings
      </a>
    </li>
    </ul>
    <hr>
    <p><a href="../public/landingpage.html" style="text-decoration: none; color: inherit;">
        🔒 Log Out
      </a></p>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Manage Bookings</h1>
    </div>

    <div class="form-container">
      <form action="../phpfiles/editbookings.php" method="POST">
        <!-- Title -->
        <div class="form-group">
          <label for="title">Event Title</label>
          <input type="text" id="title" name="title" placeholder="Enter event title" required>
        </div>
    
        <!-- Course Selection -->
        <div class="form-group">
          <label for="course">Course</label>
          <select id="course" name="course" required>
            <option value="">Select Course</option>
            <option value="COMP202">COMP202</option>
            <option value="COMP303">COMP303</option>
            <option value="COMP307">COMP307</option>
          </select>
        </div>
    
        <!-- Date -->
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" required>
        </div>
    
        <!-- Time Inputs in Same Row -->
        <div class="time-row">
          <div class="form-group">
            <label for="startTime">Start Time</label>
            <input type="time" id="startTime" name="startTime" required>
          </div>
          <div class="form-group">
            <label for="endTime">End Time</label>
            <input type="time" id="endTime" name="endTime" required>
          </div>
        </div>
    
        <!-- Recurrence -->
        <div class="form-group">
          <label for="recurrence">Occurrence</label>
          <select id="recurrence" name="recurrence">
            <option value="none">None</option>
            <option value="weekly">Weekly</option>
            <option value="biweekly">Biweekly</option>
            <option value="monthly">Monthly</option>
            <option value="custom">Custom</option>
          </select>
        </div>
    
        <!-- End Date -->
        <div class="form-group">
          <label for="endDate">End Date</label>
          <input type="date" id="endDate" name="endDate">
        </div>
    
        <!-- Location -->
        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" placeholder="Enter location or link">
        </div>
    
        <!-- Buttons -->
        <div class="button-group">
          <button type="submit" class="btn-save">Save Changes</button>
          <button type="button" class="btn-cancel" onclick="window.location.href='ProfessorDashboard.html';">Cancel</button>
        </div>
      </form>
    </div>
    
  </div>
</body>
</html>
