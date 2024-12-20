<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "phpmyadmin";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Collect form data
  $date = $_POST['date'] ?? null;
  $startTime = $_POST['startTime'] ?? null;
  $endTime = $_POST['endTime'] ?? null;
  $location = $_POST['location'] ?? null;

  // Validate required fields
  if (!$date || !$startTime || !$endTime || !$location) {
      die("All fields (Date, Start Time, End Time, Location) are required.");
  }
   // Generate a unique booking ID
   $uniqueID = uniqid();
   $bookingURL = "http://localhost/SOCS-WEBSITE/public/urlBookingPage.html?id=" . $uniqueID;


  // Generate time range
  $time = "$startTime - $endTime";

  // Default status
  $status = "NB";

  // Create new booking object
  $newBooking = [
      "date" => $date,
      "time" => $time,
      "location" => $location,
      "status" => $status
  ];

  // Hardcoded professor ID for this example (replace with dynamic value as needed)
  $professorID = $_SESSION['userID'];

  // Check if professor has existing availability
  $sql = "SELECT Availability FROM ProfessorAvailability WHERE ProfessorID = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $professorID);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      // Fetch current availability and append the new booking
      $row = $result->fetch_assoc();
      $currentAvailability = json_decode($row['Availability'], true);
      $currentAvailability[] = $newBooking;
  } else {
      // Initialize availability with the new booking
      $currentAvailability = [$newBooking];
  }

  // Update the database
  $updatedAvailability = json_encode($currentAvailability);
  $sql = "INSERT INTO ProfessorAvailability (ProfessorID, Availability) 
          VALUES (?, ?) 
          ON DUPLICATE KEY UPDATE Availability = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("iss", $professorID, $updatedAvailability, $updatedAvailability);

  if ($stmt->execute()) {
      echo "<script>
              alert('Office hours added successfully with status NB!, Share this link: $bookingURL');
              window.location.href = 'editbookings.php';
            </script>";
  } else {
      echo "<script>alert('Error updating office hours: " . $stmt->error . "');</script>";
  }

  $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bookings</title>
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
      margin-left: 250px;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      width: calc(100% - 250px);
    }

    .form-container {
      background: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
      max-width: 800px;
      width: 90%;
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

    /* Responsive Design */
    @media (max-width: 900px) {
      body {
        flex-direction: column;
      }

      .sidebar {
        width: 100%;
        position: relative;
        height: auto;
        padding: 10px;
      }

      .main-content {
        margin-left: 0;
        width: 100%;
        height: auto;
        padding: 20px;
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
      <li><a href="../private/ProfessorDashboard.php" style="text-decoration: none; color: inherit;">🏠 My Dashboard</a></li>
      <li><a href="../private/ProfessorCalendar.php" style="text-decoration: none; color: inherit;">🗓 View Calendar</a></li>
      <li><a href="../private/CreatePoll.php" style="text-decoration: none; color: inherit;">📊 Create Poll</a></li>
      <li><a href="viewpoll.php" style="text-decoration: none; color: inherit;">📊 View Poll</a></li>
      <li><a href="editbookings.php" style="text-decoration: none; color: inherit;">⚙ Manage Bookings</a></li>
    </ul>
    <hr>
    <p><a href="../public/landingpage.html" style="text-decoration: none; color: inherit;">🔒 Log Out</a></p>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <div class="form-container">
      <h2>Edit Office Hours</h2>
      <form action="editbookings.php" method="POST">
        <!-- Date -->
        <div class="form-group">
          <label for="date">Date</label>
          <input type="date" id="date" name="date" required>
        </div>

        <!-- Time Inputs -->
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

        <!-- Location -->
        <div class="form-group">
          <label for="location">Location</label>
          <input type="text" id="location" name="location" placeholder="Enter location" required>
        </div>

        <!-- Submit Button -->
        <div class="button-group">
          <button type="submit" class="btn-save">Save</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>