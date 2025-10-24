<?php
require 'db_connect.php'; 
header('Content-Type: application/json');

$location_id = isset($_GET['location_id']) ? intval($_GET['location_id']) : 0;

$seats = [];

if ($location_id > 0) {
    // First, get the location details including total_seats
    $stmt = $conn->prepare("SELECT name, total_seats FROM locations WHERE id = ?");
    $stmt->bind_param("i", $location_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $location = $result->fetch_assoc();
        $total_seats = $location['total_seats'];
        $location_name = $location['name'];
        
        // Get existing booked seats for this location
        $stmt2 = $conn->prepare("SELECT seat_number FROM seats WHERE location_id = ? AND status = 'booked'");
        $stmt2->bind_param("i", $location_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        $booked_seats = [];
        while($row = $result2->fetch_assoc()) {
            $booked_seats[] = $row['seat_number'];
        }
        $stmt2->close();
        
        // Generate seats based on total_seats from locations table
        // Determine seat prefix based on location name
        $prefix = '';
        if (strpos($location_name, 'A') !== false) $prefix = 'A';
        elseif (strpos($location_name, 'B') !== false) $prefix = 'B';
        elseif (strpos($location_name, 'C') !== false) $prefix = 'C';
        elseif (strpos($location_name, 'D') !== false) $prefix = 'D';
        else $prefix = 'S'; // Default prefix
        
        // Generate seats
        for ($i = 1; $i <= $total_seats; $i++) {
            $seat_number = $prefix . $i;
            $status = in_array($seat_number, $booked_seats) ? 'booked' : 'available';
            
            $seats[] = [
                'id' => $location_id . '_' . $i, // Generate unique ID
                'seat_number' => $seat_number,
                'status' => $status
            ];
        }
    }
    $stmt->close();
}

echo json_encode($seats);
?>