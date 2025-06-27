<?php
session_start();

// Handle redirect parameter
if (isset($_GET['redirect'])) {
    $_SESSION['redirect_after_login'] = $_GET['redirect'];
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($errors)) {
        // In a real application, you would:
        // 1. Hash the password and compare with database
        // 2. Validate against user database
        // 3. Set up proper session management
        
        // For demo purposes, we'll use hardcoded credentials
        $valid_users = [
            'admin@busgo.com' => 'admin123',
            'user@busgo.com' => 'user123',
            'demo@busgo.com' => 'demo123'
        ];
        
        if (isset($valid_users[$email]) && $valid_users[$email] === $password) {
            // Successful login
            $_SESSION['user'] = [
                'email' => $email,
                'name' => ucfirst(explode('@', $email)[0]),
                'logged_in' => true,
                'login_time' => date('Y-m-d H:i:s')
            ];
            
            // Redirect to dashboard or previous page
            $redirect_url = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            
            if (isset($_POST['ajax'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    "success" => true, 
                    "message" => "Login successful",
                    "redirect" => $redirect_url
                ]);
                exit();
            } else {
                header('Location: ' . $redirect_url);
                exit();
            }
        } else {
            $errors[] = "Invalid email or password";
        }
    }
    
    // If AJAX request, return JSON response
    if (isset($_POST['ajax'])) {
        header('Content-Type: application/json');
        echo json_encode([
            "success" => false, 
            "message" => implode(', ', $errors)
        ]);
        exit();
    } else {
        $_SESSION['login_errors'] = $errors;
        $_SESSION['login_form_data'] = $_POST;
    }
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit();
}

// Check if user is already logged in
if (isset($_SESSION['user']) && $_SESSION['user']['logged_in']) {
    header('Location: index.php');
    exit();
}

// Get errors and form data
$errors = $_SESSION['login_errors'] ?? [];
$form_data = $_SESSION['login_form_data'] ?? [];
unset($_SESSION['login_errors'], $_SESSION['login_form_data']);

// Demo credentials for display
$demo_credentials = [
    ['email' => 'admin@busgo.com', 'password' => 'admin123', 'role' => 'Administrator'],
    ['email' => 'user@busgo.com', 'password' => 'user123', 'role' => 'Regular User'],
    ['email' => 'demo@busgo.com', 'password' => 'demo123', 'role' => 'Demo User']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to BusGo - Your Travel Partner</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .header {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .logo i {
            margin-right: 0.5rem;
            font-size: 2rem;
        }
        
        .nav {
            display: flex;
            list-style: none;
            gap: 2rem;
        }
        
        .nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .nav a:hover {
            color: #ffd700;
        }
        
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: calc(100vh - 80px);
            padding: 2rem 0;
        }
        
        .login-container {
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            backdrop-filter: blur(10px);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .login-header h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #666;
            font-size: 1.1rem;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 1rem;
            padding-left: 3rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
        }
        
        .form-group i {
            position: absolute;
            left: 1rem;
            top: 2.3rem;
            color: #667eea;
            font-size: 1.1rem;
        }
        
        .login-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-bottom: 1.5rem;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        
        .login-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .forgot-password {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .forgot-password a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .forgot-password a:hover {
            text-decoration: underline;
        }
        
        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }
        
        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0e0e0;
        }
        
        .divider span {
            background: white;
            padding: 0 1rem;
            color: #666;
        }
        
        .demo-credentials {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        
        .demo-credentials h4 {
            color: #333;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .demo-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            background: white;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        
        .demo-item .credentials {
            display: flex;
            flex-direction: column;
        }
        
        .demo-item .role {
            color: #667eea;
            font-weight: 500;
        }
        
        .signup-link {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .signup-link p {
            color: #666;
            margin-bottom: 1rem;
        }
        
        .signup-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .features-preview {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-left: 2rem;
            width: 300px;
        }
        
        .features-preview h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .feature-item i {
            font-size: 1.2rem;
            margin-right: 1rem;
            width: 20px;
        }
        
        @media (max-width: 768px) {
            .main-content {
                flex-direction: column;
            }
            
            .features-preview {
                margin-left: 0;
                margin-top: 2rem;
                width: 100%;
            }
            
            .nav {
                display: none;
            }
            
            .login-container {
                margin: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-bus"></i>
                    BusGo
                </div>
                <nav class="nav">
                    <a href="index.php">Home</a>
                    <a href="product.php">Routes</a>
                    <a href="about.html">About</a>
                    <a href="contact.php">Contact</a>
                    <a href="login.php"><i class="fas fa-user"></i> Login</a>
                    <a href="bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <div class="login-container">
            <div class="login-header">
                <h2>Welcome Back</h2>
                <p>Sign in to your BusGo account</p>
            </div>
            
            <!-- Error Messages -->
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo implode('<br>', array_map('htmlspecialchars', $errors)); ?>
                </div>
            <?php endif; ?>
            
            <!-- Demo Credentials -->
            <div class="demo-credentials">
                <h4><i class="fas fa-info-circle"></i> Demo Credentials</h4>
                <?php foreach ($demo_credentials as $demo): ?>
                <div class="demo-item">
                    <div class="credentials">
                        <span><?php echo htmlspecialchars($demo['email']); ?></span>
                        <span>Password: <?php echo htmlspecialchars($demo['password']); ?></span>
                    </div>
                    <div class="role"><?php echo htmlspecialchars($demo['role']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <form method="POST" action="" id="loginForm">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="Enter your email" 
                           value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="login-btn" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <div class="forgot-password">
                <a href="#">Forgot your password?</a>
            </div>
            
            <div class="signup-link">
                <p>Don't have an account?</p>
                <a href="signup.html">Create a new account</a>
            </div>
        </div>
        
        <div class="features-preview">
            <h3>Why Choose BusGo?</h3>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <span>Safe & Secure Booking</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-clock"></i>
                <span>Real-time Bus Tracking</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-mobile-alt"></i>
                <span>Easy Mobile Tickets</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                <span>24/7 Customer Support</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-money-bill-wave"></i>
                <span>Best Price Guarantee</span>
            </div>
        </div>
    </div>

    <script>
        // Auto-fill demo credentials when clicked
        document.querySelectorAll('.demo-item').forEach(item => {
            item.addEventListener('click', function() {
                const email = this.querySelector('.credentials span:first-child').textContent;
                const password = this.querySelector('.credentials span:last-child').textContent.replace('Password: ', '');
                
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
            });
        });

        // Form submission with loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        });
    </script>
</body>
</html>
