<?php
//DOMinatrix- Nigel
session_start();

  
   header("Cache-Control: no-cache, no-store, must-revalidate"); 
   header("Pragma: no-cache"); 
   header("Expires: 0"); 


$host = "127.0.0.1";
$dbname = "phpmyadmin";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_SESSION['userID'])) {
    echo "You need to log in first.";
    exit();
}


$studentID = $_SESSION['userID'];


$queryStudentCourses = "
    SELECT Courses 
    FROM StudentInfo 
    WHERE StudentID = '$studentID'
";

$resultCourses = $conn->query($queryStudentCourses);
$courses = [];

if ($resultCourses && $resultCourses->num_rows > 0) {
    $row = $resultCourses->fetch_assoc();
    // Decode the JSON array of courses
    $courses = json_decode($row['Courses'], true);
} else {
    echo "No courses found for the student.";
    exit();
}

// Query to get professors who teach the student's enrolled courses
$queryProfessors = "
    SELECT 
        e.FirstName AS ProfessorFirstName, 
        e.LastName AS ProfessorLastName, 
        e.EmployeeID AS ProfessorID,
        JSON_UNQUOTE(e.Courses) AS Courses 
    FROM EmployeeInfo e
";

$resultProfessors = $conn->query($queryProfessors);
$professors = [];

if ($resultProfessors && $resultProfessors->num_rows > 0) {
    while ($row = $resultProfessors->fetch_assoc()) {
        $professorCourses = json_decode($row['Courses'], true);
        
        $professorCoursesFiltered = array_intersect($professorCourses, $courses);

        if (!empty($professorCoursesFiltered)) {
            $professors[] = [
                'professorID' => $row['ProfessorID'],
                'fullName' => $row['ProfessorFirstName'] . ' ' . $row['ProfessorLastName'],
                'courses' => implode(', ', $professorCoursesFiltered),
                'email' => strtolower($row['ProfessorFirstName']) . '.' . strtolower($row['ProfessorLastName']) . '@mcgill.ca' // Email format
            ];
        }
    }
} else {
    echo "No professors found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $professorID = $_POST['prof'];
    $date = $_POST['date'];
    $startTime = $_POST['start-time'];
    $endTime = $_POST['end-time'];
    $description = $_POST['description'];
    $alternateEmail = $_POST['altemail'];

    // Find the professor's email
    $professorEmail = '';
    foreach ($professors as $professor) {
        if ($professor['professorID'] == $professorID) {
            $professorEmail = $professor['email'];
            break;
        }
    }

    // If alternate email is provided, use that
    if (!empty($alternateEmail)) {
        $toEmail = $alternateEmail;
    } else {
        $toEmail = $professorEmail;
    }

    // Create the email subject and message
    $subject = "Request for Office Hours";
    $message = "
        <h2>Office Hours Request</h2>
        <p><strong>Student ID:</strong> $studentID</p>
        <p><strong>Request Date:</strong> $date</p>
        <p><strong>Time:</strong> $startTime - $endTime</p>
        <p><strong>Description:</strong> $description</p>
    ";

    // Headers for email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: nigel.ojuang@mail.mcgill.ca" . "\r\n";

    // Send the email
    $emailSuccess = mail($toEmail, $subject, $message, $headers);

    // Return success or failure status
    $status = $emailSuccess ? 'success' : 'failure';
    echo "<script>window.location.href = '?status=$status';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="assets/css/private.css">

  <title>Student Dashboard</title>
  <!-- Internal Styles -->
  <style>
   
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

    .sidebar hr {
      border: 0.5px solid #FDFEFD;
      margin: 0px; 
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
      width: calc(100% - 250px);
      height: 100vh;
    }

    
    .main-container {
      background-color: rgba(255, 255, 255, 0.9);
      width: 500px;
      border-radius: 20px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
      padding: 30px;
    }

    .main-container h1 {
      text-align: center;
      margin-bottom: 20px;
      font-size: 1.5rem;
      color: #19344D;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-size: 1rem;
      color: #19344D;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #BFDCE5;
      border-radius: 10px;
      font-size: 1rem;
      background-color: #F7FAFC;
      color: #27455A;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #77A9B2;
    }

    .time-row {
      display: flex;
      justify-content: space-between;
      gap: 10px;
    }

    .submit-btn {
      background-color: #19344D;
      color: #FDFEFD;
      border: none;
      padding: 10px 20px;
      border-radius: 40px;
      font-size: 1rem;
      cursor: pointer;
      width: 100%;
      margin-top: 20px;
      text-align: center;
    }

    .submit-btn:hover {
      background-color: #77A9B2;
    }

    @media (max-width: 850px) {

     
      body {
        display: flex;
        flex-direction: column;
       
        align-items: center;
        justify-content: flex-start;
      
        height: 100vh;
       
        margin: 0;
      }


      .sidebar {
        background-color: rgba(39, 69, 90, 0.9);
        z-index: 3;
        width: 100vw;
    
        height: auto;
        
        position: static;
        
        padding: 0;
      }

     
      .main-content {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 100vw;
        
        height: 100vh;
        
        padding: 0;
        margin-left: 0;
      }

      
      .main-container {
        width: 100vw;
        height: 100vh;
        padding: 30px;
        box-sizing: border-box;
       
        border-radius: 0;
      
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        
        align-items: center;
       
      }


      .form-group input,
      .form-group select,
      .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #BFDCE5;
        border-radius: 10px;
        font-size: 1rem;
        background-color: #F7FAFC;
        color: #27455A;
        margin-bottom: 15px;
        
      }

      
      .submit-btn {
        width: 100%;
       
        padding: 15px;
      }
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      max-width: 400px;
      width: 100%;
    }

    .modal .modal-header {
      font-size: 1.5rem;
      margin-bottom: 15px;
    }

    .modal .modal-body {
      font-size: 1rem;
      margin-bottom: 20px;
    }

    .modal .modal-footer {
      margin-top: 10px;
    }

    .modal .modal-btn {
      background-color: #19344D;
      color: white;
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .modal .modal-btn:hover {
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
      <li>
        <a href="studentdashboard.php" class="link-dashboard" style="text-decoration: none; color: inherit;">
          🏠 My Dashboard
        </a>
      </li>
      <li>
        <a href="studentCalendar.php" class="link-calendar" style="text-decoration: none; color: inherit;">
          🗓 View Calendar
        </a>
      </li>
      <li>
        <a href="VoteonPoll.php" class="link-poll" style="text-decoration: none; color: inherit;">
          📊 Vote on Poll
        </a>
      </li>
      <li>
        <a href="RequestOfficeHour.php" class="link-office-hours" style="text-decoration: none; color: inherit;">
          📅 Request Office Hours
        </a>
      </li>
      <li>
        <a href="RequestEquiptment.html" class="link-equipment" style="text-decoration: none; color: inherit;">
          💻 Request Equipment
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
    <!-- Main Form -->
    <div class="main-container">
      <h1>REQUEST OFFICE HOUR</h1>
      <form action="" method="POST">
        <!-- Date and Time -->
        <div class="form-group">
          <label for="date">Monday, 25th November 13:00–15:00</label>
          <div class="time-row">
            <input type="date" id="date" name="date" value="2024-11-25" />
            <input type="time" id="start-time" name="start-time" value="13:00" />
            <span>to</span>
            <input type="time" id="end-time" name="end-time" value="15:00" />
          </div>
        </div>

        <!-- Professors -->
        <div class="form-group time-row">
          <select id="prof" name="prof">
            <option value="">Select Prof/TA</option>
            <?php
            foreach ($professors as $professor) {
                echo "<option value='" . $professor['professorID'] . "'>" . $professor['fullName'] . " (" . $professor['courses'] . ")</option>";
            }
            ?>
          </select>
        </div>

        <!-- Description -->
        <div class="form-group">
          <textarea rows="3" name="description" placeholder="Description..."></textarea>
        </div>

        <!-- Alternate Email -->
        <div class="form-group">
          <textarea rows="3" name="altemail" placeholder="Alternate email..."></textarea>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit-btn">SUBMIT</button>
      </form>
    </div>
  </div>

  <!-- Success/Failure Modal -->
  <div class="modal" id="status-modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Status</h2>
      </div>
      <div class="modal-body" id="modal-message"></div>
      <div class="modal-footer">
        <button class="modal-btn" id="close-modal">Close</button>
      </div>
    </div>
  </div>

  <script>
    // Show the modal based on the query status
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    const modal = document.getElementById('status-modal');
    const modalMessage = document.getElementById('modal-message');
    
    if (status === 'success') {
        modalMessage.textContent = 'Your office hour request has been sent successfully!';
    } else if (status === 'failure') {
        modalMessage.textContent = 'Sorry, there was an error sending your request.';
    }

    if (status) {
        modal.style.display = 'flex';
    }

    // Close the modal
    document.getElementById('close-modal').onclick = function () {
        modal.style.display = 'none';
    };
  </script>
</body>

</html>