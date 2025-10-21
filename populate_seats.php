<?php
require 'db_connect.php'; // Make sure this path is correct

echo "Starting to populate seats...<br>";

// 1. Define your locations and the prefix for their seat numbers
// We get this from your 'locations' table screenshot
$locations = [
    ['id' => 1, 'prefix' => 'A', 'total_seats' => 100],
    ['id' => 2, 'prefix' => 'B', 'total_seats' => 100],
    ['id' => 3, 'prefix' => 'C', 'total_seats' => 100],
    ['id' => 4, 'prefix' => 'D', 'total_seats' => 100],
];

// 2. Prepare the SQL query one time
$stmt = $conn->prepare("INSERT INTO seats (location_id, seat_number, status) VALUES (?, ?, 'available')");

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$total_inserted = 0;

// 3. Loop through each location
foreach ($locations as $location) {
    $location_id = $location['id'];
    $prefix = $location['prefix'];
    $total_seats = $location['total_seats'];
    
    echo "Inserting seats for Location " . $prefix . "...<br>";

    // 4. Inner loop to create seat numbers from 1 to 100
    for ($i = 1; $i <= $total_seats; $i++) {
        $seat_number = $prefix . $i; // Creates "A1", "A2", "B1", "B55", etc.
        
        // Bind parameters and execute
        $stmt->bind_param("is", $location_id, $seat_number);
        $stmt->execute();
        
        $total_inserted++;
    }
}

$stmt->close();
$conn->close();

echo "<h2>Success!</h2>";
echo "Finished populating database.<br>";
echo "Total seats inserted: " . $total_inserted;
?>