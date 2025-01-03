<?php
//DOMinatrix- Natasha
session_start();

// Database configuration
$host = "localhost";       
$dbname = "phpmyadmin"; 
$username = "root"; 
$password = "";  

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input from form fields
    $userID = $_POST['ID'];  // This will be the StudentID or EmployeeID based on selection
    $email = $_POST['email'];
    $userPassword = $_POST['password'];
    $role = $_POST['role'];  // Get the selected role (Student or Professor)

    // Prevent SQL Injection
    $userID = mysqli_real_escape_string($conn, $userID);
    $userPassword = mysqli_real_escape_string($conn, $userPassword);
    $email = mysqli_real_escape_string($conn, $email);

    // Validate email based on role
    if ($role === 'student') {
        // For students, check if the email matches the first.last@mail.mcgill.ca format
        $emailParts = explode('@', $email);
        $emailDomain = end($emailParts);

        if ($emailDomain === 'mail.mcgill.ca') {
            $emailLocal = strtolower($emailParts[0]);
            $nameParts = explode('.', $emailLocal);
            
            // Check if the email matches the format first.last for students
            if (count($nameParts) < 2) {
                echo "<script>alert('Invalid email format for student. It should be in the format first.last@mail.mcgill.ca'); window.location.href = '../public/login.html';</script>";
                exit();
            }

            // Query for student login
            $query = "SELECT * FROM StudentLogin WHERE StudentID = '$userID' AND Email = '$email'";
        } else {
            echo "<script>alert('Invalid email domain for student. It should end with @mail.mcgill.ca.'); window.location.href = '../public/login.html';</script>";
            exit();
        }

    } elseif ($role === 'employee') {
        // For professors, the email should end with @mcgill.ca
        $emailParts = explode('@', $email);
        $emailDomain = end($emailParts);

        if ($emailDomain !== 'mcgill.ca') {
            echo "<script>alert('Invalid email domain for professor. It should end with @mcgill.ca.'); window.location.href = '../public/login.html';</script>";
            exit();
        }

        // Query for professor login
        $query = "SELECT * FROM EmployeeLogin WHERE EmployeeID = '$userID' AND Email = '$email'";
    }
    else {
        echo "<script>alert('Invalid role selected.'); window.location.href = '../public/login.html';</script>";
        exit();
    }

    // Execute the query
    $result = $conn->query($query);

    // Check if login is successful
    if ($result && $result->num_rows > 0) {
        // Fetch user info
        $row = $result->fetch_assoc();
        $storedPassword = $row['Password']; // The hashed password from the database

        // Verify if the password matches the hashed password
        if (password_verify($userPassword, $storedPassword)) {

            $_SESSION['userID'] = $userID;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;
        
            // Successful login
            if ($role === 'student') {
                // Redirect to the student dashboard
                header("Location: ../private/studentdashboard.php");
            } elseif ($role === 'employee') {
                // Redirect to the professor dashboard
                header("Location: ../private/ProfessorDashboard.php");
            }
            exit();  // Ensure no further code is executed after the redirect
        } else {
            // Login failed
            echo "<script>alert('Invalid credentials.'); window.location.href = '../public/login.html';</script>";
        }
    } else {
        // If no user found
        echo "<script>alert('Invalid credentials.'); window.location.href = '../public/login.html';</script>";
    }
}
// Close database connection
$conn->close();
?>