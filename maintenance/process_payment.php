<?php
require_once '../config/mercadopago.php';

function createSubscription($user_id) {
    $subscription = new MercadoPago\Preapproval();
    
    $subscription->back_url = "http://localhost:8888<?= url('/') ?>maintenance/subscription_status.php";
    $subscription->reason = "Suscripción Mensual - Sistema de Ranking";
    $subscription->auto_recurring = [
        "frequency" => 1,
        "frequency_type" => "months",
        "transaction_amount" => 1000, // Monto en pesos
        "currency_id" => "ARS"
    ];
    
    $subscription->save();
    
    // Guardar en la base de datos
    saveSubscriptionToDatabase($user_id, $subscription->id);
    
    return $subscription->init_point;
}

function saveSubscriptionToDatabase($user_id, $subscription_id) {
    global $conn;
    $current_month = date('n');
    $current_year = date('Y');
    
    $sql = "INSERT INTO payments (user_id, amount, status, mercadopago_payment_id, period_month, period_year) 
            VALUES (?, 1000, 'pending', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isii", $user_id, $subscription_id, $current_month, $current_year);
    $stmt->execute();
}