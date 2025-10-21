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
// Fetch user's bookings from the database
// We JOIN bookings -> seats -> locations
$stmt = $conn->prepare(
    "SELECT 
        b.reference_number, 
        b.booking_time, 
        l.name AS location_name,
        s.seat_number 
     FROM 
        bookings b 
     JOIN 
        seats s ON b.seat_id = s.id
     JOIN 
        locations l ON s.location_id = l.id 
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
            background-color: #FAF7F0;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px 0; /* Add some padding for top and bottom */
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
    </style>
</head>
<body>
    <div class="container">
        <h2>My Profile</h2>
        <p class="profile-info"><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        
        <h3>My Bookings</h3>
        <?php if ($result->num_rows > 0): ?>
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
                    <?php while($row = $result->fetch_assoc()): ?>
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
        <?php else: ?>
            <p class="no-bookings">You have not booked any tickets yet.</p>
        <?php endif; ?>
    </div>
</body>