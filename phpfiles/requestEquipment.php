<?php
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

// Check if form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $studentID = $_POST['studentID'];
    $equipmentType = $_POST['equipmentType'];

    // Validate inputs
    if (empty($studentID) || empty($equipmentType)) {
        die("Student ID and Equipment Type are required.");
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
            // Decrement the available amount
            $newAmount = $availableAmount - 1;
            $updateQuery = "UPDATE AvailableEquipment SET Amount = $newAmount WHERE Equipment = '$equipmentType'";
            $conn->query($updateQuery);

            // Check if student already exists in LoanedEquipment
            $checkLoanQuery = "SELECT * FROM LoanedEquipment WHERE StudentID = $studentID";
            $loanResult = $conn->query($checkLoanQuery);

            if ($loanResult && $loanResult->num_rows > 0) {
                // Update existing JSON data
                $loanRow = $loanResult->fetch_assoc();
                $equipmentLoaned = json_decode($loanRow['Equipment'], true);

                // Increment the loaned equipment count
                if (isset($equipmentLoaned[$equipmentType])) {
                    $equipmentLoaned[$equipmentType]++;
                } else {
                    $equipmentLoaned[$equipmentType] = 1;
                }

                $updatedEquipmentJSON = json_encode($equipmentLoaned);
                $updateLoanQuery = "UPDATE LoanedEquipment SET Equipment = '$updatedEquipmentJSON' WHERE StudentID = $studentID";
                $conn->query($updateLoanQuery);
            } else {
                // Insert new record in LoanedEquipment
                $newEquipmentJSON = json_encode([$equipmentType => 1]);
                $insertLoanQuery = "INSERT INTO LoanedEquipment (StudentID, Equipment) VALUES ($studentID, '$newEquipmentJSON')";
                $conn->query($insertLoanQuery);
            }

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
                echo "Equipment loaned successfully! An email notification has been sent.";
            } else {
                echo "Equipment loaned successfully, but the email notification failed to send.";
            }
        } else {
            echo "Sorry, $equipmentType is out of stock.";
        }
    } else {
        echo "Equipment type not found.";
        echo "Query: " . $query;
    }
} else {
    echo "Invalid request.";
}

// Close the database connection
$conn->close();
?>