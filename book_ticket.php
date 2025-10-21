<?php
session_start();
require 'db_connect.php';

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// 2. Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Get all data from session and form
    $user_id = $_SESSION['user_id'];
    $customer_email = $_SESSION['user_email']; // Make sure this is set in session
    
    // We ONLY need the seat_id from the form.
    $seat_id = $_POST['seat_id']; // This is the ID from 'seats' table (e.g., 101)
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];

    // These will be fetched from the DB for the reference number
    $seat_number_text = "";
    $location_id_from_db = "";

    // --- Start Database Transaction ---
    $conn->begin_transaction();

    try {
        
        // 4. (Step 1 of Transaction)
        // Lock the seat: Try to update the seat's status from 'available' to 'booked'
        $stmt_update_seat = $conn->prepare("UPDATE seats SET status = 'booked' WHERE id = ? AND status = 'available'");
        $stmt_update_seat->bind_param("i", $seat_id);
        $stmt_update_seat->execute();

        // 5. Check if the seat was successfully locked
        if ($stmt_update_seat->affected_rows > 0) {
            
            // 6. (Step 2 of Transaction)
            // Get the seat_number and location_id for creating the reference_number
            $stmt_get_details = $conn->prepare("SELECT seat_number, location_id FROM seats WHERE id = ?");
            $stmt_get_details->bind_param("i", $seat_id);
            $stmt_get_details->execute();
            $result = $stmt_get_details->get_result();
            
            if ($result->num_rows === 0) {
                 // This should never happen if the update worked
                throw new Exception("Could not find seat details for the booked seat.");
            }
            
            $seat_details = $result->fetch_assoc();
            $seat_number_text = $seat_details['seat_number'];    // e.g., "A1"
            $location_id_from_db = $seat_details['location_id']; // e.g., 1
            $stmt_get_details->close();

            // 7. (Step 3 of Transaction)
            // Create the booking record using the 'seat_id'
            
            // Use the details we just fetched for the reference number
            $reference_number = "PERA-" . $location_id_from_db . "-" . $seat_number_text . "-" . time();
            $qr_code_path = ""; // QR code generation will be added later

            // **RECOMMENDED INSERT QUERY:**
            // Inserting the 'seat_id' (Foreign Key) into the 'bookings' table.
            // Note: We are NOT saving location_id or seat_number text here.
            $stmt_insert = $conn->prepare("INSERT INTO bookings (user_id, seat_id, customer_name, customer_email, customer_phone, reference_number, qr_code_path) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            // Note: bind_param "iisssss" - 'i' for user_id, 'i' for seat_id
            $stmt_insert->bind_param("iisssss", $user_id, $seat_id, $customer_name, $customer_email, $customer_phone, $reference_number, $qr_code_path);
            
            if ($stmt_insert->execute()) {
                // 8. (Success!)
                // All queries worked. Commit the changes.
                $conn->commit();
                
                // 9. Redirect to the ticket page
                header("Location: ticket.php?ref=" . $reference_number);
                exit();
                
            } else {
                // Booking insert failed
                throw new Exception("Error creating booking record: " . $stmt_insert->error);
            }

        } else {
            // The UPDATE query affected 0 rows. Seat was already booked.
            throw new Exception("කණගාටුයි! ඔබ තේරූ ආසනය මීට මොහොතකට පෙර වෙනත් අයෙකු වෙන් කර ඇත. කරුණාකර නැවත උත්සහ කරන්න.");
        }

    } catch (Exception $e) {
        // 10. (Failure)
        // Something went wrong. Rollback all changes.
        $conn->rollback();
        
        // Show the error message
        echo "Booking Failed: " . $e->getMessage();
    }
    
    // Close statements and connection
    if (isset($stmt_update_seat)) $stmt_update_seat->close();
    if (isset($stmt_insert)) $stmt_insert->close();
    $conn->close();
}
?>