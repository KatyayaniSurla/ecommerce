<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle delete product
if (isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
}

// Get all products
$stmt = $conn->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Admin Panel</title>
    <link rel="stylesheet" href="../css/tailwind-config.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--bg), #14141C);
            padding: 40px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2rem;
            color: #FFFFFF;
            margin: 0;
        }

        .page-header p {
            color: #A1A1AA;
            margin: 0;
            font-size: 0.95rem;
        }

        .btn-add {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(249, 115, 22, 0.3);
        }

        .search-bar {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
        }

        .search-input {
            flex: 1;
            padding: 12px 16px;
            background-color: #18181B;
            border: 1px solid #27272A;
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #F97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        .table-container {
            background: #18181B;
            border: 1px solid #27272A;
            border-radius: 16px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #111827;
            border-bottom: 2px solid #27272A;
        }

        th {
            padding: 16px;
            text-align: left;
            color: #A1A1AA;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        tbody tr {
            border-bottom: 1px solid #27272A;
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: rgba(249, 115, 22, 0.05);
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        td {
            padding: 16px;
            color: #FFFFFF;
        }

        .product-image {
            width: 50px;
            height: 50px;
            border-radius: 6px;
            background: #27272A;
            object-fit: cover;
        }

        .product-name {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .product-id {
            font-size: 0.85rem;
            color: #A1A1AA;
        }

        .price {
            color: #F97316;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .description {
            color: #A1A1AA;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .actions {
            display: flex;
            gap: 12px;
        }

        .btn-edit,
        .btn-delete {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-edit {
            background: rgba(59, 130, 246, 0.1);
            color: #3B82F6;
            border: 1px solid #3B82F6;
        }

        .btn-edit:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: #EF4444;
            border: 1px solid #EF4444;
        }

        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .empty-title {
            font-size: 1.5rem;
            color: #FFFFFF;
            margin-bottom: 12px;
        }

        .empty-description {
            color: #A1A1AA;
            margin-bottom: 24px;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #18181B;
            border: 1px solid #27272A;
            border-radius: 16px;
            padding: 32px;
            max-width: 400px;
            width: 90%;
        }

        .modal-title {
            font-size: 1.5rem;
            color: #FFFFFF;
            margin-bottom: 16px;
        }

        .modal-message {
            color: #A1A1AA;
            margin-bottom: 24px;
        }

        .modal-actions {
            display: flex;
            gap: 12px;
        }

        .modal-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .modal-btn-confirm {
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
        }

        .modal-btn-confirm:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .modal-btn-cancel {
            background: transparent;
            color: #A1A1AA;
            border: 1px solid #27272A;
        }

        .modal-btn-cancel:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        html.light {
            background-color: #FFFFFF;
        }

        html.light .table-container {
            background: #FFFFFF;
            border-color: #E5E7EB;
        }

        html.light thead {
            background: #F9FAFB;
            border-bottom-color: #E5E7EB;
        }

        html.light th {
            color: #6B7280;
        }

        html.light tbody tr {
            border-bottom-color: #E5E7EB;
        }

        html.light tbody tr:hover {
            background-color: rgba(249, 115, 22, 0.03);
        }

        html.light td {
            color: #111827;
        }

        html.light .product-id {
            color: #6B7280;
        }

        html.light .description {
            color: #6B7280;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .table-container {
                overflow-x: auto;
            }

            th, td {
                padding: 12px;
                font-size: 0.85rem;
            }

            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <div>
                <h1>📦 Manage Products</h1>
                <p><?= count($products); ?> product<?= count($products) !== 1 ? 's' : ''; ?> in catalog</p>
            </div>
            <a href="add_product.php" class="btn-add">➕ Add Product</a>
        </div>

        <div class="search-bar">
            <input type="text" id="search" class="search-input" placeholder="Search products by name...">
        </div>

        <?php if (empty($products)): ?>
            <div class="table-container">
                <div class="empty-state">
                    <div class="empty-icon">📭</div>
                    <h2 class="empty-title">No products found</h2>
                    <p class="empty-description">Start by adding your first product to the marketplace</p>
                    <a href="add_product.php" class="btn-add">Add First Product</a>
                </div>
            </div>
        <?php else: ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr class="product-row">
                                <td>
                                    <?php if (!empty($product['image']) && file_exists('../images/' . htmlspecialchars($product['image']))): ?>
                                        <img src="../images/<?= htmlspecialchars($product['image']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="product-image">
                                    <?php else: ?>
                                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Crect fill='%2327272A' width='100' height='100'/%3E%3C/svg%3E" class="product-image">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="product-name"><?= htmlspecialchars($product['name']); ?></div>
                                    <div class="product-id">ID: <?= $product['id']; ?></div>
                                </td>
                                <td>
                                    <span class="price">$<?= number_format($product['price'], 2); ?></span>
                                </td>
                                <td>
                                    <span class="description"><?= htmlspecialchars(substr($product['description'], 0, 50)); ?></span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <button class="btn-edit" onclick="alert('Edit feature coming soon!')">✏️ Edit</button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
                                            <button type="button" class="btn-delete" onclick="showDeleteModal('<?= htmlspecialchars($product['name']); ?>', <?= $product['id']; ?>)">🗑️ Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h2 class="modal-title">Delete Product</h2>
            <p class="modal-message">Are you sure you want to delete <strong id="productNameToDelete"></strong>? This action cannot be undone.</p>
            <div class="modal-actions">
                <form method="POST" style="flex: 1;">
                    <input type="hidden" id="deleteProductId" name="product_id">
                    <button type="submit" name="delete_product" class="modal-btn modal-btn-confirm" style="width: 100%;">Delete</button>
                </form>
                <button type="button" class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        function showDeleteModal(productName, productId) {
            document.getElementById('productNameToDelete').textContent = productName;
            document.getElementById('deleteProductId').value = productId;
            document.getElementById('deleteModal').classList.add('active');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.remove('active');
        }

        // Search functionality
        const searchInput = document.getElementById('search');
        searchInput.addEventListener('keyup', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.product-row');
            
            rows.forEach(row => {
                const productName = row.querySelector('.product-name').textContent.toLowerCase();
                if (productName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Initialize dark mode
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
