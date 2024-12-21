<?php
   // Enable error reporting for debugging
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   
   session_start();

   // Prevent caching
   header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
   header("Pragma: no-cache"); // HTTP 1.0
   header("Expires: 0"); // Proxies
   if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'student') {
       echo "You must be logged in as a student to vote.";
       exit();
   }
   
   // Database configuration
   $host = "localhost";
   $dbname = "phpmyadmin"; // Update to your DB name
   $username = "root";
   $password = "";
   
   // Create database connection
   $conn = new mysqli($host, $username, $password, $dbname);
   
   // Check connection
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   
   // Fetch student courses from `StudentInfo`
   $studentID = $_SESSION['userID'];
   $sql = "SELECT Courses FROM StudentInfo WHERE StudentID = ?";
   $stmt = $conn->prepare($sql);
   $stmt->bind_param("i", $studentID);
   $stmt->execute();
   $result = $stmt->get_result();
   
   if ($result->num_rows > 0) {
       $studentInfo = $result->fetch_assoc();
       $courses = json_decode($studentInfo['Courses'], true); // Parse JSON into an array
   } else {
       header("Location: VoteonPoll.html");
       exit(); // Stop further execution
   }
   
   // Fetch polls matching the student’s courses
   $polls = [];
   if (!empty($courses)) {
       $placeholders = implode(',', array_fill(0, count($courses), '?'));
       $sql = "SELECT * FROM Polls WHERE course IN ($placeholders)";
       $stmt = $conn->prepare($sql);
   
       // Bind course parameters dynamically
       $stmt->bind_param(str_repeat('s', count($courses)), ...$courses);
       $stmt->execute();
       $result = $stmt->get_result();
   
       while ($row = $result->fetch_assoc()) {
           $polls[] = $row;
       }
   }
   
   if (empty($polls)) {
       header("Location: VoteonPoll.html");
       exit(); // Redirect if no polls are found
   }
   
   // Get selected poll
   $poll_id = isset($_GET['pollID']) ? intval($_GET['pollID']) : $polls[0]['id'];
   $selectedPoll = null;
   
   foreach ($polls as $poll) {
       if ($poll['id'] == $poll_id) {
           $selectedPoll = $poll;
           break;
       }
   }
   
   // Helper function to format date and time
   function formatDateTime($date, $time) {
       $dateTime = new DateTime($date . ' ' . $time);
       return $dateTime->format('F j, Y \a\t g:i A');
   }
   
   // Handle form submission
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       $ranks = $_POST['rank']; // Array of original 'data-index' values
       $pollId = intval($_POST['poll_id']);
   
       if (count($ranks) === 4) {
           // Points system: 1st = 4 points, 2nd = 3 points, 3rd = 2 points, 4th = 1 point
           $points = [4, 3, 2, 1];
   
           // Initialize votes array
           $voteCounts = [0, 0, 0, 0];
   
           foreach ($ranks as $position => $dataIndex) {
               $voteCounts[$dataIndex - 1] = $points[$position];
           }
   
           // SQL to update the votes
           $sql = "UPDATE Polls 
                   SET votes1 = votes1 + ?, 
                       votes2 = votes2 + ?, 
                       votes3 = votes3 + ?, 
                       votes4 = votes4 + ? 
                   WHERE id = ?";
   
           $stmt = $conn->prepare($sql);
           $stmt->bind_param("iiiii", $voteCounts[0], $voteCounts[1], $voteCounts[2], $voteCounts[3], $pollId);
   
           if ($stmt->execute()) {
               echo "<script>alert('Votes submitted successfully!');</script>";
           } else {
               echo "<script>alert('Error updating votes: " . $stmt->error . "');</script>";
           }
   
           $stmt->close();
       } else {
           echo "<script>alert('Invalid vote submission.');</script>";
       }
   }
   
   $conn->close();
   ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/private.css">
  <title>Vote on Poll</title>
  <style>
    /* Import Poppins Font */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap');

    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
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
      background: url('images/trottier.png') no-repeat center center/cover;
      filter: brightness(0.8);
      z-index: -1;
    }

    /* Sidebar */
    .sidebar {
      background-color: rgba(39, 69, 90, 0.9); /* Slight transparency */
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
      margin-bottom: 5px;
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

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin-bottom: 0;
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
      margin: 15px 0 0px; /* Set consistent top and bottom margin */
    }

    .sidebar li:last-child {
      margin-bottom: 0; /* Remove extra space below the last list item */
    }

    .sidebar p {
      margin-top: 20px;
      font-weight: 400;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .main-content {
      margin-left: 250px;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .main-container {
      background-color: rgba(255, 255, 255, 0.9);
      width: 500px;
      border-radius: 20px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
      padding: 30px;
      text-align: center;
    }

    .main-container h1 {
      margin-bottom: 20px;
      font-size: 1.5rem;
      color: #19344D;
    }

    /* Rank List */
    .rank-list {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .rank-row {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .rank-number {
      font-weight: bold;
      font-size: 1.2rem;
      color: #FFFFFF;
      background-color: #27455A;
      border-radius: 50%;
      width: 30px;
      height: 30px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .rank-item {
      flex: 1;
      background-color: #E4ECF2;
      border-radius: 10px;
      padding: 10px;
      text-align: center;
      font-size: 1rem;
      cursor: grab;
      box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .submit-btn {
      background-color: #19344D;
      color: #FFFFFF;
      border: none;
      padding: 15px;
      border-radius: 40px;
      font-size: 1rem;
      cursor: pointer;
      margin-top: 30px;
      width: 100%;
    }

    .submit-btn:hover {
      background-color: #77A9B2;
    }
  </style>
</head>
<body>
  <!-- Background Image -->
  <div class="background"></div>

  <!-- Sidebar -->
  <aside class="sidebar">
    <h3>Vote on Poll</h3>
    <ul>
      <li><a href="studentdashboard.php" class="link-dashboard" style="text-decoration: none; color: inherit;">🏠 My Dashboard</a></li>
      <li><a href="studentCalendar.php" class="link-calendar" style="text-decoration: none; color: inherit;">🗓 View Calendar</a></li>
      <li><a href="VoteonPoll.php" class="link-poll" style="text-decoration: none; color: inherit;">📊 Vote on Poll</a></li>
      <li><a href="RequestOfficeHour.html" class="link-office-hours" style="text-decoration: none; color: inherit;">📅 Request Office Hours</a></li>
      <li><a href="RequestEquiptment.html" class="link-equipment" style="text-decoration: none; color: inherit;">💻 Request Equipment</a></li>
    </ul>
    <hr>
    <p>
      <div class="lang_logout_container">
        <a href="../public/landingpage.html" class="link-logout" style="text-decoration: none; color: inherit;">🔒 Log Out</a>
        <a href="#" class="menu-language">FR</a>
      </div>
    </p>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-container">
      <h1>Vote on OH Poll</h1>
      <h3><?= htmlspecialchars($selectedPoll['poll_title']) ?> (<?= htmlspecialchars($selectedPoll['course']) ?>)</h3>
      
      <div style="text-align: center; margin-bottom: 20px;">
        <form method="GET" action="VoteonPoll.php" style="display: inline-block;">
            <label for="pollID" style="font-weight: bold;">Select a Poll:</label>
            <select name="pollID" id="pollID" onchange="this.form.submit()" style="padding: 5px; font-size: 1rem;">
              <?php foreach ($polls as $poll): ?>
              <option value="<?= $poll['id'] ?>" <?= $poll['id'] == $poll_id ? 'selected' : '' ?>>
                  <?= htmlspecialchars($poll['poll_title'] . " (" . $poll['course'] . ")") ?>
              </option>
              <?php endforeach; ?>
            </select>
        </form>
      </div>

      <!-- Form for submitting ranks -->
      <form method="POST">
      <input type="hidden" name="poll_id" value="<?= $selectedPoll['id']; ?>">

        <div class="rank-list" id="rankList">
          <?php
            $dates = [
                ['date' => $selectedPoll['date1'], 'time' => $selectedPoll['time1']],
                ['date' => $selectedPoll['date2'], 'time' => $selectedPoll['time2']],
                ['date' => $selectedPoll['date3'], 'time' => $selectedPoll['time3']],
                ['date' => $selectedPoll['date4'], 'time' => $selectedPoll['time4']],
            ];
            foreach ($dates as $index => $dateTime) {
          ?>
            <div class="rank-row" data-index="<?= $index + 1 ?>">
              <input type="hidden" name="rank[]" value="<?= $index + 1 ?>">
              <div class="rank-number"><?= $index + 1 ?></div>
              <div class="rank-item" draggable="true">
                <?= formatDateTime($dateTime['date'], $dateTime['time']); ?>
              </div>
            </div>
          <?php } ?>
        </div>
        <button type="submit" class="submit-btn">SUBMIT</button>
      </form>
    </div>
  </div>


  <!-- Drag-and-Drop JavaScript -->
  <script>
    const rankItems = document.querySelectorAll(".rank-item");
    const rankRows = document.querySelectorAll(".rank-row");
    const rankList = document.getElementById("rankList");
    let draggedItem = null;

    rankItems.forEach((item) => {
      item.addEventListener("dragstart", () => {
        draggedItem = item;
        setTimeout(() => (item.style.display = "none"), 0);
      });

      item.addEventListener("dragend", () => {
        setTimeout(() => {
          draggedItem.style.display = "block";
          draggedItem = null;
          updateRanks();
        }, 0);
      });
    });

    rankList.addEventListener("dragover", (e) => e.preventDefault());

    rankList.addEventListener("drop", (e) => {
      const targetRow = e.target.closest(".rank-row");
      if (targetRow && draggedItem) {
        const draggedRow = draggedItem.parentNode;

        // Swap the whole rows instead of just swapping contents
        rankList.insertBefore(draggedRow, targetRow);
        rankList.insertBefore(targetRow, draggedRow.nextSibling);

        updateRanks(); // Ensure the ranks are updated
      }
    });

    function updateRanks() {
      const rows = document.querySelectorAll(".rank-row");
      rows.forEach((row, index) => {
        // Update the rank number visually
        row.querySelector(".rank-number").textContent = index + 1;

        // Update the hidden input value for submission
        row.querySelector("input[name='rank[]']").value = row.getAttribute("data-index");
      });
    }
  </script>
  <script src="assets/javascript/switchLanguageStudents.js"> </script>
</body>
</html>
