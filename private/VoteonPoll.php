<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "your_database_name");

if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}

// Fetch Poll Data
$pollData = [];
$query = "SELECT * FROM polls";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $pollData[] = $row;
  }
} else {
  $pollData = null; // No data found
}

// Format DateTime Function
function formatDateTime($dateTime)
{
  return date('F j, Y, g:i A', strtotime($dateTime));
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
    /* All your existing styles are retained here */
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

    /* Sidebar and other styles here */
  </style>
</head>

<body>
  <!-- Background Image -->
  <div class="background"></div>

  <!-- Sidebar -->
  <aside class="sidebar">
    <h3>Vote on Poll</h3>
    <ul>
      <li><a href="studentdashboard.html" style="text-decoration: none; color: inherit;">🏠 My Dashboard</a></li>
      <li><a href="studentCalendar.html" style="text-decoration: none; color: inherit;">🗓 View Calendar</a></li>
      <li><a href="VoteonPoll.php" style="text-decoration: none; color: inherit;">📊 Vote on Poll</a></li>
    </ul>
    <hr>
    <p>
    <div class="lang_logout_container">
      <a href="../public/landingpage.html" style="text-decoration: none; color: inherit;">🔒 Log Out</a>
      <a href="#" class="menu-language">FR</a>
    </div>
    </p>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <div class="main-container">
      <h1>Vote on OH Poll</h1>
      <h3>Rank Days</h3>
      <div class="rank-list" id="rankList">
        <?php if (isset($pollData) && is_array($pollData)): ?>
          <?php $rank = 1; ?>
          <?php foreach ($pollData as $data): ?>
            <div class="rank-row" data-index="<?php echo $rank; ?>">
              <div class="rank-number"><?php echo $rank; ?></div>
              <div class="rank-item" draggable="true">
                <?php echo htmlspecialchars($data['title'] ?? 'No Title'); ?> -
                <?php echo formatDateTime($data['datetime'] ?? 'now'); ?>
              </div>
            </div>
            <?php $rank++; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No poll data available.</p>
        <?php endif; ?>
      </div>

      <!-- Submit Button -->
      <button class="submit-btn">SUBMIT</button>
    </div>
  </div>

  <!-- Drag-and-Drop JavaScript -->
  <script>
    const rankItems = document.querySelectorAll(".rank-item");
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
        }, 0);
      });
    });

    rankList.addEventListener("dragover", (e) => e.preventDefault());

    rankList.addEventListener("drop", (e) => {
      const targetRow = e.target.closest(".rank-row");
      if (targetRow && draggedItem) {
        const draggedRow = draggedItem.parentNode;
        const tempContent = targetRow.querySelector(".rank-item").innerHTML;

        targetRow.querySelector(".rank-item").innerHTML = draggedItem.innerHTML;
        draggedItem.innerHTML = tempContent;
      }
    });
  </script>
  <script src="assets/javascript/switchLanguageStudents.js"></script>
</body>

</html>