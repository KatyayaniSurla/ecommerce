<?php
include '../includes/db.php';
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid credentials or not an admin.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Campus Marketplace</title>
    <link rel="stylesheet" href="../css/tailwind-config.css">
    <style>
        body {
            background: linear-gradient(135deg, var(--bg), #14141C);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .admin-login-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            background: var(--card);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            max-width: 1000px;
            width: 100%;
        }

        .admin-illust {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
            padding: 60px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        .admin-illust h1 {
            font-size: 2.5rem;
            color: #FFFFFF;
            margin-bottom: 20px;
        }

        .admin-illust p {
            color: #A1A1AA;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .illust-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }

        .admin-form-container {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .admin-form-container h2 {
            font-size: 1.75rem;
            color: #FFFFFF;
            margin-bottom: 12px;
        }

        .admin-form-container > p {
            color: #A1A1AA;
            margin-bottom: 32px;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            color: #FFFFFF;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            background-color: #18181B;
            border: 1px solid #27272A;
            border-radius: 8px;
            color: #FFFFFF;
            font-size: 1rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #F97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        input::placeholder {
            color: #6B7280;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #EF4444, #DC2626);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid #EF4444;
            color: #FECACA;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .admin-warning {
            background: rgba(245, 158, 11, 0.1);
            border: 1px solid #F59E0B;
            color: #FCD34D;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        html.light {
            background: linear-gradient(135deg, #FFFFFF, #F9FAFB);
        }

        html.light .admin-login-wrapper {
            background: #FFFFFF;
        }

        html.light .admin-illust {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(220, 38, 38, 0.02));
        }

        html.light .admin-illust h1 {
            color: #111827;
        }

        html.light .admin-illust p {
            color: #6B7280;
        }

        html.light .admin-form-container h2 {
            color: #111827;
        }

        html.light .admin-form-container > p {
            color: #6B7280;
        }

        html.light label {
            color: #111827;
        }

        html.light input[type="email"],
        html.light input[type="password"] {
            background-color: #F9FAFB;
            border-color: #E5E7EB;
            color: #111827;
        }

        html.light input::placeholder {
            color: #9CA3AF;
        }

        @media (max-width: 768px) {
            .admin-login-wrapper {
                grid-template-columns: 1fr;
            }

            .admin-illust {
                padding: 40px 20px;
            }

            .admin-form-container {
                padding: 40px 20px;
            }

            .admin-illust h1 {
                font-size: 2rem;
            }

            .illust-icon {
                font-size: 4rem;
            }
        }
    </style>
</head>
<body>
    <div class="admin-login-wrapper">
        <div class="admin-illust">
            <div class="illust-icon">⚙️</div>
            <h1>Admin Portal</h1>
            <p>Manage your marketplace, products, users, and analytics from the admin dashboard</p>
        </div>

        <div class="admin-form-container">
            <h2>Admin Login</h2>
            <p>Access admin controls</p>

            <div class="admin-warning">
                🔐 This is a restricted admin area. Unauthorized access is prohibited.
            </div>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    ⚠️ <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Admin Email</label>
                    <input type="email" id="email" name="email" placeholder="admin@campus.edu" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login" class="login-btn">Sign In to Admin Panel</button>
            </form>
        </div>
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
    </div>

</body>
</html>
