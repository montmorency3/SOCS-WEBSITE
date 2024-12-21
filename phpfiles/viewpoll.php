<?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   
   session_start();

   // Prevent caching
   header("Cache-Control: no-cache, no-store, must-revalidate");
   header("Pragma: no-cache");
   header("Expires: 0");
   
   // Check if the user is logged in and has the role of 'professor'
   if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'employee') {
       echo "<script>
               alert('You must be logged in as a professor to access this page.');
               window.location.href = '../public/login.html'; // Redirect to login page
             </script>";
       exit();
   }
   
   $host = "localhost";
   $dbname = "phpmyadmin";
   $username = "root";
   $password = "";
   
   $conn = new mysqli($host, $username, $password, $dbname);
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   
   $professorID = $_SESSION['userID'];
   
   $sql = "SELECT id, poll_title FROM Polls WHERE professorID = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("i", $professorID);
   $stmt->execute();
   $result = $stmt->get_result();
   
   $polls = [];
   if ($result->num_rows > 0) {
       while ($row = $result->fetch_assoc()) {
           $polls[] = $row;
       }
   }
   
   $poll_id = isset($_GET['pollID']) ? intval($_GET['pollID']) : null;
   
   if (!$poll_id && !empty($polls)) {
       $poll_id = $polls[0]['id'];
       header("Location: viewpoll.php?pollID=$poll_id");
       exit();
   }
   
   // Fetch specific poll data
   if ($poll_id) {
       $sql = "SELECT * FROM Polls WHERE id = ?";
       $stmt = $conn->prepare($sql);
       $stmt->bind_param("i", $poll_id);
       $stmt->execute();
       $result = $stmt->get_result();
   
       if ($result->num_rows > 0) {
           $poll = $result->fetch_assoc();
           $pollTitle = htmlspecialchars($poll['poll_title']);
           $course = htmlspecialchars($poll['course']);
           $dates = [
               ["date" => $poll['date1'], "time" => $poll['time1'], "votes" => $poll['votes1']],
               ["date" => $poll['date2'], "time" => $poll['time2'], "votes" => $poll['votes2']],
               ["date" => $poll['date3'], "time" => $poll['time3'], "votes" => $poll['votes3']],
               ["date" => $poll['date4'], "time" => $poll['time4'], "votes" => $poll['votes4']]
           ];

          
          usort($dates, function($a, $b) {
            return $b['votes'] <=> $a['votes'];
          });

       } else {
           $error = "Poll not found.";
       }
   } else {
       $error = "No polls available for this professor.";
   }
    // Helper function to format date
    function formatDate($date) {
        if ($date) {
            $dateTime = new DateTime($date);
            return $dateTime->format('F j, Y');
        }
        return "Invalid date";
    }

    // Helper function to format time
    function formatTime($time) {
        if ($time) {
            $timeObject = new DateTime($time);
            return $timeObject->format('g:i A');
        }
        return "Invalid time";
    }
   
   $stmt->close();
   $conn->close();
   ?>

<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Poll Results</title>
      <style>
         @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');
         body {
         margin: 0;
         font-family: 'Poppins', sans-serif;
         background-color: #FDFEFD;
         color: #27455A;
         display: flex;
         height: 100vh;
         }
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
         .sidebar li:hover {
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
         .main-content {
         margin-left: 270px;
         padding: 30px;
         width: calc(100% - 270px);
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
         .results-container {
         background: rgba(255, 255, 255, 0.9);
         padding: 30px;
         border-radius: 15px;
         box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
         max-width: 800px;
         margin: 0 auto;
         }
         table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 20px;
         }
         th, td {
         padding: 12px;
         border: 1px solid #ccc;
         text-align: center;
         }
         th {
         background-color: #2C3E50;
         color: white;
         }
         td {
         background-color: #FDFEFD;
         }
      </style>
   </head>
   <body>
      <!-- Background Image -->
      <div class="background"></div>
      <!-- Sidebar -->
      <aside class="sidebar">
         <h3>Poll Results</h3>
         <button onclick="location.href='editbookings.php'">CREATE OFFICE HOUR</button>
         <ul>
            <li>
               <a href="../private/ProfessorDashboard.php" style="text-decoration: none; color: inherit;">
               🏠 My Dashboard
               </a>
            </li>
            <li>
               <a href="../private/ProfessorCalendar.php" style="text-decoration: none; color: inherit;">
               🗓 View Calendar
               </a>
            </li>
            <li>
               <a href="../private/CreatePoll.php" style="text-decoration: none; color: inherit;">
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
         <p><a href="../phpfiles/logout.php" class="link-logout" style="text-decoration: none; color: inherit;">
    🔒 Log Out
</a>
         </p>
      </aside>
      <!-- Main Content -->
      <div class="main-content">
         <div class="main-header">
            <h1>Poll Results</h1>
         </div>
         <!-- Display poll results -->
         <div class="results-container">
          <!-- Dropdown to select a poll -->
        <div style="text-align: center; margin-bottom: 20px;">
        <?php if (!empty($polls)): ?>
        <h2 style="display: inline-block; margin-right: 10px;">
            Poll: <?= isset($pollTitle) ? htmlspecialchars($pollTitle) : 'No Title' ?> 
            (<?= isset($course) ? htmlspecialchars($course) : 'No Course' ?>)
        </h2>
        <form method="GET" action="viewpoll.php" style="display: inline-block;">
            <label for="pollID" style="font-weight: bold;">Select a Poll:</label>
            <select name="pollID" id="pollID" onchange="this.form.submit()" style="padding: 5px; font-size: 1rem;">
                <option value="">-- Select a Poll --</option>
                <?php foreach ($polls as $pollItem): ?>
                <option value="<?= $pollItem['id'] ?>" <?= isset($poll_id) && $poll_id == $pollItem['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($pollItem['poll_title']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </form>
        <?php else: ?>
        <h2 style="text-align: center; color: red;">Please create a poll in the Create Poll tab.</h2>
        <?php endif; ?>
        </div>
          <!-- Poll Results Table -->
          <?php if (isset($pollTitle) && isset($dates)): ?>
          <table>
              <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Votes</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($dates as $dateTime): ?>
                <tr>
                <td><?= htmlspecialchars(formatDate($dateTime['date'])) ?></td>
                <td><?= htmlspecialchars(formatTime($dateTime['time'])) ?></td>
                    <td><?= $dateTime['votes'] ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
          </table>
          <?php elseif (isset($error)): ?>
          <p style="text-align: center; color: red;"><?= htmlspecialchars($error) ?></p>
          <?php else: ?>
          <p style="text-align: center; color: red;">Select a poll to view its details.</p>
          <?php endif; ?>
        </div>
      </div>
   </body>
</html>