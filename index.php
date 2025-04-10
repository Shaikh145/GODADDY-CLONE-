<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Finder - Find Your Perfect Domain Name</title>
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
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
        }

        .main-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
            text-align: center;
        }

        .search-container {
            margin-top: 5rem;
            animation: fadeIn 1s ease-in;
        }

        .search-title {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            color: #333;
        }

        .search-form {
            display: flex;
            max-width: 600px;
            margin: 0 auto;
            gap: 1rem;
        }

        .search-input {
            flex: 1;
            padding: 1rem;
            font-size: 1.2rem;
            border: 2px solid #ddd;
            border-radius: 4px;
            transition: border-color 0.3s ease;
        }

        .search-input:focus {
            border-color: #00a4dc;
            outline: none;
        }

        .search-button {
            padding: 1rem 2rem;
            font-size: 1.2rem;
            background: #00a4dc;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-button:hover {
            background: #0088b9;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .cart-icon {
            position: fixed;
            top: 1rem;
            right: 2rem;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            color: #00a4dc;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .cart-count {
            background: #00a4dc;
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 50%;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">Domain Finder</div>
    </header>

    <?php
    // Get cart count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM cart WHERE session_id = ?");
    $stmt->execute([$_SESSION['session_id']]);
    $cartCount = $stmt->fetchColumn();
    ?>

    <a href="cart.php" class="cart-icon">
        🛒 Cart <span class="cart-count"><?php echo $cartCount; ?></span>
    </a>

    <main class="main-content">
        <div class="search-container">
            <h1 class="search-title">Find Your Perfect Domain Name</h1>
            <form class="search-form" action="search.php" method="GET">
                <input type="text" 
                       name="domain" 
                       class="search-input" 
                       placeholder="Enter your domain name..."
                       required>
                <button type="submit" class="search-button">Search Domains</button>
            </form>
        </div>
    </main>

    <script>
        // Add some animation when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            const searchContainer = document.querySelector('.search-container');
            searchContainer.style.opacity = '0';
            searchContainer.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                searchContainer.style.opacity = '1';
                searchContainer.style.transform = 'translateY(0)';
            }, 100);
        });

        // Animate the search button on hover
        const searchButton = document.querySelector('.search-button');
        searchButton.addEventListener('mouseover', function() {
            this.style.transform = 'scale(1.05)';
        });
        searchButton.addEventListener('mouseout', function() {
            this.style.transform = 'scale(1)';
        });
    </script>
</body>
</html>
