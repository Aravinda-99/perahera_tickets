<?php
require 'db_connect.php';

header('Content-Type: application/json');

if (isset($_GET['code'])) {
    $referral_code = trim($_GET['code']);
    
    if (empty($referral_code)) {
        echo json_encode(['valid' => false, 'message' => 'Referral code is required']);
        exit;
    }
    
    // Check if the referral code exists in the agent table
    $stmt = $conn->prepare("SELECT ag_name, discount FROM agent WHERE ag_code = ?");
    $stmt->bind_param("s", $referral_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $agent = $result->fetch_assoc();
        echo json_encode([
            'valid' => true,
            'agent_name' => $agent['ag_name'],
            'discount' => $agent['discount'],
            'message' => 'Valid referral code'
        ]);
    } else {
        echo json_encode(['valid' => false, 'message' => 'Invalid referral code']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['valid' => false, 'message' => 'No referral code provided']);
}

$conn->close();
?>
