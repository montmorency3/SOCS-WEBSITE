<?php
// sample.php

// Define a constant
define("GREETING", "Welcome to our website!");

// Function definition
function calculateArea($length, $width) {
    return $length * $width;
}

// Class definition
class User {
    private $name;
    private $email;

    public function __construct($name, $email) {
        $this->name = $name;
        $this->email = $email;
    }

    public function getInfo() {
        return "Name: {$this->name}, Email: {$this->email}";
    }
}

// Variables and array
$colors = ["Red", "Green", "Blue"];
$age = 25;

// Conditional statement
if ($age >= 18) {
    echo "You are an adult.<br>";
} else {
    echo "You are a minor.<br>";
}

// Loop
echo "Colors: ";
foreach ($colors as $color) {
    echo "$color ";
}
echo "<br>";

// Using the function
$area = calculateArea(5, 3);
echo "Area of rectangle: $area<br>";

// Using the class
$user = new User("John Doe", "john@example.com");
echo $user->getInfo() . "<br>";

// Using a constant
echo GREETING;
?>