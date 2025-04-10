<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cart_id = filter_input(INPUT_POST, 'cart_id', FILTER_SANITIZE_NUMBER_INT);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND session_id = ?");
        $stmt->execute([$cart_id, $_SESSION['session_id']]);
        
        header('Location: cart.php');
        exit;
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

header('Location: cart.php');
exit;
?>
