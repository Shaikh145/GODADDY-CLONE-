<?php
require_once 'config.php';

$domain = filter_input(INPUT_GET, 'domain', FILTER_SANITIZE_STRING);
$domain = strtolower(trim($domain));

// Get available domain extensions
$stmt = $pdo->prepare("SELECT * FROM domains WHERE domain_name = ?");
$stmt->execute([$domain]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// If no results, generate some suggestions
if (empty($results)) {
    $suggestions = [
        ['extension' => '.com', 'price' => 12.99],
        ['extension' => '.net', 'price' => 11.99],
        ['extension' => '.org', 'price' => 10.99],
        ['extension' => '.io', 'price' => 29.99]
    ];
    
    foreach ($suggestions as $suggestion) {
        $stmt = $pdo->prepare("INSERT INTO domains (domain_name, extension, price) VALUES (?, ?, ?)");
        $stmt->execute([$domain, $suggestion['extension'], $suggestion['price']]);
    }
    
    // Fetch the newly inserted domains
    $stmt = $pdo->prepare("SELECT * FROM domains WHERE domain_name = ?");
    $stmt->execute([$domain]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - Domain Finder</title>
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

        .results-container {
            animation: slideIn 0.5s ease-out;
        }

        .domain-card {
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .domain-card:hover {
            transform: translateX(10px);
            border-color: #00a4dc;
        }

        .domain-name {
            font-size: 1.2rem;
            color: #333;
        }

        .domain-price {
            font-size: 1.5rem;
            color: #00a4dc;
            font-weight: bold;
        }

        .add-to-cart {
            background: #00a4dc;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .add-to-cart:hover {
            background: #0088b9;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .search-again {
            margin-bottom: 2rem;
        }

        .search-input {
            padding: 0.5rem;
            font-size: 1rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            margin-right: 0.5rem;
        }

        .search-button {
            padding: 0.5rem 1rem;
            background: #00a4dc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Domain Finder</div>
    </header>

    <main class="main-content">
        <div class="search-again">
            <form action="search.php" method="GET">
                <input type="text" 
                       name="domain" 
                       class="search-input" 
                       value="<?php echo htmlspecialchars($domain); ?>"
                       required>
                <button type="submit" class="search-button">Search Again</button>
            </form>
        </div>

        <div class="results-container">
            <?php foreach ($results as $result): ?>
                <div class="domain-card">
                    <div class="domain-info">
                        <div class="domain-name">
                            <?php echo htmlspecialchars($result['domain_name'] . $result['extension']); ?>
                        </div>
                    </div>
                    <div class="domain-price">
                        $<?php echo number_format($result['price'], 2); ?>
                    </div>
                    <form action="add-to-cart.php" method="POST">
                        <input type="hidden" name="domain_id" value="<?php echo $result['id']; ?>">
                        <button type="submit" class="add-to-cart">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        // Animate domain cards on scroll
        const cards = document.querySelectorAll('.domain-card');
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateX(0)';
                    }
                });
            },
            { threshold: 0.1 }
        );

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateX(-20px)';
            observer.observe(card);
        });
    </script>
</body>
</html>
