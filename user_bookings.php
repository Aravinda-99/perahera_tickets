<?php
require 'db_connect.php';

if (!isset($_GET['user_id'])) {
    die("User ID not provided.");
}

$user_id = $_GET['user_id'];

// Fetch all bookings for the user
$stmt = $conn->prepare("SELECT 
                            b.reference_number, 
                            b.customer_name, 
                            s.seat_number,
                            l.name AS location_name,
                            b.booking_time,
                            p.payment_status,
                            p.amount_paid
                        FROM 
                            bookings AS b
                        JOIN 
                            seats AS s ON b.seat_id = s.id
                        JOIN 
                            locations AS l ON s.location_id = l.id
                        LEFT JOIN
                            payments AS p ON b.id = p.booking_id
                        WHERE 
                            b.user_id = ?
                        ORDER BY 
                            b.booking_time DESC");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("No bookings found for this user.");
}

// Get user details
$stmt_user = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Details</title>
    <style>
        body { 
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; 
            background-color: #f4f4f4; 
            margin: 0;
            padding: 20px;
        }
        .bookings-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .user-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
        }
        .user-info p {
            margin: 5px 0;
            color: #555;
        }
        .booking-card {
            background: #fff;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
            border-left: 4px solid #5c67f2;
        }
        .booking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .reference-number {
            color: #d9534f;
            font-weight: bold;
            font-size: 1.1em;
        }
        .payment-status {
            padding: 6px 12px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: bold;
        }
        .status-completed {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .status-pending {
            background-color: #fcf8e3;
            color: #8a6d3b;
        }
        .booking-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .detail-item {
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
        }
        .detail-value {
            color: #333;
            font-size: 1em;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #5c67f2;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="bookings-container">
        <h1>Your Booking Details</h1>
        
        <div class="user-info">
            <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>

        <?php while ($booking = $result->fetch_assoc()): ?>
            <div class="booking-card">
                <div class="booking-header">
                    <div class="reference-number"><?php echo htmlspecialchars($booking['reference_number']); ?></div>
                    <span class="payment-status status-<?php echo strtolower($booking['payment_status'] ?? 'pending'); ?>">
                        <?php echo ucfirst(htmlspecialchars($booking['payment_status'] ?? 'pending')); ?>
                    </span>
                </div>
                <div class="booking-details">
                    <div class="detail-item">
                        <div class="detail-label">Name</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Location</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['location_name']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Seat Number</div>
                        <div class="detail-value"><?php echo htmlspecialchars($booking['seat_number']); ?></div>
                    </div>
                    <?php if (!empty($booking['amount_paid'])): ?>
                    <div class="detail-item">
                        <div class="detail-label">Amount Paid</div>
                        <div class="detail-value">$ <?php echo number_format($booking['amount_paid'], 2); ?></div>
                    </div>
                    <?php endif; ?>
                    <div class="detail-item">
                        <div class="detail-label">Booking Time</div>
                        <div class="detail-value"><?php echo date('Y-m-d H:i', strtotime($booking['booking_time'])); ?></div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>

