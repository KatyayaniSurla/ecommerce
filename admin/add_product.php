<?php
include '../includes/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

$success_message = '';
$error_message = '';

if (isset($_POST['add_product'])) {
    $name = htmlspecialchars($_POST['name']);
    $price = (float)$_POST['price'];
    $description = htmlspecialchars($_POST['description']);
    
    if (empty($name) || $price <= 0 || empty($description)) {
        $error_message = "All fields are required and price must be positive.";
    } elseif ($_FILES['image']['error'] === 0) {
        $image_name = basename($_FILES['image']['name']);
        $target_path = "../images/" . $image_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $price, $description, $image_name])) {
                $success_message = "✓ Product added successfully!";
            } else {
                $error_message = "Database error. Please try again.";
            }
        } else {
            $error_message = "Failed to upload image.";
        }
    } else {
        $error_message = "Please upload an image.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product - Admin Panel</title>
    <link rel="stylesheet" href="../css/tailwind-config.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--bg), #14141C);
            padding: 40px 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 700px;
            margin: 0 auto;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 2rem;
            color: #FFFFFF;
            margin: 0 0 8px;
        }

        .page-header p {
            color: #A1A1AA;
            margin: 0;
        }

        .form-card {
            background: #18181B;
            border: 1px solid #27272A;
            border-radius: 16px;
            padding: 40px;
        }

        .form-group {
            margin-bottom: 28px;
        }

        label {
            display: block;
            color: #FFFFFF;
            font-weight: 600;
            margin-bottom: 12px;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 12px 16px;
            background-color: #27272A;
            border: 1px solid #27272A;
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        input[type="file"]:focus {
            outline: none;
            border-color: #F97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        input::placeholder,
        textarea::placeholder {
            color: #6B7280;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .file-input-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(234, 88, 12, 0.05));
            border: 2px dashed #27272A;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .file-input-label:hover {
            border-color: #F97316;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.15), rgba(234, 88, 12, 0.08));
        }

        input[type="file"] {
            display: none;
        }

        .file-input-text {
            text-align: center;
        }

        .file-icon {
            font-size: 2rem;
            margin-bottom: 8px;
        }

        .file-name {
            color: #F97316;
            font-weight: 600;
        }

        .form-actions {
            display: flex;
            gap: 12px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(249, 115, 22, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: #F97316;
            border: 1px solid #F97316;
        }

        .btn-secondary:hover {
            background: rgba(249, 115, 22, 0.1);
        }

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid #22C55E;
            color: #86EFAC;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #EF4444;
            color: #FECACA;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        html.light {
            background-color: #FFFFFF;
        }

        html.light .form-card {
            background: #FFFFFF;
            border-color: #E5E7EB;
        }

        html.light label {
            color: #111827;
        }

        html.light input[type="text"],
        html.light input[type="number"],
        html.light textarea,
        html.light input[type="file"] {
            background-color: #F9FAFB;
            border-color: #E5E7EB;
            color: #111827;
        }

        html.light input::placeholder,
        html.light textarea::placeholder {
            color: #9CA3AF;
        }

        html.light .page-header h1 {
            color: #111827;
        }

        @media (max-width: 768px) {
            .form-card {
                padding: 24px;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .form-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Add New Product</h1>
            <p>Create a new product listing</p>
        </div>

        <div class="form-card">
            <?php if ($success_message): ?>
                <div class="success-message">
                    <span>✓</span>
                    <span><?= $success_message; ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="error-message">
                    <span>⚠️</span>
                    <span><?= $error_message; ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter product name" required>
                </div>

                <div class="form-group">
                    <label for="price">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Enter product description" required></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Product Image</label>
                    <label for="image" class="file-input-label">
                        <div class="file-input-text">
                            <div class="file-icon">📸</div>
                            <div>Drag and drop your image or click to browse</div>
                            <div class="file-name" id="file-name"></div>
                        </div>
                    </label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>

                <div class="form-actions">
                    <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    <a href="manage_products.php" class="btn btn-secondary" style="text-decoration: none; text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // File input handler
        const fileInput = document.getElementById('image');
        fileInput.addEventListener('change', (e) => {
            const fileName = e.target.files[0]?.name;
            document.getElementById('file-name').textContent = fileName ? `Selected: ${fileName}` : '';
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
