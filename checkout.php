<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get cart items
        $stmt = $pdo->prepare("
            SELECT d.* 
            FROM cart c 
            JOIN domains d ON c.domain_id = d.id 
            WHERE c.session_id = ?
        ");
        $stmt->execute([$_SESSION['session_id']]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($cartItems)) {
            header('Location: cart.php');
            exit;
        }
        
        // Calculate total
        $total = array_sum(array_column($cartItems, 'price'));
        
        // Create order
        $pdo->beginTransaction();
        
        // Insert order
        $stmt = $pdo->prepare("INSERT INTO orders (session_id, total_amount, status) VALUES (?, ?, 'completed')");
        $stmt->execute([$_SESSION['session_id'], $total]);
        $orderId = $pdo->lastInsertId();
        
        // Insert order items
        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, domain_id, price) VALUES (?, ?, ?)");
        foreach ($cartItems as $item) {
            $stmt->execute([$orderId, $item['id'], $item['price']]);
        }
        
        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE session_id = ?");
        $stmt->execute([$_SESSION['session_id']]);
        
        $pdo->commit();
        
        // Redirect to thank you page
        header('Location: thank-you.php?order=' . $orderId);
        exit;
    } catch(PDOException $e) {
        $pdo->rollBack();
        die("Error processing order: " . $e->getMessage());
    }
}

// Get cart items for display
$stmt = $pdo->prepare("
    SELECT d.* 
    FROM cart c 
    JOIN domains d ON c.domain_id = d.id 
    WHERE c.session_id = ?
");
$stmt->execute([$_SESSION['session_id']]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total
$total = array_sum(array_column($cartItems, 'price'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Domain Finder</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: #f5f5f5;
        }

        .header {
            background: #00a4dc;
            padding: 1rem;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            max-width: 1200px;
            margin: 0 auto;
        }

        .main-content {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        .checkout-form {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .order-summary {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            height: fit-content;
        }

        h1, h2 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        input:focus, select:focus {
            border-color: #00a4dc;
            outline: none;
            box-shadow: 0 0 0 2px rgba(0,164,220,0.2);
        }

        .domain-list {
            margin: 1rem 0;
        }

        .domain-item {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .total {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            font-size: 1.2rem;
            font-weight: bold;
        }

        .checkout-button {
            background: #00a4dc;
            color: white;
            border: none;
            width: 100%;
            padding: 1rem;
            font-size: 1.2rem;
            border-radius: 4px;
            cursor: pointer;
            margin-top: 1rem;
            transition: background 0.3s ease;
        }

        .checkout-button:hover {
            background: #0088b9;
        }

        .error {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }

        .payment-icons {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            color: #666;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #28a745;
            margin-top: 1rem;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Domain Finder</div>
    </header>

    <main class="main-content">
        <div class="checkout-form">
            <h1>Checkout Information</h1>
            <form method="POST" action="checkout.php" id="checkoutForm">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="card">Card Number</label>
                    <input type="text" id="card" name="card" maxlength="16" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="expiry">Expiry Date</label>
                        <input type="text" id="expiry" name="expiry" placeholder="MM/YY" required>
                    </div>

                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" maxlength="3" required>
                    </div>
                </div>

                <button type="submit" class="checkout-button">Complete Purchase</button>

                <div class="secure-badge">
                    🔒 Secure Checkout
                </div>
            </form>
        </div>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="domain-list">
                <?php foreach ($cartItems as $item): ?>
                    <div class="domain-item">
                        <span><?php echo htmlspecialchars($item['domain_name'] . $item['extension']); ?></span>
                        <span>$<?php echo number_format($item['price'], 2); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="total">
                <span>Total</span>
                <span>$<?php echo number_format($total, 2); ?></span>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Add loading state to button
            const button = this.querySelector('button[type="submit"]');
            button.textContent = 'Processing...';
            button.disabled = true;
            
            // Simulate processing (remove in production)
            setTimeout(() => {
                this.submit();
            }, 1500);
        });

        // Format card number
        document.getElementById('card').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });

        // Format expiry date
        document.getElementById('expiry').addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substr(0,2) + '/' + value.substr(2);
            }
            this.value = value;
        });

        // Format CVV
        document.getElementById('cvv').addEventListener('input', function(e) {
            this.value = this.value.replace(/\D/g, '');
        });
    </script>
</body>
</html>
