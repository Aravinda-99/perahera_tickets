<?php
require 'db_connect.php';
require_once 'session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get payment data
$location_id = $_POST['location_id'] ?? '';
$seat_id = $_POST['seat_id'] ?? '';
$customer_name = $_POST['customer_name'] ?? '';
$customer_phone = $_POST['customer_phone'] ?? '';
$ticket_price = $_POST['ticket_price'] ?? 35.00;
$card_number = $_POST['card_number'] ?? '';
$cardholder_name = $_POST['cardholder_name'] ?? '';
$expiry_month = $_POST['expiry_month'] ?? '';
$expiry_year = $_POST['expiry_year'] ?? '';
$cvv = $_POST['cvv'] ?? '';
$email = $_POST['email'] ?? '';

// Validate required fields
if (empty($seat_id) || empty($customer_name) || empty($customer_phone)) {
    header("Location: booking_form.php");
    exit();
}

// Simulate payment processing
$payment_success = false;
$payment_error = '';

// Basic validation
if (empty($card_number) || empty($cardholder_name) || empty($expiry_month) || empty($expiry_year) || empty($cvv) || empty($email)) {
    $payment_error = 'Please fill in all payment fields';
} else {
    // Simulate payment gateway processing
    // In a real application, you would integrate with Stripe, PayPal, etc.
    
    // Check if card number looks valid (basic check)
    $card_number_clean = preg_replace('/\s+/', '', $card_number);
    if (strlen($card_number_clean) >= 13 && strlen($card_number_clean) <= 19) {
        // Simulate successful payment (90% success rate for demo)
        $payment_success = (rand(1, 10) <= 9);
        
        if (!$payment_success) {
            $payment_error = 'Payment declined. Please check your card details and try again.';
        }
    } else {
        $payment_error = 'Invalid card number format';
    }
}

if ($payment_success) {
    // Payment successful - create booking
    try {
        $conn->begin_transaction();
        
        // Generate reference number
        $reference_number = 'PERA-' . date('Y') . '-' . date('m') . '-' . str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
        
        // Insert booking
        $stmt = $conn->prepare("
            INSERT INTO bookings (user_id, seat_id, customer_name, customer_phone, customer_email, reference_number, booking_time, payment_status, amount_paid) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), 'completed', ?)
        ");
        $stmt->bind_param("iissssd", $_SESSION['user_id'], $seat_id, $customer_name, $customer_phone, $email, $reference_number, $ticket_price);
        $stmt->execute();
        
        // Update seat status to booked
        $stmt2 = $conn->prepare("UPDATE seats SET status = 'booked' WHERE id = ?");
        $stmt2->bind_param("i", $seat_id);
        $stmt2->execute();
        
        $conn->commit();
        
        // Redirect to success page
        header("Location: payment_success.php?ref=" . urlencode($reference_number));
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $payment_error = 'Booking failed. Please try again.';
    }
}

// If we reach here, payment failed
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Perahera Tickets</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Sinhala', sans-serif;
            background-image: url('assets/images/perahera2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            line-height: 1.6;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            max-width: 500px;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-top: 5px solid #e74c3c;
        }
        .error-icon {
            font-size: 60px;
            color: #e74c3c;
            margin-bottom: 20px;
        }
        h2 { 
            color: #e74c3c; 
            font-weight: 700; 
            margin-bottom: 20px; 
        }
        .error-message {
            background: #fdf2f2;
            color: #e74c3c;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            border: 1px solid #fecaca;
        }
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(45deg, #3498db, #2980b9);
            color: white;
        }
        .btn-secondary {
            background: #95a5a6;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-icon">❌</div>
        <h2>Payment Failed</h2>
        
        <div class="error-message">
            <strong>Error:</strong> <?php echo htmlspecialchars($payment_error); ?>
        </div>
        
        <div class="button-group">
            <a href="payment.php" class="btn btn-primary">Try Again</a>
            <a href="booking_form.php" class="btn btn-secondary">Back to Booking</a>
        </div>
    </div>
</body>
</html>
