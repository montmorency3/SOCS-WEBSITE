<?php
session_start();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate"); 
header("Pragma: no-cache"); 
header("Expires: 0");


// Fetch session ID and data for debugging
$sessionID = session_id();
$sessionData = json_encode($_SESSION); 

// Check if the user is logged in and has the role of 'professor'
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'employee') {
  echo "<script>
          alert('You must be logged in as a professor to access this page.');
          window.location.href = '../public/login.html'; // Redirect to login page
        </script>";
  exit();
}

ob_start();
include '../phpfiles/generateProfMeetings.php';
if(ob_get_contents()) {
  $generateOHContent = ob_get_clean();
} else {
  $generateOHContent = "Error loading content";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Professor Dashboard</title>
  <link rel="stylesheet" href="assets/css/private.css">
  <style>
    
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background-color: #FDFEFD;
      color: #27455A;
    }

    
    .background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100vh;
      background: url('images/trottier.png') no-repeat center center/cover;
      filter: brightness(0.8);
      z-index: -1; 
    }

   
    .sidebar {
      background-color: rgba(39, 69, 90, 0.9); 
      color: white;
      width: 250px;
      height: 100vh;
      position: fixed;
      padding: 20px;
      padding-bottom: 0px;
      top: 0;
      display: flex;
      flex-direction: column;
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
      margin-bottom: 0;
    }

    .sidebar li:hover {
      transform: translateX(5px);
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

    .sidebar hr {
      border: 0.5px solid #FDFEFD;
      margin: 15px 0 0px; 
    }
    
    .sidebar li:last-child {
      margin-bottom: 0; 
    }
    
    .sidebar p {
      margin-top: 20px;
      font-weight: 400;
      display: flex;
      align-items: center;
      gap: 10px;
    }

   
    .day-summary {
      margin-top: 20px;
    }

    .day-summary h4 {
      font-weight: 600;
      margin-bottom: 10px;
    }

    .day-summary .summary-card {
      background-color: #FFFFFF;
      color: #27455A;
      margin-bottom: 10px;
      padding: 10px;
      border-radius: 5px;
      text-align: center;
      box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .day-summary .summary-card h2 {
      margin: 0;
      font-size: 1.5rem;
    }

    .day-summary .summary-card p {
      margin: 0;
      font-size: 0.9rem;
    }

  
    .hero {
      margin-left: 320px; 
      position: absolute;
      top: 40px;
      font-size: 2.5rem;
      font-weight: 600;
      color: #FFFFFF;
      background: transparent;
      z-index: 10;
    }


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
      padding: 5px;
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

    .event-link {
      color: #007BFF;
      text-decoration: none;
    }

    .event-link:hover {
      text-decoration: underline;
    }


    @media (max-width: 850px) {
      body{
        width:100vw;
      }
      .sidebar {
        width: 100vw;
        height: auto;
        position: static;
        padding: 0px;
      }
      
      .hero, .events-table {
        margin-left: 0;
        width: 100%;
      }

      button {
        width: 100vw;
      }

      .hero {
        display: none;
        margin-top: 200px;
      }
    }
  </style>
</head>
<body>
  <div class="background"></div>
  <aside class="sidebar">
    <h3>WELCOME</h3>
    <button onclick="location.href='../phpfiles/editbookings.php'">CREATE OFFICE HOUR</button>
    <ul>
      <li>
        <a href="ProfessorDashboard.php" class="link-dashboard" style="text-decoration: none; color: inherit;">
          🏠 My Dashboard
        </a>
      </li>
      <li>
        <a href="ProfessorCalendar.php" class="link-calendar" style="text-decoration: none; color: inherit;">
          🗓 View Calendar
        </a>
      </li>
      <li>
        <a href="CreatePoll.php" class="link-create-poll" style="text-decoration: none; color: inherit;">
          📊 Create Poll
        </a>
      </li>
      <li><a href="../phpfiles/viewpoll.php" class="link-view-poll" style="text-decoration: none; color: inherit;">📊 View Poll</a></li>
      <li>
        <a href="../phpfiles/editbookings.php" class="link-manage" style="text-decoration: none; color: inherit;">
          ⚙ Manage Bookings
        </a>
      </li>
    </ul>
    <hr>
    <p>
    <div class="lang_logout_container">
    <a href="../phpfiles/logout.php" class="link-logout" style="text-decoration: none; color: inherit;">
    🔒 Log Out
</a>
        <a href="#" class="menu-language">FR</a>
      </div>
    </p>

    <div class="day-summary">
      <h4>Day at a Glance</h4>
      <div class="summary-card">
        <h2><?php echo $upcomingBookingsCount; ?></h2>
        <p>Upcoming Booked Events</p>
      </div>
    </div>
  </aside>
  
  <div class="hero">
    Professor Dashboard
  </div>


  <table class="events-table">
    <thead>
      <tr>
        <th>Event</th>
        <th>Date & Time</th>
        <th>Location</th>
      </tr>
    </thead>
    <tbody>
      <?php 
      
      echo $generateOHContent; 
      ?>
    </tbody>
  </table>
  <script src="assets/javascript/switchLanguageMenu.js"> </script>
</body>
</html>