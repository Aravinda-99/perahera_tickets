<?php
// db_connect.php
$servername = "localhost:4306";
$username = "root"; 
$password = ""; 
$dbname = "perahera_tickets";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>