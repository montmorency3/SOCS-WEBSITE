<?php
//DOMinatrix - Muhammad
   // Start the session
   session_start();

   // Unset all session variables
   session_unset();

   // Destroy the session
   session_destroy();

   // Clear the session cookie
   if (ini_get("session.use_cookies")) {
       $params = session_get_cookie_params();
       setcookie(session_name(), '', time() - 42000,
           $params["path"], $params["domain"],
           $params["secure"], $params["httponly"]
       );
   }

   // Redirect to the landing page
   header("Location: ../public/landingpage.html");
   exit();
?>