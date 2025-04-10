<?php
require_once 'config.php';

$orderId = filter_input(INPUT_GET, 'order', FILTER_SANITIZE_NUMBER_INT);

if (!$orderId) {
    header('Location: index.php');
    exit;
}

// Get order details
$stmt = $pdo->prepare("
    SELECT o.*, oi.*, d.*
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN domains d ON oi.domain_id = d.id
    WHERE o.id = ? AND o.session_id = ?
");
$stmt->execute([$orderId, $_SESSION['session_id']]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($orderItems)) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Domain Finder</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            background: #f8f9fa;
            min-height: 100vh;
        }

        .header {
            background: #00a4dc;
            padding: 1rem;
            color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo {
            max-width: 1200px;
            margin: 0 auto;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .confirmation-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .success-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #28a745;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 2.5rem;
        }

        .success-title {
            color: #28a745;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .order-number {
            color: #6c757d;
            font-size: 1.1rem;
        }

        .order-details {
            margin-top: 2rem;
        }

        .section-title {
            color: #343a40;
            font-size: 1.2rem;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .domain-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .domain-name {
            color: #495057;
        }

        .domain-price {
            color: #00a4dc;
            font-weight: bold;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 2px solid #f8f9fa;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .next-steps {
            background: #e9ecef;
            padding: 1.5rem;
            border-radius: 8px;
            margin-top: 2rem;
        }

        .next-steps h3 {
            color: #495057;
            margin-bottom: 1rem;
        }

        .next-steps ul {
            list-style-type: none;
        }

        .next-steps li {
            margin-bottom: 0.5rem;
            color: #6c757d;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        .button {
            flex: 1;
            padding: 1rem;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .primary-button {
            background: #00a4dc;
            color: white;
        }

        .primary-button:hover {
            background: #0088b9;
        }

        .secondary-button {
            background: #6c757d;
            color: white;
        }

        .secondary-button:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Domain Finder</div>
    </header>

    <div class="container">
        <div class="confirmation-card">
            <div class="success-header">
                <div class="checkmark">✓</div>
                <h1 class="success-title">Order Confirmed!</h1>
                <p class="order-number">Order #<?php echo $orderId; ?></p>
            </div>

            <div class="order-details">
                <h2 class="section-title">Order Summary</h2>
                <?php foreach ($orderItems as $item): ?>
                    <div class="domain-item">
                        <span class="domain-name">
                            <?php echo htmlspecialchars($item['domain_name'] . $item['extension']); ?>
                        </span>
                        <span class="domain-price">
                            $<?php echo number_format($item['price'], 2); ?>
                        </span>
                    </div>
                <?php endforeach; ?>

                <div class="total-row">
                    <span>Total</span>
                    <span>$<?php echo number_format($orderItems[0]['total_amount'], 2); ?></span>
                </div>
            </div>

            <div class="next-steps">
                <h3>Next Steps</h3>
                <ul>
                    <li>• Check your email for order confirmation</li>
                    <li>• Set up your domain DNS settings</li>
                    <li>• Configure your domain nameservers</li>
                    <li>• Add domain privacy protection</li>
                </ul>
            </div>

            <div class="action-buttons">
                <a href="index.php" class="button primary-button">Return to Homepage</a>
                <a href="account/domains.php" class="button secondary-button">Manage Domains</a>
            </div>
        </div>
    </div>

    <script>
        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add hover animations to buttons
        document.querySelectorAll('.button').forEach(button => {
            button.addEventListener('mouseover', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.transition = 'transform 0.3s ease';
            });
            button.addEventListener('mouseout', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>
