<?php
require_once 'config.php';

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.id as cart_id, d.* 
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
    <title>Shopping Cart - Domain Finder</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        .header {
            background: #00a4dc;
            padding: 1rem;
            color: white;
            margin-bottom: 2rem;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .cart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 2rem;
            animation: slideIn 0.5s ease-out;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
        }

        .cart-item:hover {
            background-color: #f9f9f9;
        }

        .domain-name {
            font-size: 1.2rem;
            color: #333;
        }

        .price {
            font-size: 1.2rem;
            color: #00a4dc;
            font-weight: bold;
        }

        .remove-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .remove-btn:hover {
            background: #cc0000;
        }

        .cart-total {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 2px solid #eee;
            text-align: right;
            font-size: 1.5rem;
        }

        .checkout-btn {
            display: block;
            width: 100%;
            background: #00a4dc;
            color: white;
            border: none;
            padding: 1rem;
            font-size: 1.2rem;
            border-radius: 4px;
            margin-top: 2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .checkout-btn:hover {
            background: #0088b9;
        }

        .empty-cart {
            text-align: center;
            padding: 2rem;
            color: #666;
        }

        .continue-shopping {
            display: inline-block;
            margin-top: 1rem;
            color: #00a4dc;
            text-decoration: none;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
            text-align: center;
        }

        .status-added {
            background: #d4edda;
            color: #155724;
        }

        .status-exists {
            background: #fff3cd;
            color: #856404;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Domain Finder</div>
    </header>

    <main class="main-content">
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'added'): ?>
                <div class="status-message status-added">
                    Domain added to cart successfully!
                </div>
            <?php elseif ($_GET['status'] === 'exists'): ?>
                <div class="status-message status-exists">
                    This domain is already in your cart.
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="cart-container">
            <?php if (empty($cartItems)): ?>
                <div class="empty-cart">
                    <h2>Your cart is empty</h2>
                    <a href="index.php" class="continue-shopping">Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <div class="domain-name">
                            <?php echo htmlspecialchars($item['domain_name'] . $item['extension']); ?>
                        </div>
                        <div class="price">
                            $<?php echo number_format($item['price'], 2); ?>
                        </div>
                        <form action="remove-from-cart.php" method="POST">
                            <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                            <button type="submit" class="remove-btn">Remove</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <div class="cart-total">
                    Total: $<?php echo number_format($total, 2); ?>
                </div>

                <form action="checkout.php" method="POST">
                    <button type="submit" class="checkout-btn">Proceed to Checkout</button>
                </form>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Animate status messages
        const statusMessage = document.querySelector('.status-message');
        if (statusMessage) {
            setTimeout(() => {
                statusMessage.style.opacity = '0';
                statusMessage.style.transition = 'opacity 0.5s ease';
            }, 3000);
        }

        // Animate remove buttons
        const removeButtons = document.querySelectorAll('.remove-btn');
        removeButtons.forEach(button => {
            button.addEventListener('mouseover', function() {
                this.style.transform = 'scale(1.1)';
                this.style.transition = 'transform 0.3s ease';
            });
            button.addEventListener('mouseout', function() {
                this.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>
