<?php
// Start the session at the top of the page
session_start();

   // Prevent caching
   header("Cache-Control: no-cache, no-store, must-revalidate"); 
   header("Pragma: no-cache"); 
   header("Expires: 0"); 


$sessionID = session_id();
$sessionData = json_encode($_SESSION); 


ob_start(); 
include '../phpfiles/generateOH.php';
$generateOHContent = ob_get_clean(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <link rel="stylesheet" href="landing.css">
  
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

   
    .hero {
      margin-left: 320px;
      position: absolute;
      top: 40px;
      font-size: 2.5rem;
      font-weight: 600;
      color: #FFFFFF;
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
      <li><a href="studentCalendar.php" style="color: inherit; text-decoration: none;">🗓 View Calendar</a></li>
      <li><a href="VoteonPoll.php" style="color: inherit; text-decoration: none;">📊 Vote on Poll</a></li>
      <li><a href="RequestOfficeHour.php" style="color: inherit; text-decoration: none;">📅 Request Office Hours</a></li>
      <li><a href="RequestEquiptment.html" style="color: inherit; text-decoration: none;">💻 Request Equipment</a></li>
    </ul>
    <hr>
    <p><a href="../phpfiles/logout.php" class="link-logout" style="text-decoration: none; color: inherit;">
    🔒 Log Out
</a></p>
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
    // Array to store all booked events
    let bookedEvents = [];

    // Function to toggle the booking status of a button
    function toggleBook(button) {
      const professorID = button.getAttribute('data-professor-id');
      const date = button.getAttribute('data-date');
      const time = button.getAttribute('data-time');

      if (button.innerText === "Book") {
        button.innerText = "Booked";
        button.classList.add("booked");

        // Add the booking to the array
        bookedEvents.push({ professorID, date, time });
      } else {
        button.innerText = "Book";
        button.classList.remove("booked");

        // Remove the booking from the array
        bookedEvents = bookedEvents.filter(event => !(event.professorID === professorID && event.date === date && event.time === time));
      }
    }

    // Function to submit all booked events
    function submitBookings() {
  if (bookedEvents.length > 0) {
    console.log("Booked Events:", JSON.stringify(bookedEvents)); 

    // Send data as JSON instead of query string
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../phpfiles/newBooking.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");


    console.log("Sending data...");

    xhr.send(JSON.stringify(bookedEvents));

    xhr.onload = function () {
      if (xhr.status == 200) {
    console.log("Server Response:", xhr.responseText); // Debug response
    alert("Your bookings have been successfully submitted!");
    bookedEvents = [];
  } else {
    console.error("Server Error:", xhr.responseText);
    alert("Error submitting bookings.");
  }
    };
  } else {
    alert("No bookings have been made yet. Please book an event before submitting.");
  }
}
  </script>
</body>
</html>