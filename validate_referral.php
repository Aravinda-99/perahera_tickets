<?php
require 'db_connect.php';

header('Content-Type: application/json');

if (isset($_GET['code'])) {
    $referral_code = trim($_GET['code']);
    
    if (empty($referral_code)) {
        echo json_encode(['valid' => false, 'message' => 'Referral code is required']);
        exit;
    }
    
    // Check if the referral code exists in the agent table and has remaining referrals
    $stmt = $conn->prepare("SELECT ag_name, discount, ref_count FROM agent WHERE ag_code = ? AND ref_count > 0");
    $stmt->bind_param("s", $referral_code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $agent = $result->fetch_assoc();
        echo json_encode([
            'valid' => true,
            'agent_name' => $agent['ag_name'],
            'discount' => $agent['discount'],
            'ref_count' => $agent['ref_count'],
            'message' => 'Valid referral code'
        ]);
    } else {
        // Check if the code exists but has no remaining referrals
        $stmt2 = $conn->prepare("SELECT ag_name FROM agent WHERE ag_code = ?");
        $stmt2->bind_param("s", $referral_code);
        $stmt2->execute();
        $result2 = $stmt2->get_result();
        
        if ($result2->num_rows > 0) {
            echo json_encode(['valid' => false, 'message' => 'This referral code has reached its usage limit (10 uses)']);
        } else {
            echo json_encode(['valid' => false, 'message' => 'Invalid referral code']);
        }
        $stmt2->close();
    }
    
    $stmt->close();
} else {
    echo json_encode(['valid' => false, 'message' => 'No referral code provided']);
}

$conn->close();
?>
