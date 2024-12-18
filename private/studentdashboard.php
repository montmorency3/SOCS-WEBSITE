<?php
// Start the session at the top of the page
session_start();




// Fetch session ID and data for debugging
$sessionID = session_id();
$sessionData = json_encode($_SESSION); // Convert session variables to JSON

// Include generateOH.php after starting the session
ob_start(); // Start output buffering
include '../phpfiles/generateOH.php';
$generateOHContent = ob_get_clean(); // Capture the output of generateOH.php into a variable
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="landing.css">
  
  <style>
    /* Import Poppins Font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #FDFEFD;
      color: #27455A;
    }

    /* Background Image */
    .background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      background: url('images/trottier.png') no-repeat center center/cover;
      filter: brightness(0.8);
      z-index: -1; /* Keeps background behind everything */
    }

    /* Sidebar */
    .sidebar {
      background-color: rgba(39, 69, 90, 0.9);
      color: white;
      width: 250px;
      height: 100vh;
      position: fixed;
      padding: 20px;
      top: 0;
    }

    .sidebar h3 {
      font-weight: 600;
      margin-bottom: 20px;
      text-transform: uppercase;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar li {
      margin-bottom: 15px;
      font-weight: 300;
      transition: all 0.3s ease;
    }

    .sidebar li:hover {
      transform: translateX(5px);
    }

    .sidebar hr {
      border: 0.5px solid #FDFEFD;
      margin-top: 10px;
    }

    /* Hero Section */
    .hero {
      margin-left: 320px;
      position: absolute;
      top: 40px;
      font-size: 2.5rem;
      font-weight: 600;
      color: #FFFFFF;
    }

    /* Events Table */
    .events-table {
      width: calc(93% - 250px);
      margin-left: 320px;
      margin-top: 120px;
      border-collapse: collapse;
      background-color: #FFFFFF;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .events-table th,
    .events-table td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    .events-table th {
      background-color: #27455A;
      color: white;
      font-weight: bold;
    }

    .events-table tr:hover {
      background-color: #f4f4f4;
    }

    /* Book Button */
    .book-btn {
      background-color: #286a99;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 8px 12px;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
    }

    .book-btn:hover {
      background-color: #0056b3;
      transform: scale(1.05);
    }

    .book-btn.booked {
      background-color: green;
      color: white;
      pointer-events: none;
    }

    /* Submit Button */
    .submit-btn {
      margin-left: 320px;
      margin-top: 20px;
      background-color: #0056b3;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 5px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.3s, transform 0.2s;
    }
  </style>
</head>
<body>
  <!-- Background Image -->
  <div class="background"></div>

  <!-- Sidebar -->
  <aside class="sidebar">
    <h3>WELCOME</h3>
    <ul>
      <li><a href="studentdashboard.php" style="color: inherit; text-decoration: none;">🏠 My Dashboard</a></li>
      <li><a href="studentCalendar.html" style="color: inherit; text-decoration: none;">🗓 View Calendar</a></li>
      <li><a href="VoteonPoll.html" style="color: inherit; text-decoration: none;">📊 Vote on Poll</a></li>
      <li><a href="RequestOfficeHour.html" style="color: inherit; text-decoration: none;">📅 Request Office Hours</a></li>
      <li><a href="RequestEquiptment.html" style="color: inherit; text-decoration: none;">💻 Request Equipment</a></li>
    </ul>
    <hr>
    <p><a href="../public/landingpage.html" style="color: inherit; text-decoration: none;">🔒 Log Out</a></p>
  </aside>

  <!-- Hero Section -->
  <div class="hero">Student Dashboard</div>

  <!-- Events Table -->
  <table class="events-table">
    <thead>
      <tr>
        <th>Event</th>
        <th>Date & Time</th>
        <th>Location</th>
        <th>Professor</th>
        <th>Course Code</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Output the content captured from generateOH.php
      echo $generateOHContent;
      ?>
    </tbody>
  </table>

  <!-- Submit Button -->
  <div>
    <button class="submit-btn" onclick="submitBookings()">Submit Bookings</button>
  </div>

  <!-- JavaScript -->
  <script>
    console.log('Session ID: <?php echo $sessionID; ?>');
    console.log('Session Data:', <?php echo $sessionData; ?>);

    function toggleBook(button) {
      if (button.innerText === "Book") {
        button.innerText = "Booked";
        button.classList.add("booked");
      } else {
        button.innerText = "Book";
        button.classList.remove("booked");
      }
    }

    function submitBookings() {
      const bookedButtons = document.querySelectorAll('.book-btn.booked');
      if (bookedButtons.length > 0) {
        alert("Your bookings have been successfully submitted!");
      } else {
        alert("No bookings have been made yet. Please book an event before submitting.");
      }
    }
  </script>
</body>
</html>