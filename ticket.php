<?php
require 'db_connect.php';
if (!isset($_GET['ref'])) {
    die("Reference number not found.");
}
$reference_number = $_GET['ref'];

// SQL Query remains the same
$stmt = $conn->prepare("SELECT 
                            b.reference_number, 
                            b.customer_name, 
                            b.qr_code_path, 
                            s.seat_number,
                            l.name AS location_name 
                        FROM 
                            bookings AS b
                        JOIN 
                            seats AS s ON b.seat_id = s.id
                        JOIN 
                            locations AS l ON s.location_id = l.id
                        WHERE 
                            b.reference_number = ?");

$stmt->bind_param("s", $reference_number);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Invalid reference number.");
}
$ticket = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <title>Your Ticket</title> 
    <style>
        /* Added a generic font-family for English */
        body { 
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; 
            background-color: #f4f4f4; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .ticket { 
            width: 350px; 
            background: #fff; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
            text-align: center; 
            border-top: 5px solid #5c67f2; 
        }
        h2 { 
            color: #333; 
        }
        p { color: #555; 
            line-height: 1.6; 
        }
        .qr-code { 
            margin-top: 20px; 
        }
        .ref-number { 
            font-size: 1.2em; 
            font-weight: bold; 
            color: #d9534f; 
            margin: 15px 0; 
        }
    </style>
</head>
<body>
<div class="ticket">
    <h2>Ticket Booked Successfully!</h2>
    <p>Please bring these details with you when you come to the event.</p>
    <hr>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($ticket['customer_name']); ?></p>
    <p><strong>Location:</strong> <?php echo htmlspecialchars($ticket['location_name']); ?></p>
    
    <p><strong>Seat Number:</strong> <?php echo htmlspecialchars($ticket['seat_number']); ?></p>
    
    <p><strong>Reference Number:</strong></p>
    <div class="ref-number"><?php echo htmlspecialchars($ticket['reference_number']); ?></div>
    <div class="qr-code">
        <?php if (!empty($ticket['qr_code_path'])): ?>
            <img src="<?php echo htmlspecialchars($ticket['qr_code_path']); ?>" alt="QR Code">
        <?php else: ?>
            <p>(QR Code will be generated later)</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>