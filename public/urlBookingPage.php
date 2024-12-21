<?php
session_start();
//parses from url
// Get the booking details from the URL
$bookingID = htmlspecialchars($_GET['id'] ?? null);
$date = htmlspecialchars($_GET['date'] ?? null);
$startTime = htmlspecialchars($_GET['startTime'] ?? null);

// Validate the required parameters
if (!$bookingID || !$date || !$startTime) {
    die("Invalid booking details.");
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentEmail = filter_var($_POST['mcgill-id'] ?? null, FILTER_SANITIZE_EMAIL);

    // Validate email
    if (!filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email address.");
    }

    // Validate checkbox
    if (empty($_POST['confirm'])) {
        die("You must confirm your booking.");
    }

    // Send email notification
    $to = $studentEmail;
    $subject = "Office Hour Booking Confirmation";
    $headers = "From: noreply@mcgill.ca" . "\r\n" .
               "Content-Type: text/html; charset=UTF-8";

    $message = "
    <html>
        <head>
            <title>Office Hour Booking Confirmation</title>
        </head>
        <body>
            <h2>Office Hour Booking Confirmed</h2>
            <p>Dear Student,</p>
            <p>Your booking has been confirmed for the following office hour:</p>
            <p><strong>Date:</strong> $date</p>
            <p><strong>Time:</strong> $startTime</p>
            <p>Thank you for booking. Please ensure to arrive on time for your session.</p>
            <p>Best regards,</p>
            <p>The Office Hour Team</p>
        </body>
    </html>
    ";

    // Check if the email was sent successfully
    if (mail($to, $subject, $message, $headers)) {
        echo "<script>alert('Booking confirmed! A confirmation email has been sent to $studentEmail.');</script>";
    } else {
        echo "<script>alert('Booking confirmed, but the confirmation email failed to send.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Office Hour</title>
  <link rel="stylesheet" href="assets/css/styles.css">


  <!-- Internal Styles -->
  <style>
    /* General Styles */
    body {
      margin: 0;
      font-family: 'HK Grotesk', sans-serif;
      background-color: #27455A;
      /* Blue background */
      color: #27455A;
      height: 100vh;
      width:100%;
      display: flex;
      align-items: center;
      position: relative;
    }

    /* Background Image with Transparency */
    body::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: url('images/Trottier.png') no-repeat center center/cover;
      opacity: 0.5;
      /* Transparency for the image */
      z-index: 0;
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 50px;
      background-color: rgba(39, 69, 90, 0.8);
      color: white;
      position: fixed;
      top: 0;
      width: 100%;
      z-index: 10;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      margin-left: 20px;
      font-size: 1rem;
    }

    .main-content {
    display: flex;
    justify-content: center; /* Horizontally centers the content */
    align-items: center; /* Vertically centers the content */
    width: 100vw; /* Take up 100% of the viewport width */
    height: calc(100vh - 60px); 
    }

    /* Booking Container */
    .booking-container {
      background-color: rgba(255, 255, 255, 0.9);
      width: 350px;
      padding: 30px;
      border-radius: 40px;
      box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
      margin: 0 50px;
      text-align: center;
      position: relative;
      z-index: 1;
      /* Above background */
    }

    .booking-container h1 {
      font-size: 1.8rem;
      margin-bottom: 20px;
    }

    .booking-container label {
      display: block;
      margin-bottom: 10px;
      text-align: left;
      font-size: 1rem;
      color: #27455A;
    }

    /* Booking Details Container */
    .booking-details-container {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      margin-bottom: 20px;
    }

    .booking-details {
      font-size: 1rem;
      margin: 0;
      /* Removes default margin for cleaner alignment */
      color: #27455A;
    }

    .booking-container input[type="text"] {
      width: 100%;
      padding: 8px;
      margin-bottom: 20px;
      border: 1px solid #BFDCE5;
      border-radius: 5px;
      font-size: 1rem;
      box-sizing: border-box;
    }

    .checkbox-container {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      margin-bottom: 20px;
    }

    .checkbox-container input[type="checkbox"] {
      margin-right: 10px;
      transform: scale(1.2);
      cursor: pointer;
    }

    .submit-btn {
      background-color: #27455A;
      color: #FDFEFD;
      border: none;
      padding: 10px 20px;
      border-radius: 40px;
      font-size: 1rem;
      cursor: pointer;
    }

    .submit-btn:hover {
      background-color: #77A9B2;
    }
  </style>
</head>

<body>


  <!-- Navbar -->


  <header>
    <nav class="navbar">
      <div class="logo">
        <img src="images/logo.png" alt="SOCS Logo">
      </div>
      <ul class="nav-links">
        <li><a href="landingpage.html" class="home-link">Home</a></li>
        <li><a href="about.html" class="about-link">About</a></li>
      </ul>

      <div class="auth-links">
        <a class="language" href="#">FR</a>
        <a href="login.html" class="login-link">Login</a>
        <a href="register.html" class="register-btn register-link">Register</a>
      </div>

      <div class="search-bar">
        <input type="text" placeholder="Search">
        <button><img src="images/search-icon.png" alt="Search"></button>
      </div>

      <div class="hamburger" id="hamburger">
        <span>&#9776;</span>
      </div>
    </nav>
  </header>

  <main>
    <nav class="menu" id="menu"> 
      <div class="nav-links">
        <a href="landingpage.html" class="menu-home-link">Home</a>
        <a href="about.html" class="menu-about-link">About</a>
        <a href="#" class="menu-language">FR</a>
      </div>
    </nav>

    <div class="main-content">
      <!-- Booking Container -->
      <div class="booking-container">
        <h1>CONFIRM OFFICE HOUR BOOKING</h1>
        <form id="booking-form" action="#" method="POST">
          
        <!-- McGill ID -->
<div style="margin-bottom: 20px;">
    <label for="mcgill-email" style="display: block; font-size: 16px; font-weight: bold; margin-bottom: 10px; color: #333;">McGill Email</label>
    <input 
        type="email" 
        id="mcgill-email" 
        name="mcgill-id" 
        placeholder="Enter your McGill Email" 
        required 
        style="width: 100%; max-width: 300px; padding: 15px; font-size: 16px; border: 1px solid #ccc; border-radius: 10px; box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);"
    />
</div>



          <!-- Booking Details -->
          <div class="booking-details-container">
              <p class="booking-details">
                  You are booking an office hour for <strong><?= htmlspecialchars($date) ?> at <?= htmlspecialchars($startTime) ?></strong>
              </p>
          </div>

          <!-- Hidden Input for Booking ID -->
          <input type="hidden" name="bookingID" value="<?= htmlspecialchars($bookingID) ?>">


          <!-- Checkbox -->
          <div class="checkbox-container">
            <input type="checkbox" id="confirm" name="confirm">
            <label for="confirm">Click to confirm</label>
        </div>

          <!-- Submit Button -->
          <button type="submit" class="submit-btn" id="submit-btn" disabled>SUBMIT</button>
        </form>
      </div>
    </div>


      <!-- JavaScript for Checkbox -->
      <script>
        document.addEventListener('DOMContentLoaded', function () {
        const confirmCheckbox = document.getElementById('confirm');
        const submitButton = document.getElementById('submit-btn');

        // Enable/disable the submit button based on checkbox state
        confirmCheckbox.addEventListener('change', function () {
            submitButton.disabled = !confirmCheckbox.checked;
        });
    });
      </script>
</body>
<script src = assets/javascript/hamburger.js></script>
<script src="assets/javascript/switchMenuLanguage.js"> </script>