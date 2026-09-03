<?php
// Start the session and check if the user is logged in
session_start();

// Logout Logic - placed at the top of the script
if (isset($_POST['logout'])) {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: pages/login.php"); // Redirect to login page
    exit(); // Make sure no further code is executed after redirection
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the login page
    header("Location: pages/login.php");
    exit();
}

// Fetch products from the database
include 'includes/db.php';
$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Marketplace - Buy, Sell, Rent Everything</title>
    <link rel="stylesheet" href="css/tailwind-config.css">
    <style>
        /* Navbar Styles */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            background: rgba(11, 11, 18, 0.78);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        nav.scrolled {
            background: rgba(11, 11, 18, 0.96);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.24);
        }

        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #F97316, #EA580C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            gap: 32px;
            align-items: center;
            list-style: none;
        }

        .nav-links a {
            color: #A1A1AA;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: #F97316;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .dark-toggle {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s ease;
        }

        .dark-toggle:hover {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: #F97316;
        }

        /* Hero Section */
        .hero {
            margin-top: 70px;
            padding: 80px 24px;
            text-align: center;
            background: radial-gradient(circle at top, rgba(255, 107, 53, 0.16), transparent 32%), linear-gradient(135deg, rgba(11, 11, 18, 1), rgba(20, 20, 28, 0.96));
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.1), transparent);
            border-radius: 50%;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
            max-width: 800px;
            margin: 0 auto;
            animation: slideUp 0.8s ease;
        }

        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 16px;
            line-height: 1.1;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #FFFFFF, #A1A1AA);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.25rem;
            color: #A1A1AA;
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .search-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 48px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 20px;
            background-color: rgba(24, 24, 27, 0.8);
            border: 1px solid rgba(39, 39, 42, 1);
            border-radius: 12px;
            color: #FFFFFF;
            font-size: 1rem;
        }

        .search-bar input::placeholder {
            color: #6B7280;
        }

        .search-bar button {
            padding: 12px 24px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .search-bar button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 24px;
            margin-top: 48px;
            padding: 24px;
            background: rgba(28, 28, 40, 0.72);
            border: 1px solid var(--border);
            border-radius: 16px;
            backdrop-filter: blur(10px);
        }

        .stat-item h3 {
            font-size: 1.75rem;
            color: #F97316;
            margin-bottom: 4px;
        }

        .stat-item p {
            font-size: 0.875rem;
            color: #6B7280;
        }

        /* Products Section */
        .products-section {
            padding: 80px 24px;
        }

        .section-header {
            max-width: 1280px;
            margin: 0 auto 48px;
        }

        .section-header h2 {
            font-size: 2rem;
            margin-bottom: 12px;
        }

        .products-grid {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        /* Product Card */
        .product-card {
            background-color: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .product-card:hover {
            border-color: var(--accent);
            box-shadow: 0 16px 34px rgba(255, 107, 53, 0.15);
            transform: translateY(-6px);
        }

        .product-image-container {
            position: relative;
            width: 100%;
            padding-bottom: 100%;
            overflow: hidden;
            background: linear-gradient(135deg, #27272A, #18181B);
        }

        .product-image {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: rgba(249, 115, 22, 0.9);
            color: white;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
        }

        .wishlist-btn {
            position: absolute;
            top: 12px;
            left: 12px;
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            font-size: 1.2rem;
        }

        .wishlist-btn:hover {
            background: rgba(249, 115, 22, 0.8);
            border-color: #F97316;
        }

        .product-content {
            padding: 20px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-category {
            display: inline-block;
            background: rgba(249, 115, 22, 0.1);
            color: #F97316;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-bottom: 12px;
            width: fit-content;
        }

        .product-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #FFFFFF;
            line-height: 1.4;
        }

        .product-description {
            font-size: 0.875rem;
            color: #A1A1AA;
            margin-bottom: 12px;
            flex: 1;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            font-size: 0.875rem;
        }

        .product-rating {
            display: flex;
            gap: 4px;
            color: #F97316;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #F97316;
            margin-bottom: 12px;
        }

        .product-actions {
            display: flex;
            gap: 12px;
        }

        .product-actions button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .add-to-cart {
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
        }

        .add-to-cart:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .request-rental {
            background: transparent;
            color: #F97316;
            border: 1px solid #F97316;
        }

        .request-rental:hover {
            background: rgba(249, 115, 22, 0.1);
        }

        /* Footer */
        footer {
            background: #111827;
            border-top: 1px solid #27272A;
            padding: 48px 24px;
            color: #A1A1AA;
        }

        .footer-content {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 48px;
            margin-bottom: 48px;
        }

        .footer-section h4 {
            color: #FFFFFF;
            margin-bottom: 16px;
            font-size: 0.95rem;
            font-weight: 600;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section a {
            color: #A1A1AA;
            display: block;
            margin-bottom: 12px;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .footer-section a:hover {
            color: #F97316;
        }

        .footer-bottom {
            border-top: 1px solid #27272A;
            padding-top: 24px;
            text-align: center;
            font-size: 0.9rem;
        }

        /* Mobile Menu */
        .mobile-menu {
            display: none;
            position: fixed;
            top: 70px;
            left: 0;
            right: 0;
            background: rgba(9, 9, 11, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(39, 39, 42, 1);
            flex-direction: column;
            gap: 16px;
            padding: 20px;
            z-index: 999;
        }

        .mobile-menu.active {
            display: flex;
        }

        .hamburger {
            display: none;
            background: none;
            border: none;
            color: #FFFFFF;
            font-size: 1.5rem;
            cursor: pointer;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero {
                padding: 60px 20px;
            }

            .hero h1 {
                font-size: 2.25rem;
            }

            .nav-links {
                display: none;
            }

            .hamburger {
                display: block;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 16px;
            }

            .hero p {
                font-size: 1rem;
            }
        }

        @media (max-width: 480px) {
            .hero h1 {
                font-size: 1.75rem;
            }

            .hero p {
                font-size: 0.95rem;
            }

            .products-grid {
                grid-template-columns: 1fr;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .footer-content {
                grid-template-columns: 1fr;
                gap: 24px;
            }
        }

        html.light {
            background-color: #FFFFFF;
        }

        html.light nav {
            background: rgba(255, 255, 255, 0.7);
            border-bottom-color: rgba(0, 0, 0, 0.05);
        }

        html.light nav.scrolled {
            background: rgba(255, 255, 255, 0.9);
        }

        html.light .product-card {
            background-color: #FFFFFF;
            border-color: #E5E7EB;
        }

        html.light .product-card:hover {
            border-color: #F97316;
        }

        html.light footer {
            background: #F9FAFB;
            border-top-color: #E5E7EB;
        }

        html.light .product-name {
            color: #111827;
        }

        html.light .product-description {
            color: #6B7280;
        }

        html.light .nav-links a {
            color: #6B7280;
        }

        html.light .hero p {
            color: #6B7280;
        }

        html.light .section-header h2 {
            color: #111827;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav id="navbar">
        <div class="nav-container">
            <div class="logo">🏫 Campus</div>
            <ul class="nav-links">
                <li><a href="#home">Home</a></li>
                <li><a href="#categories">Categories</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="pages/cart.php">Cart</a></li>
            </ul>
            <div class="nav-actions">
                <button class="dark-toggle" onclick="toggleDarkMode()" title="Toggle dark mode">
                    <span id="theme-icon">🌙</span>
                </button>
                <form method="POST" style="display: inline;">
                    <button type="submit" name="logout" class="btn btn-primary btn-sm">Logout</button>
                </form>
            </div>
            <button class="hamburger" onclick="toggleMobileMenu()">☰</button>
        </div>
        <div class="mobile-menu" id="mobileMenu">
            <a href="#home" style="color: #F97316; font-weight: 600;">Home</a>
            <a href="#categories" style="color: #A1A1AA;">Categories</a>
            <a href="#products" style="color: #A1A1AA;">Products</a>
            <a href="pages/cart.php" style="color: #A1A1AA;">Cart</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Campus<br><span class="hero-gradient">Marketplace</span></h1>
            <p>Buy • Sell • Rent • Trade Everything Within Your Campus Community</p>
            
            <div class="search-bar">
                <input type="text" placeholder="Search products, categories, or sellers...">
                <button type="submit">Search</button>
            </div>

            <div class="stats">
                <div class="stat-item">
                    <h3><?= count($products) ?></h3>
                    <p>Active Products</p>
                </div>
                <div class="stat-item">
                    <h3>5K+</h3>
                    <p>Happy Buyers</p>
                </div>
                <div class="stat-item">
                    <h3>2K+</h3>
                    <p>Active Sellers</p>
                </div>
                <div class="stat-item">
                    <h3>$50K+</h3>
                    <p>Transactions</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products-section" id="products">
        <div class="section-header">
            <h2>🔥 Latest Listings</h2>
            <p style="color: #A1A1AA;">Discover amazing deals from your campus community</p>
        </div>

        <div class="products-grid">
            <?php if (empty($products)) : ?>
                <p style="grid-column: 1/-1; text-align: center; color: #A1A1AA;">No products available yet. Check back soon!</p>
            <?php else : ?>
                <?php foreach ($products as $product) : ?>
                    <div class="product-card fade-in">
                        <div class="product-image-container">
                            <?php if (!empty($product['image']) && file_exists('images/' . htmlspecialchars($product['image']))) : ?>
                                <img src="images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                            <?php else : ?>
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 400 400'%3E%3Crect fill='%2327272A' width='400' height='400'/%3E%3Ctext x='50%25' y='50%25' font-size='24' fill='%23A1A1AA' text-anchor='middle' dy='.3em'%3ENo Image%3C/text%3E%3C/svg%3E" alt="No image available" class="product-image">
                            <?php endif; ?>
                            <div class="product-badge">New</div>
                            <button class="wishlist-btn" onclick="toggleWishlist(this)" title="Add to wishlist">♡</button>
                        </div>

                        <div class="product-content">
                            <div class="product-category">Electronics</div>
                            <h3 class="product-name"><?= htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?= substr(htmlspecialchars($product['description']), 0, 80); ?>...</p>

                            <div class="product-meta">
                                <div class="product-rating">
                                    <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                                </div>
                                <span style="color: #6B7280;">In Stock</span>
                            </div>

                            <div class="product-price">$<?= number_format($product['price'], 2); ?></div>

                            <div class="product-actions">
                                <form method="POST" action="pages/cart.php" style="flex: 1; display: flex; gap: 12px;">
                                    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                    <button type="submit" name="add_to_cart" class="add-to-cart">Add to Cart</button>
                                    <button type="button" class="request-rental">Rent</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>About Campus</h4>
                <ul>
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Press</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Status</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Community</h4>
                <ul>
                    <li><a href="#">Discord</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Instagram</a></li>
                    <li><a href="#">LinkedIn</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Privacy</a></li>
                    <li><a href="#">Terms</a></li>
                    <li><a href="#">Cookies</a></li>
                    <li><a href="#">License</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Campus Marketplace. All rights reserved. Built with ♡ for students.</p>
        </div>
    </footer>

    <script>
        // Dark Mode Toggle
        function initDarkMode() {
            const isDark = localStorage.getItem('darkMode') !== 'false';
            if (isDark) {
                document.documentElement.classList.remove('light');
                updateThemeIcon();
            } else {
                document.documentElement.classList.add('light');
                updateThemeIcon();
            }
        }

        function toggleDarkMode() {
            const isDark = !document.documentElement.classList.contains('light');
            if (isDark) {
                document.documentElement.classList.add('light');
                localStorage.setItem('darkMode', 'false');
            } else {
                document.documentElement.classList.remove('light');
                localStorage.setItem('darkMode', 'true');
            }
            updateThemeIcon();
        }

        function updateThemeIcon() {
            const isDark = !document.documentElement.classList.contains('light');
            document.getElementById('theme-icon').textContent = isDark ? '🌙' : '☀️';
        }

        // Navbar Scroll Effect
        window.addEventListener('scroll', () => {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile Menu
        function toggleMobileMenu() {
            document.getElementById('mobileMenu').classList.toggle('active');
        }

        document.querySelectorAll('#mobileMenu a').forEach(link => {
            link.addEventListener('click', () => {
                document.getElementById('mobileMenu').classList.remove('active');
            });
        });

        // Wishlist
        function toggleWishlist(btn) {
            btn.textContent = btn.textContent === '♡' ? '♥' : '♡';
            btn.style.color = btn.textContent === '♥' ? '#F97316' : '#FFFFFF';
        }

        // Initialize
        initDarkMode();
    </script>
</body>
</html>
