<?php
require 'db_connect.php';
require_once 'session.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$reference_number = $_GET['ref'] ?? '';

if (empty($reference_number)) {
    header("Location: profile.php");
    exit();
}

// Get booking details with payment information
$stmt = $conn->prepare("
    SELECT 
        b.*,
        l.name as location_name,
        s.seat_number,
        p.payment_status,
        p.amount_paid,
        p.referral_code,
        p.agent_name,
        p.discount_amount,
        p.payment_date
    FROM bookings b
    JOIN seats s ON b.seat_id = s.id
    JOIN locations l ON s.location_id = l.id
    LEFT JOIN payments p ON b.id = p.booking_id
    WHERE b.reference_number = ? AND b.user_id = ?
");
$stmt->bind_param("si", $reference_number, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: profile.php");
    exit();
}

$booking = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success - Perahera Tickets</title>
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
            max-width: 600px;
            text-align: center;
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-top: 5px solid rgb(218, 105, 12);
            margin-top: 50px;
            margin-bottom: 50px;
        }
        .success-icon {
            font-size: 60px;
            color: #27ae60;
            margin-bottom: 20px;
        }
        h2 { 
            color:rgb(174, 100, 39); 
            font-weight: 700; 
            margin-bottom: 30px; 
        }
        .ticket-card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 30px;
            border: 2px solid rgb(174, 104, 39);
            text-align: left;
        }
        .ticket-header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid rgb(151, 86, 25);
        }
        .ticket-title {
            font-size: 24px;
            font-weight: bold;
            color:rgb(174, 131, 39);
            margin-bottom: 5px;
        }
        .reference-number {
            font-size: 18px;
            font-weight: 600;
            color: #2c3e50;
        }
        .ticket-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .detail-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid rgb(174, 115, 39);
        }
        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .detail-value {
            font-size: 16px;
            color: #2c3e50;
            font-weight: 500;
        }
        .amount-paid {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-top: 20px;
        }
        .amount-label {
            font-size: 14px;
            color:rgb(174, 120, 39);
            margin-bottom: 5px;
        }
        .amount-value {
            font-size: 28px;
            font-weight: bold;
            color:rgb(174, 138, 39);
        }
        .button-group {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
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
            background: linear-gradient(45deg,rgb(174, 111, 39),rgb(214, 142, 83));
            color: white;
        }
        .btn-secondary {
            background:rgb(174, 111, 39);
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon"> <img src="assets/logo/correct.png" alt="Success Icon" width="100" height="100"></div>
        <h2>Payment Successful!</h2>
        
        <div class="ticket-card">
            <div class="ticket-header">
                <div class="ticket-title">PERAHERA TICKET</div>
                <div class="reference-number"><?php echo htmlspecialchars($booking['reference_number']); ?></div>
            </div>
            
            <div class="ticket-details">
                <div class="detail-item">
                    <div class="detail-label">Location</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['location_name']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Seat Number</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['seat_number']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Customer Name</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['customer_name']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Phone</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['customer_phone']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Email</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['customer_email']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Booking Date</div>
                    <div class="detail-value"><?php echo date('M d, Y', strtotime($booking['booking_time'])); ?></div>
                </div>
                <?php if (!empty($booking['referral_code']) && $booking['discount_amount'] > 0): ?>
                <div class="detail-item">
                    <div class="detail-label">Referral Code</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['referral_code']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Agent</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['agent_name']); ?></div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($booking['referral_code']) && $booking['discount_amount'] > 0): ?>
            <div class="discount-info" style="background: #d5f4e6; padding: 15px; border-radius: 8px; margin: 15px 0; text-align: center; border: 1px solid #27ae60;">
                <div style="color: #27ae60; font-weight: 600; font-size: 16px;">Discount Applied!</div>
                <div style="color: #27ae60; font-size: 14px; margin-top: 5px;">
                    You saved <?php echo $booking['discount_amount']; ?>% with referral code: <?php echo htmlspecialchars($booking['referral_code']); ?>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="amount-paid">
                <div class="amount-label">Amount Paid</div>
                <div class="amount-value">$<?php echo number_format($booking['amount_paid'], 2); ?></div>
            </div>
        </div>
        
        <div class="button-group">
            <a href="ticket.php?ref=<?php echo urlencode($booking['reference_number']); ?>" class="btn btn-primary" target="_blank">
                 View Ticket
            </a>
            <a href="profile.php" class="btn btn-secondary">
                 My Bookings
            </a>
        </div>
        
        <p style="margin-top: 30px; color:rgb(174, 115, 39); font-weight: 600;">
            ✔ Your ticket has been successfully booked and payment processed!
        </p>
    </div>

    <script>
        // Simple confetti effect
        function createConfetti() {
            const colors = ['#27ae60', '#2ecc71', '#3498db', '#e74c3c', '#f39c12'];
            const confettiCount = 50;
            
            for (let i = 0; i < confettiCount; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.style.position = 'fixed';
                    confetti.style.width = '10px';
                    confetti.style.height = '10px';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.top = '-10px';
                    confetti.style.zIndex = '1000';
                    confetti.style.pointerEvents = 'none';
                    confetti.style.borderRadius = '50%';
                    
                    document.body.appendChild(confetti);
                    
                    const animation = confetti.animate([
                        { transform: 'translateY(0px) rotate(0deg)', opacity: 1 },
                        { transform: `translateY(${window.innerHeight + 100}px) rotate(720deg)`, opacity: 0 }
                    ], {
                        duration: 3000,
                        easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
                    });
                    
                    animation.onfinish = () => confetti.remove();
                }, i * 50);
            }
        }
        
        // Start confetti after a short delay
        setTimeout(createConfetti, 500);
    </script>
</body>
</html>
