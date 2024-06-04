<?php
$servername = "localhost";
$username = "root";
$password = "SKRIV_INN_PASSORD_HER";
$database = "fjell_bedriftsloosninger";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
