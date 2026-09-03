<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}
include '../includes/db.php';

// Fetch dashboard statistics
$stmt = $conn->query("SELECT COUNT(*) as count FROM users");
$user_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->query("SELECT COUNT(*) as count FROM products");
$product_count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

$stmt = $conn->query("SELECT SUM(price) as total FROM products");
$total_inventory = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->query("SELECT COUNT(*) as count FROM cart");
$pending_orders = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Campus Marketplace</title>
    <link rel="stylesheet" href="../css/tailwind-config.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, var(--bg), #14141C);
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: #111827;
            border-right: 1px solid #27272A;
            padding: 24px 20px;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 32px;
            padding-bottom: 20px;
            border-bottom: 1px solid #27272A;
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            background: linear-gradient(135deg, #F97316, #EA580C);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            color: #A1A1AA;
            text-decoration: none;
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.95rem;
        }

        .sidebar-link:hover {
            background: #18181B;
            color: #F97316;
        }

        .sidebar-link.active {
            background: #18181B;
            color: #F97316;
            border-left: 3px solid #F97316;
            padding-left: 13px;
        }

        .sidebar-logout {
            margin-top: auto;
            padding-top: 20px;
            border-top: 1px solid #27272A;
        }

        .logout-btn {
            width: 100%;
            padding: 12px 16px;
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            border: 1px solid #EF4444;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 40px;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #A1A1AA;
        }

        /* Statistics Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: #18181B;
            border: 1px solid #27272A;
            border-radius: 12px;
            padding: 24px;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            border-color: #F97316;
            box-shadow: 0 8px 16px rgba(249, 115, 22, 0.1);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 16px;
        }

        .stat-title {
            font-size: 0.9rem;
            color: #A1A1AA;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: #F97316;
            margin-bottom: 8px;
        }

        .stat-change {
            font-size: 0.85rem;
            color: #22C55E;
        }

        /* Quick Actions */
        .quick-actions {
            background: #18181B;
            border: 1px solid #27272A;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 40px;
        }

        .quick-actions h3 {
            margin-bottom: 20px;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 12px;
        }

        .action-btn {
            padding: 12px 20px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .action-btn.secondary {
            background: transparent;
            color: #F97316;
            border: 1px solid #F97316;
        }

        .action-btn.secondary:hover {
            background: rgba(249, 115, 22, 0.1);
        }

        /* Dark/Light Mode Toggle */
        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.2s ease;
        }

        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: #F97316;
        }

        /* Responsive */
        @media (max-width: 768px) {
            body {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                height: auto;
                position: static;
                border-right: none;
                border-bottom: 1px solid #27272A;
                padding: 20px;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .action-buttons {
                grid-template-columns: 1fr;
            }
        }

        html.light {
            background-color: #FFFFFF;
        }

        html.light .sidebar {
            background: #F9FAFB;
            border-right-color: #E5E7EB;
        }

        html.light .stat-card {
            background: #FFFFFF;
            border-color: #E5E7EB;
        }

        html.light .quick-actions {
            background: #FFFFFF;
            border-color: #E5E7EB;
        }

        html.light .page-header h1 {
            color: #111827;
        }

        html.light .stat-title {
            color: #6B7280;
        }

        html.light .sidebar-link {
            color: #6B7280;
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div style="font-size: 1.75rem;">⚙️</div>
            <h1>Admin</h1>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="sidebar-link active">
                <span>📊</span>
                <span>Dashboard</span>
            </a>
            <a href="manage_products.php" class="sidebar-link">
                <span>📦</span>
                <span>Products</span>
            </a>
            <a href="add_product.php" class="sidebar-link">
                <span>➕</span>
                <span>Add Product</span>
            </a>
            <a href="#" class="sidebar-link" style="opacity: 0.5;">
                <span>👥</span>
                <span>Users</span>
            </a>
            <a href="#" class="sidebar-link" style="opacity: 0.5;">
                <span>📋</span>
                <span>Orders</span>
            </a>
            <a href="#" class="sidebar-link" style="opacity: 0.5;">
                <span>📊</span>
                <span>Analytics</span>
            </a>
            <a href="#" class="sidebar-link" style="opacity: 0.5;">
                <span>⚙️</span>
                <span>Settings</span>
            </a>
        </nav>

        <div class="sidebar-logout">
            <form method="POST" action="logout.php">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <button class="theme-toggle" onclick="toggleDarkMode()" title="Toggle dark mode">
            <span id="theme-icon">🌙</span>
        </button>

        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Welcome to your admin control panel</p>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👥</div>
                <div class="stat-title">Total Users</div>
                <div class="stat-value"><?= $user_count; ?></div>
                <div class="stat-change">↑ 12% from last month</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-title">Total Products</div>
                <div class="stat-value"><?= $product_count; ?></div>
                <div class="stat-change">↑ 8% from last month</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">💰</div>
                <div class="stat-title">Inventory Value</div>
                <div class="stat-value">$<?= number_format($total_inventory, 0); ?></div>
                <div class="stat-change">↑ 5% from last month</div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">🛒</div>
                <div class="stat-title">Pending Orders</div>
                <div class="stat-value"><?= $pending_orders; ?></div>
                <div class="stat-change">↑ 3% from last month</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <div class="action-buttons">
                <a href="add_product.php" class="action-btn">➕ Add New Product</a>
                <a href="manage_products.php" class="action-btn">📦 Manage Products</a>
                <a href="#" class="action-btn secondary">👥 View Users</a>
                <a href="#" class="action-btn secondary">📊 View Reports</a>
            </div>
        </div>
    </div>

    <script>
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

        function initDarkMode() {
            const isDark = localStorage.getItem('darkMode') !== 'false';
            if (isDark) {
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
            }
            updateThemeIcon();
        }

        initDarkMode();
    </script>
</body>
</html>
