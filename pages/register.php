<?php
include('../includes/db.php');
session_start();

if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $role = 'user';

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $error_message = "Email is already registered!";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
            $stmt->execute([$email, $password_hash, $role]);

            $_SESSION['user_id'] = $conn->lastInsertId();
            header("Location: ../index.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Campus Marketplace</title>
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

        .register-wrapper {
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

        .register-illust {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(234, 88, 12, 0.05));
            padding: 60px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        .register-illust h1 {
            font-size: 2.5rem;
            color: #FFFFFF;
            margin-bottom: 20px;
        }

        .register-illust p {
            color: #A1A1AA;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .illust-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }

        .register-form-container {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-form-container h2 {
            font-size: 1.75rem;
            color: #FFFFFF;
            margin-bottom: 12px;
        }

        .register-form-container > p {
            color: #A1A1AA;
            margin-bottom: 32px;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 20px;
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

        .password-requirements {
            font-size: 0.8rem;
            color: #A1A1AA;
            margin-top: 8px;
            line-height: 1.5;
        }

        .register-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #F97316, #EA580C);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 16px;
            font-family: inherit;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(249, 115, 22, 0.3);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            color: #A1A1AA;
            font-size: 0.95rem;
        }

        .login-link a {
            color: #F97316;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .login-link a:hover {
            color: #EA580C;
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

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border: 1px solid #22C55E;
            color: #86EFAC;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        html.light {
            background: linear-gradient(135deg, #FFFFFF, #F9FAFB);
        }

        html.light .register-wrapper {
            background: #FFFFFF;
        }

        html.light .register-illust {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.05), rgba(234, 88, 12, 0.02));
        }

        html.light .register-illust h1 {
            color: #111827;
        }

        html.light .register-illust p {
            color: #6B7280;
        }

        html.light .register-form-container h2 {
            color: #111827;
        }

        html.light .register-form-container > p {
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
            .register-wrapper {
                grid-template-columns: 1fr;
            }

            .register-illust {
                padding: 40px 20px;
            }

            .register-form-container {
                padding: 40px 20px;
                max-height: 80vh;
                overflow-y: auto;
            }

            .register-illust h1 {
                font-size: 2rem;
            }

            .illust-icon {
                font-size: 4rem;
            }
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="register-illust">
            <div class="illust-icon">🚀</div>
            <h1>Join Campus</h1>
            <p>Create an account and start buying, selling, or renting items on our marketplace</p>
        </div>

        <div class="register-form-container">
            <h2>Create Account</h2>
            <p>Sign up to get started</p>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    ⚠️ <?= htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="you@campus.edu" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                    <div class="password-requirements">
                        Must be at least 6 characters
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
                </div>

                <button type="submit" name="register" class="register-btn">Create Account</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Sign in here</a>
            </div>
        </div>
    </div>

    <script>
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
