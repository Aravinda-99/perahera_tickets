<?php
// session_start() is now only in header.php
require 'db_connect.php';
include 'header.php';

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// *** වෙනස් කරන ලද SQL QUERY එක ***
// Fetch user's bookings from the database with payment information
// We JOIN bookings -> seats -> locations -> payments
$stmt = $conn->prepare(
    "SELECT 
        b.reference_number, 
        b.booking_time, 
        l.name AS location_name,
        s.seat_number,
        p.payment_status,
        p.amount_paid,
        p.referral_code,
        p.agent_name,
        p.discount_amount
     FROM 
        bookings b 
     JOIN 
        seats s ON b.seat_id = s.id
     JOIN 
        locations l ON s.location_id = l.id 
     LEFT JOIN
        payments p ON b.id = p.booking_id
     WHERE 
        b.user_id = ? 
     ORDER BY 
        b.booking_time DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<head>
    <title>My Profile</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Your CSS styles are correct, no changes needed here */
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
            padding: 0; /* Remove all padding */
        }
        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #FFFFFF;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-top: 5px solid #FF9933;
        }
        h2, h3 {
            color: #D35400;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
        h2 {
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .profile-info {
            font-size: 1.1em;
            margin-bottom: 25px;
        }
        .booking-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .booking-table th, .booking-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .booking-table th {
            background-color: #FFF8E1; /* Light yellow from theme */
            color: #D35400;
            font-weight: 700;
        }
        .booking-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .booking-table a {
            color: #D35400;
            text-decoration: none;
            font-weight: 700;
        }
        .booking-table a:hover {
            text-decoration: underline;
        }
        .no-bookings {
            color: #888;
            background-color: #f9f9f9;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
        }

        /* Mobile Responsive Styles */
        @media screen and (max-width: 768px) {
            body {
                padding: 0;
            }

            .container {
                margin: 20px 10px;
                padding: 20px 15px;
                border-radius: 8px;
            }

            h2 {
                font-size: 1.5em;
                margin-bottom: 15px;
            }

            h3 {
                font-size: 1.2em;
                margin-top: 20px;
            }

            .profile-info {
                font-size: 1em;
                margin-bottom: 20px;
                word-break: break-word;
            }

            /* Hide table on mobile and show card layout */
            .booking-table {
                display: none;
            }

            /* Card layout for mobile */
            .booking-card {
                background: #fff;
                border: 1px solid #ddd;
                border-left: 4px solid #FF9933;
                border-radius: 8px;
                padding: 15px;
                margin-bottom: 15px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .booking-card-row {
                margin-bottom: 10px;
                padding-bottom: 8px;
                border-bottom: 1px solid #f0f0f0;
            }

            .booking-card-row:last-child {
                border-bottom: none;
                margin-bottom: 0;
            }

            .booking-card-label {
                font-weight: 700;
                color: #D35400;
                display: block;
                margin-bottom: 5px;
                font-size: 0.9em;
            }

            .booking-card-value {
                color: #333;
                display: block;
                font-size: 0.95em;
            }

            .booking-card-value a {
                color: #D35400;
                text-decoration: none;
                font-weight: 700;
                display: inline-block;
                padding: 8px 15px;
                background-color: #FFF8E1;
                border-radius: 4px;
                margin-top: 5px;
            }

            .booking-card-value a:hover {
                background-color: #FFE082;
            }

            /* QR Code section responsive */
            .qr-section {
                padding: 15px !important;
                margin: 15px 0 !important;
            }

            .qr-section h3 {
                font-size: 1.1em;
            }

            .qr-section img {
                max-width: 180px !important;
            }

            .qr-section p {
                font-size: 0.9em;
            }

            .qr-section a {
                font-size: 0.9em !important;
                padding: 8px 15px !important;
            }
        }

        @media screen and (max-width: 480px) {
            .container {
                margin: 10px 5px;
                padding: 15px 10px;
            }

            h2 {
                font-size: 1.3em;
            }

            .qr-section img {
                max-width: 150px !important;
            }
        }

        /* Show cards only on mobile */
        .booking-cards {
            display: none;
        }

        @media screen and (max-width: 768px) {
            .booking-cards {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>My Profile</h2>
        <p class="profile-info"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        
        <?php 
        // Check if user has a QR code, if not generate it
        $qr_path = 'assets/qrcodes/qr_user_' . $user_id . '.png';
        
        // Create QR code directory if it doesn't exist
        if (!file_exists('assets/qrcodes')) {
            mkdir('assets/qrcodes', 0777, true);
        }
        
        // Generate QR code if it doesn't exist
        if (!file_exists($qr_path)) {
            require_once('lib/phpqrcode/qrlib.php');
            
            // URL for viewing all bookings - Use production server URL
            $bookings_url = 'https://testing.sltdigitalweb.lk/perahera_tickets/user_bookings.php?user_id=' . $user_id;
            
            // Generate QR code
            QRcode::png($bookings_url, $qr_path);
        }
        ?>
        <div class="qr-section" style="text-align: center; margin: 20px 0; padding: 20px; background-color: #f9f9f9; border-radius: 8px;">
            <h3 style="margin-top: 0;">Your QR Code</h3>
            <p style="color: #666; margin-bottom: 15px;">Scan this QR code to view all your booking details</p>
            <img src="<?php echo $qr_path; ?>" alt="QR Code" style="max-width: 200px; margin-bottom: 15px;">
            <br>
            <a href="<?php echo $qr_path; ?>" download="my_qr_code.png" style="display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; font-weight: bold;">
                Download QR Code
            </a>
        </div>
        
        <h3>My Bookings</h3>
        <?php if ($result->num_rows > 0): ?>
            <!-- Desktop Table View -->
            <table class="booking-table">
                <thead>
                    <tr>
                        <th>Reference Number</th>
                        <th>Location</th>
                        <th>Seat Number</th>
                        <th>Booking Date/Time</th>
                        <th>Ticket</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Store results in array for reuse
                    $bookings = [];
                    while($row = $result->fetch_assoc()): 
                        $bookings[] = $row;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['reference_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['location_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['seat_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['booking_time']); ?></td>
                        <td><a href="ticket.php?ref=<?php echo htmlspecialchars($row['reference_number']); ?>" target="_blank">View Ticket</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Mobile Card View -->
            <div class="booking-cards">
                <?php foreach($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-card-row">
                        <span class="booking-card-label">Reference Number</span>
                        <span class="booking-card-value"><?php echo htmlspecialchars($booking['reference_number']); ?></span>
                    </div>
                    <div class="booking-card-row">
                        <span class="booking-card-label">Location</span>
                        <span class="booking-card-value"><?php echo htmlspecialchars($booking['location_name']); ?></span>
                    </div>
                    <div class="booking-card-row">
                        <span class="booking-card-label">Seat Number</span>
                        <span class="booking-card-value"><?php echo htmlspecialchars($booking['seat_number']); ?></span>
                    </div>
                    <div class="booking-card-row">
                        <span class="booking-card-label">Booking Date/Time</span>
                        <span class="booking-card-value"><?php echo htmlspecialchars($booking['booking_time']); ?></span>
                    </div>
                    <div class="booking-card-row">
                        <span class="booking-card-label">Ticket</span>
                        <span class="booking-card-value">
                            <a href="ticket.php?ref=<?php echo htmlspecialchars($booking['reference_number']); ?>" target="_blank">View Ticket</a>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-bookings">You have not booked any tickets yet.</p>
        <?php endif; ?>
    </div>
</body>