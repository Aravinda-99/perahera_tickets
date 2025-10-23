<?php
require 'db_connect.php';
require_once 'session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Get booking details from POST data
$location_id = $_POST['location_id'] ?? '';
$seat_id = $_POST['seat_id'] ?? '';
$customer_name = $_POST['customer_name'] ?? '';
$customer_phone = $_POST['customer_phone'] ?? '';
$referral_code = $_POST['referral_code'] ?? '';
$discount_amount = $_POST['discount_amount'] ?? 0;
$agent_name = $_POST['agent_name'] ?? '';

// Validate required fields
if (empty($location_id) || empty($seat_id) || empty($customer_name) || empty($customer_phone)) {
    header("Location: booking_form.php");
    exit();
}

// Get location and seat details
$stmt = $conn->prepare("
    SELECT l.name as location_name, s.seat_number 
    FROM locations l 
    JOIN seats s ON l.id = s.location_id 
    WHERE l.id = ? AND s.id = ?
");
$stmt->bind_param("ii", $location_id, $seat_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: booking_form.php");
    exit();
}

$booking_details = $result->fetch_assoc();
$ticket_price = 35.00; // $35 per ticket
$original_price = $ticket_price;
$discount_percentage = floatval($discount_amount);
$discount_value = ($ticket_price * $discount_percentage) / 100;
$final_price = $ticket_price - $discount_value;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Perahera Tickets</title>
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
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-top: 5px solid rgb(255, 198, 40);
        }
        h2 { 
            text-align: center; 
            color: #D35400; 
            font-weight: 700; 
            margin-bottom: 25px; 
            letter-spacing: 1px; 
        }
        .booking-summary {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .summary-row:last-child {
            border-bottom: none;
            font-weight: bold;
            font-size: 18px;
            color: #D35400;
        }
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 700; 
            color: #555; 
        }
        input[type="text"], input[type="email"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        .card-row {
            display: flex;
            gap: 15px;
        }
        .card-row .form-group {
            flex: 1;
        }
        .expiry-row {
            display: flex;
            gap: 10px;
        }
        .expiry-row select {
            flex: 1;
        }
        .payment-button {
            width: 100%;
            background: linear-gradient(45deg,rgb(174, 100, 39),rgb(204, 141, 46));
            color: white;
            padding: 15px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 700;
            text-transform: uppercase;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .payment-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(204, 64, 46, 0.4);
        }
        .payment-button:disabled {
            background: #bdc3c7;
            cursor: not-allowed;
            transform: none;
        }
        .back-button {
            background: #95a5a6;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }
        .back-button:hover {
            background: #7f8c8d;
        }
        .security-info {
            background: #e8f5e8;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 14px;
            color:rgb(143, 12, 40);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>💳 Payment Details</h2>
        
        <a href="booking_form.php" class="back-button">← Back to Booking</a>
        
        <div class="booking-summary">
            <h3>Booking Summary</h3>
            <div class="summary-row">
                <span>Location:</span>
                <span><?php echo htmlspecialchars($booking_details['location_name']); ?></span>
            </div>
            <div class="summary-row">
                <span>Seat Number:</span>
                <span><?php echo htmlspecialchars($booking_details['seat_number']); ?></span>
            </div>
            <div class="summary-row">
                <span>Customer Name:</span>
                <span><?php echo htmlspecialchars($customer_name); ?></span>
            </div>
            <div class="summary-row">
                <span>Phone:</span>
                <span><?php echo htmlspecialchars($customer_phone); ?></span>
            </div>
            <?php if (!empty($referral_code) && $discount_percentage > 0): ?>
            <div class="summary-row">
                <span>Referral Code:</span>
                <span><?php echo htmlspecialchars($referral_code); ?> (<?php echo htmlspecialchars($agent_name); ?>)</span>
            </div>
            <div class="summary-row">
                <span>Original Price:</span>
                <span>$<?php echo number_format($original_price, 2); ?></span>
            </div>
            <div class="summary-row" style="color: #27ae60;">
                <span>Discount (<?php echo $discount_percentage; ?>%):</span>
                <span>-$<?php echo number_format($discount_value, 2); ?></span>
            </div>
            <?php endif; ?>
            <div class="summary-row">
                <span><?php echo ($discount_percentage > 0) ? 'Final Price:' : 'Ticket Price:'; ?></span>
                <span>$<?php echo number_format($final_price, 2); ?></span>
            </div>
        </div>

        <form action="process_payment.php" method="POST" id="payment-form">
            <!-- Hidden fields to pass booking data -->
            <input type="hidden" name="location_id" value="<?php echo htmlspecialchars($location_id); ?>">
            <input type="hidden" name="seat_id" value="<?php echo htmlspecialchars($seat_id); ?>">
            <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($customer_name); ?>">
            <input type="hidden" name="customer_phone" value="<?php echo htmlspecialchars($customer_phone); ?>">
            <input type="hidden" name="ticket_price" value="<?php echo $final_price; ?>">
            <input type="hidden" name="referral_code" value="<?php echo htmlspecialchars($referral_code); ?>">
            <input type="hidden" name="discount_amount" value="<?php echo $discount_amount; ?>">
            <input type="hidden" name="agent_name" value="<?php echo htmlspecialchars($agent_name); ?>">

            <div class="form-group">
                <label for="card_number">Card Number:</label>
                <input type="text" id="card_number" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19" required>
            </div>

            <div class="form-group">
                <label for="cardholder_name">Cardholder Name:</label>
                <input type="text" id="cardholder_name" name="cardholder_name" placeholder="John Doe" required>
            </div>

            <div class="card-row">
                <div class="form-group">
                    <label for="expiry_month">Expiry Month:</label>
                    <select id="expiry_month" name="expiry_month" required>
                        <option value="">Month</option>
                        <?php for($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo sprintf('%02d', $i); ?>"><?php echo sprintf('%02d', $i); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="expiry_year">Expiry Year:</label>
                    <select id="expiry_year" name="expiry_year" required>
                        <option value="">Year</option>
                        <?php 
                        $current_year = date('Y');
                        for($i = $current_year; $i <= $current_year + 10; $i++): 
                        ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cvv">CVV:</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4" required>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" placeholder="john@example.com" required>
            </div>

            <button type="submit" class="payment-button" id="pay-button">
                💳 Pay $<?php echo number_format($final_price, 2); ?>
            </button>
        </form>

        <div class="security-info">
            🔒 Your payment information is secure and encrypted. We use industry-standard security measures to protect your data.
        </div>
    </div>

    <script>
        // Format card number with spaces
        document.getElementById('card_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // Only allow numbers for CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
        });

        // Form validation
        document.getElementById('payment-form').addEventListener('submit', function(e) {
            const cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
            const cvv = document.getElementById('cvv').value;
            
            if (cardNumber.length < 13 || cardNumber.length > 19) {
                alert('Please enter a valid card number');
                e.preventDefault();
                return;
            }
            
            if (cvv.length < 3 || cvv.length > 4) {
                alert('Please enter a valid CVV');
                e.preventDefault();
                return;
            }

            // Disable button to prevent double submission
            document.getElementById('pay-button').disabled = true;
            document.getElementById('pay-button').textContent = 'Processing...';
        });
    </script>
</body>
</html>
