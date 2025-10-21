<?php
require 'db_connect.php'; 
header('Content-Type: application/json');

$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : 0;

$seats = [];

if ($location_id > 0) {
    
    $stmt = $conn->prepare("SELECT id, seat_number, status FROM seats 
                             WHERE location_id = ? 
                             ORDER BY CAST(SUBSTRING(seat_number, 2) AS UNSIGNED)");
    
    $stmt->bind_param("i", $location_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc()) {
        $seats[] = $row; 
    }
    $stmt->close();
}

echo json_encode($seats);
?>