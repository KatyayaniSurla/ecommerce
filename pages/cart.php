<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';

$user_id = $_SESSION['user_id'];

// Handle Add to Cart with Quantity
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $cart_item = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cart_item) {
        $new_quantity = $cart_item['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$new_quantity, $user_id, $product_id]);
    } else {
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
}

// Handle Product Removal from Cart
if (isset($_POST['remove_from_cart'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
}

// Handle Quantity Update
if (isset($_POST['update_quantity'])) {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    if ($quantity > 0) {
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $user_id, $product_id]);
    } else {
        $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
    }
}

// Fetch the user's cart items with product details
$stmt = $conn->prepare("SELECT c.*, p.name, p.price, p.image, p.description FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_cost = 0;
foreach ($cart_items as $item) {
    $total_cost += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Campus Marketplace</title>
    <link rel="stylesheet" href="../css/tailwind-config.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--bg), #14141C);
            color: var(--text-primary);
            padding: 20px;
            min-height: 100vh;
        }

        .cart-header {
            max-width: 1280px;
            margin: 0 auto 40px;
            padding: 40px 24px;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(234, 88, 12, 0.05));
            border-radius: 16px;
            border: 1px solid rgba(39, 39, 42, 1);
        }

        .cart-header h1 {
            font-size: 2.5rem;
            margin: 0 0 8px;
        }

        .cart-header p {
            color: #A1A1AA;
            margin: 0;
        }

        .cart-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 32px;
        }

        .cart-items-section {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .cart-item {
            background: #18181B;
            border: 1px solid #27272A;
            border-radius: 12px;
            padding: 20px;
            display: flex;
            gap: 20px;
            align-items: flex-start;
            transition: all 0.2s ease;
        }

        .cart-item:hover {
            border-color: #F97316;
            box-shadow: 0 8px 16px rgba(249, 115, 22, 0.1);
        }

        .item-image-container {
            flex-shrink: 0;
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            background: #27272A;
        }

        .item-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #FFFFFF;
        }

        .item-description {
            font-size: 0.9rem;
            color: #A1A1AA;
            margin-bottom: 12px;
            line-height: 1.5;
        }

        .item-price-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .item-price {
            font-size: 1.25rem;
            font-weight: 700;
            color: #F97316;
        }

        .item-total {
            font-size: 0.95rem;
            color: #A1A1AA;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .quantity-control input {
            width: 70px;
            padding: 8px 12px;
            background: #27272A;
            border: 1px solid #27272A;
            border-radius: 6px;
            color: #FFFFFF;
            text-align: center;
        }

        .quantity-control input:focus {
            outline: none;
            border-color: #F97316;
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn-update {
            flex: 1;
            padding: 10px 16px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        .btn-remove {
            flex: 1;
            padding: 10px 16px;
            background: transparent;
            color: #EF4444;
            border: 1px solid #EF4444;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .btn-remove:hover {
            background: rgba(239, 68, 68, 0.1);
        }

        .cart-summary {
            position: sticky;
            top: 20px;
            background: #18181B;
            border: 1px solid #27272A;
            border-radius: 12px;
            padding: 24px;
            height: fit-content;
        }

        .summary-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #27272A;
            color: #A1A1AA;
            font-size: 0.95rem;
        }

        .summary-row:last-child {
            border: none;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-top: 2px solid #27272A;
            font-size: 1.25rem;
            font-weight: 700;
            color: #F97316;
            margin-bottom: 24px;
        }

        .btn-checkout {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.2s ease;
        }

        .btn-checkout:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(249, 115, 22, 0.3);
        }

        .btn-continue {
            width: 100%;
            padding: 12px;
            background: transparent;
            color: #F97316;
            border: 1px solid #F97316;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            margin-top: 12px;
            transition: all 0.2s ease;
        }

        .btn-continue:hover {
            background: rgba(249, 115, 22, 0.1);
        }

        .empty-cart {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 1.5rem;
            margin-bottom: 12px;
        }

        .empty-description {
            color: #A1A1AA;
            margin-bottom: 32px;
        }

        .btn-shop {
            display: inline-block;
            padding: 12px 32px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-shop:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        }

        html.light {
            background-color: #FFFFFF;
        }

        html.light body {
            color: #111827;
        }

        html.light .cart-item {
            background: #FFFFFF;
            border-color: #E5E7EB;
        }

        html.light .cart-item:hover {
            border-color: #F97316;
        }

        html.light .cart-summary {
            background: #FFFFFF;
            border-color: #E5E7EB;
        }

        html.light .quantity-control input {
            background: #F9FAFB;
            border-color: #E5E7EB;
            color: #111827;
        }

        html.light .item-name {
            color: #111827;
        }

        html.light .summary-row {
            border-color: #E5E7EB;
        }

        html.light .summary-total {
            border-top-color: #E5E7EB;
        }

        @media (max-width: 768px) {
            .cart-container {
                grid-template-columns: 1fr;
            }

            .cart-summary {
                position: static;
            }

            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .cart-header h1 {
                font-size: 2rem;
            }

            .item-image-container {
                width: 100%;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="cart-header">
        <h1>🛒 Shopping Cart</h1>
        <p><?= count($cart_items); ?> item<?= count($cart_items) !== 1 ? 's' : ''; ?> in your cart</p>
    </div>

    <div class="cart-container">
        <div class="cart-items-section">
            <?php if (empty($cart_items)): ?>
                <div class="empty-cart">
                    <div class="empty-icon">📦</div>
                    <h2 class="empty-title">Your cart is empty</h2>
                    <p class="empty-description">Start adding products to see them here</p>
                    <a href="../index.php" class="btn-shop">Continue Shopping</a>
                </div>
            <?php else: ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item fade-in">
                        <div class="item-image-container">
                            <?php if (!empty($item['image']) && file_exists('../images/' . htmlspecialchars($item['image']))): ?>
                                <img src="../images/<?= htmlspecialchars($item['image']); ?>" alt="<?= htmlspecialchars($item['name']); ?>" class="item-image">
                            <?php else: ?>
                                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200'%3E%3Crect fill='%2327272A' width='200' height='200'/%3E%3Ctext x='50%25' y='50%25' font-size='14' fill='%23A1A1AA' text-anchor='middle' dy='.3em'%3ENo Image%3C/text%3E%3C/svg%3E" alt="No image" class="item-image">
                            <?php endif; ?>
                        </div>

                        <div class="item-details">
                            <h3 class="item-name"><?= htmlspecialchars($item['name']); ?></h3>
                            <p class="item-description"><?= substr(htmlspecialchars($item['description']), 0, 100); ?>...</p>

                            <div class="item-price-row">
                                <span class="item-price">$<?= number_format($item['price'], 2); ?></span>
                                <span class="item-total">Subtotal: $<?= number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>

                            <div class="quantity-control">
                                <label for="qty_<?= $item['product_id']; ?>">Quantity:</label>
                                <form method="POST" style="display: flex; gap: 12px; flex: 1;">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
                                    <input type="number" id="qty_<?= $item['product_id']; ?>" name="quantity" value="<?= $item['quantity']; ?>" min="1" required>
                                    <button type="submit" name="update_quantity" class="btn-update">Update</button>
                                </form>
                            </div>

                            <div class="btn-group">
                                <form method="POST" style="flex: 1;">
                                    <input type="hidden" name="product_id" value="<?= $item['product_id']; ?>">
                                    <button type="submit" name="remove_from_cart" class="btn-remove">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if (!empty($cart_items)): ?>
            <div class="cart-summary">
                <div class="summary-title">Order Summary</div>

                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?= number_format($total_cost, 2); ?></span>
                </div>

                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>$0.00</span>
                </div>

                <div class="summary-row">
                    <span>Tax (estimated):</span>
                    <span>$<?= number_format($total_cost * 0.08, 2); ?></span>
                </div>

                <div class="summary-total">
                    <span>Total:</span>
                    <span>$<?= number_format($total_cost * 1.08, 2); ?></span>
                </div>

                <button class="btn-checkout">Proceed to Checkout</button>
                <a href="../index.php" class="btn-continue">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function initDarkMode() {
            const isDark = localStorage.getItem('darkMode') !== 'false';
            if (isDark) {
                document.documentElement.classList.remove('light');
            } else {
                document.documentElement.classList.add('light');
            }
        }
        initDarkMode();
    </script>
</body>
</html>