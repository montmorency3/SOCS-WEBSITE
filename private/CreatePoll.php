<?php
//DOMinatrix- Muhammad
session_start();

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

$userID = $_SESSION['userID'];

$sql = "SELECT Courses FROM EmployeeInfo WHERE EmployeeID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$courses = [];
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $courses = json_decode($row['Courses'], true);
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/private.css">

  <title>Create Poll</title>
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
    .form-row {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 20px;
    }

    .form-row label {
      font-weight: 600;
      flex: 0 0 100px;
      text-align: left;
    }

    .form-row input, .form-row select {
      flex: 1;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
      font-family: 'Poppins', sans-serif;
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
  </style>
</head>
<body>
  <!-- Background Image -->
  <div class="background"></div>

  <!-- Sidebar -->
  <aside class="sidebar">
    <h3>Create Poll</h3>
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
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-header">
      <h1>Create Poll</h1>
    </div>

    <div class="form-container">
      <form action="../phpfiles/poll.php" method="POST">
        <!-- Poll Title -->
        <div class="form-row">
          <label for="pollTitle">Poll Title</label>
          <input type="text" id="pollTitle" name="pollTitle" placeholder="Enter poll title" required>
        </div>

        <!-- Date and Time Rows -->
        <div class="form-row">
          <label for="date1">Date 1</label>
          <input type="date" id="date1" name="date1" required>
          <label for="time1">Time 1</label>
          <input type="time" id="time1" name="time1" required>
        </div>

        <div class="form-row">
          <label for="date2">Date 2</label>
          <input type="date" id="date2" name="date2" required>
          <label for="time2">Time 2</label>
          <input type="time" id="time2" name="time2" required>
        </div>

        <div class="form-row">
          <label for="date3">Date 3</label>
          <input type="date" id="date3" name="date3" required>
          <label for="time3">Time 3</label>
          <input type="time" id="time3" name="time3" required>
        </div>

        <div class="form-row">
          <label for="date4">Date 4</label>
          <input type="date" id="date4" name="date4" required>
          <label for="time4">Time 4</label>
          <input type="time" id="time4" name="time4" required>
        </div>

        <!-- Course Dropdown -->
        <div class="form-row">
            <label for="course">Course</label>
            <select id="course" name="course" required>
              <option value="">Select Course</option>
              <?php foreach ($courses as $course): ?>
                <option value="<?= htmlspecialchars($course) ?>"><?= htmlspecialchars($course) ?></option>
              <?php endforeach; ?>
            </select>
        </div>

        <!-- Submit and Cancel Buttons -->
        <div class="button-group">
          <button type="submit" class="btn-save">Create Poll</button>
          <button type="button" class="btn-cancel" onclick="location.href='ProfessorDashboard.php'">Cancel</button>
        </div>
      </form>
    </div>
  </div>
  <script src="assets/javascript/switchLanguageStudents.js"></script>
</body>
</html>
