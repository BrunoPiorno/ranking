<?php
function checkPaymentStatus($user_id) {
    global $conn;
    
    $current_month = date('n');
    $current_year = date('Y');
    
    $sql = "SELECT * FROM payments 
            WHERE user_id = ? 
            AND period_month = ? 
            AND period_year = ? 
            AND status = 'approved'";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $user_id, $current_month, $current_year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

function isPaymentPeriod() {
    $current_day = date('j');
    return $current_day >= 1 && $current_day <= 10;
}