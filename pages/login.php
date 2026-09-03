<?php
include('../includes/db.php');
session_start();

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: ../index.php");
        exit();
    } else {
        $error_message = "Invalid email or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Campus Marketplace</title>
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

        .login-wrapper {
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

        .login-illust {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), rgba(234, 88, 12, 0.05));
            padding: 60px 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        .login-illust h1 {
            font-size: 2.5rem;
            color: #FFFFFF;
            margin-bottom: 20px;
        }

        .login-illust p {
            color: #A1A1AA;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .illust-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }

        .login-form-container {
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form-container h2 {
            font-size: 1.75rem;
            color: #FFFFFF;
            margin-bottom: 12px;
        }

        .login-form-container > p {
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

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #F97316;
        }

        .checkbox-group label {
            margin: 0;
            font-weight: 500;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 24px;
        }

        .forgot-password a {
            color: #F97316;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .forgot-password a:hover {
            color: #EA580C;
        }

        .login-btn {
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
            font-family: inherit;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(249, 115, 22, 0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .signup-link {
            text-align: center;
            margin-top: 24px;
            color: #A1A1AA;
            font-size: 0.95rem;
        }

        .signup-link a {
            color: #F97316;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .signup-link a:hover {
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

        html.light {
            background: linear-gradient(135deg, #FFFFFF, #F9FAFB);
        }

        html.light .login-wrapper {
            background: #FFFFFF;
        }

        html.light .login-illust {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.05), rgba(234, 88, 12, 0.02));
        }

        html.light .login-illust h1 {
            color: #111827;
        }

        html.light .login-illust p {
            color: #6B7280;
        }

        html.light .login-form-container h2 {
            color: #111827;
        }

        html.light .login-form-container > p {
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
            .login-wrapper {
                grid-template-columns: 1fr;
            }

            .login-illust {
                padding: 40px 20px;
            }

            .login-form-container {
                padding: 40px 20px;
            }

            .login-illust h1 {
                font-size: 2rem;
            }

            .illust-icon {
                font-size: 4rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-illust">
            <div class="illust-icon">🔐</div>
            <h1>Welcome Back</h1>
            <p>Sign in to your account and continue shopping at Campus Marketplace</p>
        </div>

        <div class="login-form-container">
            <h2>Login</h2>
            <p>Access your account</p>

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
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <div class="forgot-password">
                    <a href="#">Forgot your password?</a>
                </div>

                <button type="submit" name="login" class="login-btn">Sign In</button>
            </form>

            <div class="signup-link">
                Don't have an account? <a href="register.php">Sign up here</a>
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
