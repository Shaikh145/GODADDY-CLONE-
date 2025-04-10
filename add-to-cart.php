<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain_id = filter_input(INPUT_POST, 'domain_id', FILTER_SANITIZE_NUMBER_INT);
    
    try {
        // Check if domain is already in cart
        $stmt = $pdo->prepare("SELECT id FROM cart WHERE session_id = ? AND domain_id = ?");
        $stmt->execute([$_SESSION['session_id'], $domain_id]);
        
        if (!$stmt->fetch()) {
            // Add to cart if not already there
            $stmt = $pdo->prepare("INSERT INTO cart (session_id, domain_id) VALUES (?, ?)");
            $stmt->execute([$_SESSION['session_id'], $domain_id]);
            
            header('Location: cart.php?status=added');
            exit;
        } else {
            header('Location: cart.php?status=exists');
            exit;
        }
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

header('Location: index.php');
exit;
?>
