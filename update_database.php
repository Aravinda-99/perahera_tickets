<?php
require 'db_connect.php';

function columnExists(mysqli $conn, string $table, string $column): bool {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?");
    $stmt->bind_param("ss", $table, $column);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return ((int)$res['cnt']) > 0;
}

try {
    $added = [];
    
    // customer_email
    if (!columnExists($conn, 'bookings', 'customer_email')) {
        $sql = "ALTER TABLE bookings ADD COLUMN customer_email VARCHAR(255) AFTER customer_phone";
        if ($conn->query($sql) === TRUE) { $added[] = 'customer_email'; }
    }

    // payment_status
    if (!columnExists($conn, 'bookings', 'payment_status')) {
        $sql = "ALTER TABLE bookings ADD COLUMN payment_status ENUM('pending','completed','failed') DEFAULT 'pending' AFTER booking_time";
        if ($conn->query($sql) === TRUE) { $added[] = 'payment_status'; }
    }

    // amount_paid
    if (!columnExists($conn, 'bookings', 'amount_paid')) {
        $sql = "ALTER TABLE bookings ADD COLUMN amount_paid DECIMAL(10,2) DEFAULT 0.00 AFTER payment_status";
        if ($conn->query($sql) === TRUE) { $added[] = 'amount_paid'; }
    }

    if (!empty($added)) {
        echo "✅ Added columns: " . implode(', ', $added) . "<br>";
    } else {
        echo "ℹ️ No changes needed. Columns already exist.<br>";
    }

    // Show table structure
    $result = $conn->query("DESCRIBE bookings");
    echo "<br><strong>Current bookings table structure:</strong><br>";
    while ($row = $result->fetch_assoc()) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")<br>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}

$conn->close();
?>
