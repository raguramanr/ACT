<?php
$servername = "localhost";
$username = "root";
$password = "extreme";
$dbname = "autosqa_act";

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
