<?php
//DOMinatrix - Alex
session_start();

// Database configuration

$host = "127.0.0.1";       
$dbname = "phpmyadmin"; 
$username = "root"; 
$password = "";  

// Create database connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['userID'])) {
    echo "<script>alert('You need to log in first.'); window.location.href = 'RequestEquipment.html';</script>";
    exit();
}

// Get the logged-in student's ID from the session
$studentID = $_SESSION['userID'];

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = $_POST['studentID'];
    $equipmentType = $_POST['equipmentType'];

    // Validate inputs
    if (empty($studentID) || empty($equipmentType)) {
        echo "<script>alert('Student ID and Equipment Type are required.'); window.location.href = 'RequestEquipment.html';</script>";
        exit();
    }

    // Escape inputs for security
    $studentID = mysqli_real_escape_string($conn, $studentID);
    $equipmentType = mysqli_real_escape_string($conn, $equipmentType);

    // Check if equipment is available
    $query = "SELECT * FROM AvailableEquipment WHERE Equipment = '$equipmentType'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $availableAmount = $row['Amount'];

        if ($availableAmount > 0) {
            // Check if the student has more than 3 items loaned
            $checkLoanQuery = "SELECT * FROM LoanedEquipment WHERE StudentID = $studentID";
            $loanResult = $conn->query($checkLoanQuery);

            if ($loanResult && $loanResult->num_rows > 0) {
                // Get the existing loaned items as an associative array
                $loanRow = $loanResult->fetch_assoc();
                $equipmentLoaned = json_decode($loanRow['Equipment'], true);

                // Calculate the total number of loaned items
                $totalLoanedItems = array_sum($equipmentLoaned);

                if ($totalLoanedItems >= 3) {
                    echo "<script>alert('You cannot loan more than 3 items.'); window.location.href = '../private/RequestEquiptment.html';</script>";
                    exit();
                } else {
                    // Proceed with the loan request
                    if (isset($equipmentLoaned[$equipmentType])) {
                        $equipmentLoaned[$equipmentType]++;
                    } else {
                        $equipmentLoaned[$equipmentType] = 1;
                    }

                    $updatedEquipmentJSON = json_encode($equipmentLoaned);
                    $updateLoanQuery = "UPDATE LoanedEquipment SET Equipment = '$updatedEquipmentJSON' WHERE StudentID = $studentID";
                    $conn->query($updateLoanQuery);
                }
            } else {
                // If no existing record, create a new loaned record for the student
                $newEquipmentJSON = json_encode([$equipmentType => 1]);
                $insertLoanQuery = "INSERT INTO LoanedEquipment (StudentID, Equipment) VALUES ($studentID, '$newEquipmentJSON')";
                $conn->query($insertLoanQuery);
            }

            // Decrement the available amount
            $newAmount = $availableAmount - 1;
            $updateQuery = "UPDATE AvailableEquipment SET Amount = $newAmount WHERE Equipment = '$equipmentType'";
            $conn->query($updateQuery);

            // Send email notification
            $to = "nigel.ojuang@mail.mcgill.ca"; // Replace with your email address
            $subject = "New Equipment Loan Request";
            $headers = "From: noreply@mcgill.ca" . "\r\n" .
                       "Content-Type: text/html; charset=UTF-8";

            $message = "
            <html>
                <head>
                    <title>Equipment Loan Request</title>
                </head>
                <body>
                    <h2>New Equipment Loan</h2>
                    <p><strong>Student ID:</strong> $studentID</p>
                    <p><strong>Equipment Type:</strong> $equipmentType</p>
                    <p><strong>Current Available Amount:</strong> $newAmount</p>
                </body>
            </html>
            ";

            // Check if the email is sent successfully
            if (mail($to, $subject, $message, $headers)) {
                echo "<script>alert('Equipment loaned successfully! An email notification has been sent.'); window.location.href = '../private/RequestEquiptment.html';</script>";
            } else {
                echo "<script>alert('Equipment loaned successfully, but the email notification failed to send.'); window.location.href = '../private/RequestEquiptment.html';</script>";
            }
        } else {
            echo "<script>alert('Sorry, $equipmentType is out of stock.'); window.location.href = '../private/RequestEquiptment.html';</script>";
        }
    } else {
        echo "<script>alert('Equipment type not found.'); window.location.href = '../private/RequestEquiptment.html';</script>";
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href = '../private/RequestEquiptment.html';</script>";
}

// Close the database connection
$conn->close();
?>