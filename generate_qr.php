<?php
/**
 * This script regenerates QR codes for all existing users
 * Run this ONCE after moving to the production server
 */
require 'db_connect.php';
require_once('lib/phpqrcode/qrlib.php');

// Create QR code directory if it doesn't exist
$qr_dir = 'assets/qrcodes/';
if (!file_exists($qr_dir)) {
    mkdir($qr_dir, 0777, true);
}

// Detect the server URL dynamically
// This will work on both localhost and production server
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$script_path = dirname($_SERVER['PHP_SELF']);

// Clean up the path (remove trailing slashes)
$base_path = rtrim($protocol . $host . $script_path, '/');

echo "<h2>QR Code Regeneration Tool</h2>";
echo "<p>Base URL: <strong>$base_path</strong></p>";
echo "<hr>";

// Get all unique user IDs from bookings
$stmt = $conn->prepare("SELECT DISTINCT user_id FROM bookings WHERE user_id IS NOT NULL");
$stmt->execute();
$result = $stmt->get_result();

$generated = 0;
$skipped = 0;

while ($row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];
    $qr_filename = 'qr_user_' . $user_id . '.png';
    $qr_path = $qr_dir . $qr_filename;
    
    // URL for viewing all bookings
    $bookings_url = $base_path . '/user_bookings.php?user_id=' . $user_id;
    
    // Always delete old QR code to force regeneration with correct URL
    if (file_exists($qr_path)) {
        unlink($qr_path);
        echo "<p>🗑️ Deleted old QR code for User ID: $user_id</p>";
    }
    
    // Generate new QR code with correct server URL
    QRcode::png($bookings_url, $qr_path);
    
    echo "<p>✅ Generated QR code for User ID: $user_id</p>";
    echo "<p>   URL: $bookings_url</p>";
    $generated++;
}

echo "<hr>";
echo "<h3>Complete!</h3>";
echo "<p>Generated $generated QR codes</p>";
echo "<p><a href='profile.php'>Go to Profile</a></p>";

$stmt->close();
$conn->close();
?>

