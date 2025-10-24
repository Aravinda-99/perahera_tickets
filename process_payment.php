<?php
require 'db_connect.php';
require_once 'session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get payment data
$location_id = $_POST['location_id'] ?? '';
$selected_seats_data = $_POST['selected_seats'] ?? '';
$customer_name = $_POST['customer_name'] ?? '';
$customer_phone = $_POST['customer_phone'] ?? '';
$ticket_price = $_POST['ticket_price'] ?? 35.00;
$referral_code = $_POST['referral_code'] ?? '';
$discount_amount = $_POST['discount_amount'] ?? 0;
$agent_name = $_POST['agent_name'] ?? '';
$card_number = $_POST['card_number'] ?? '';
$cardholder_name = $_POST['cardholder_name'] ?? '';
$expiry_month = $_POST['expiry_month'] ?? '';
$expiry_year = $_POST['expiry_year'] ?? '';
$cvv = $_POST['cvv'] ?? '';
$email = $_POST['email'] ?? '';

// Parse selected seats
$selected_seats = json_decode($selected_seats_data, true);
if (!$selected_seats || !is_array($selected_seats) || empty($selected_seats)) {
    header("Location: booking_form.php");
    exit();
}

// Validate required fields
if (empty($customer_name) || empty($customer_phone)) {
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
        
        // Generate unique reference number for each seat
        $reference_number_base = 'PERA-' . date('Y') . '-' . date('m') . '-' . str_pad(rand(1, 9999999), 7, '0', STR_PAD_LEFT);
        
        // Insert bookings for each seat
        $booking_ids = [];
        foreach ($selected_seats as $index => $seat) {
            $seat_number = $seat['number'];
            $location_id_int = intval($location_id);
            
            // First, ensure the seat exists in the database
            $stmt_check = $conn->prepare("SELECT id FROM seats WHERE location_id = ? AND seat_number = ?");
            $stmt_check->bind_param("is", $location_id_int, $seat_number);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            
            if ($result->num_rows > 0) {
                // Seat exists, get its ID
                $seat_data = $result->fetch_assoc();
                $seat_id = $seat_data['id'];
            } else {
                // Seat doesn't exist, create it
                $stmt_create = $conn->prepare("INSERT INTO seats (location_id, seat_number, status) VALUES (?, ?, 'available')");
                $stmt_create->bind_param("is", $location_id_int, $seat_number);
                if (!$stmt_create->execute()) {
                    throw new Exception("Failed to create seat " . $seat_number . ": " . $stmt_create->error);
                }
                $seat_id = $conn->insert_id;
                $stmt_create->close();
            }
            $stmt_check->close();
            
            // Create unique reference number for each seat
            $reference_number = $reference_number_base . '-' . ($index + 1);
            
            $stmt = $conn->prepare("
                INSERT INTO bookings (user_id, seat_id, customer_name, customer_phone, customer_email, reference_number, booking_time) 
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt->bind_param("iissss", $_SESSION['user_id'], $seat_id, $customer_name, $customer_phone, $email, $reference_number);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to insert booking for seat " . $seat_number . ": " . $stmt->error);
            }
            
            $booking_ids[] = $conn->insert_id;
            $stmt->close();
        }
        
        // Insert payment record (one payment for all seats)
        if (empty($booking_ids)) {
            throw new Exception("No booking IDs generated - cannot create payment record");
        }
        
        // Verify the booking_id exists before creating payment
        $stmt_check_booking = $conn->prepare("SELECT id FROM bookings WHERE id = ?");
        $stmt_check_booking->bind_param("i", $booking_ids[0]);
        $stmt_check_booking->execute();
        $result_check = $stmt_check_booking->get_result();
        
        if ($result_check->num_rows === 0) {
            throw new Exception("Booking ID " . $booking_ids[0] . " does not exist in bookings table");
        }
        $stmt_check_booking->close();
        
        $stmt2 = $conn->prepare("
            INSERT INTO payments (booking_id, user_id, payment_status, amount_paid, referral_code, agent_name, discount_amount, payment_date) 
            VALUES (?, ?, 'completed', ?, ?, ?, ?, NOW())
        ");
        $stmt2->bind_param("iidssd", $booking_ids[0], $_SESSION['user_id'], $ticket_price, $referral_code, $agent_name, $discount_amount);
        
        if (!$stmt2->execute()) {
            throw new Exception("Failed to insert payment record: " . $stmt2->error);
        }
        $stmt2->close();
        
        // Update all seat statuses to booked
        $seat_numbers = array_column($selected_seats, 'number');
        if (count($seat_numbers) > 0) {
            $placeholders = str_repeat('?,', count($seat_numbers) - 1) . '?';
            $stmt3 = $conn->prepare("UPDATE seats SET status = 'booked' WHERE location_id = ? AND seat_number IN ($placeholders)");
            $params = array_merge([$location_id_int], $seat_numbers);
            $stmt3->bind_param(str_repeat('s', count($params)), ...$params);
            
            if (!$stmt3->execute()) {
                throw new Exception("Failed to update seat statuses: " . $stmt3->error);
            }
            
            if ($stmt3->affected_rows !== count($seat_numbers)) {
                throw new Exception("Expected to update " . count($seat_numbers) . " seats, but only updated " . $stmt3->affected_rows);
            }
            
            $stmt3->close();
        }
        
        // Decrement ref_count if referral code was used
        if (!empty($referral_code)) {
            $stmt4 = $conn->prepare("UPDATE agent SET ref_count = ref_count - 1 WHERE ag_code = ? AND ref_count > 0");
            $stmt4->bind_param("s", $referral_code);
            $stmt4->execute();
            
            if ($stmt4->affected_rows === 0) {
                // This shouldn't happen if validation worked, but just in case
                error_log("Warning: Could not decrement ref_count for referral code: " . $referral_code);
            }
            $stmt4->close();
        }
        
        $conn->commit();
        
        // Redirect to success page
        header("Location: payment_success.php?ref=" . urlencode($reference_number_base));
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Booking error: " . $e->getMessage());
        $payment_error = 'Booking failed: ' . $e->getMessage();
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
