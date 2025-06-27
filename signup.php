<?php
session_start();

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];

// Determine if request is JSON or form data
$content_type = $_SERVER['CONTENT_TYPE'] ?? '';
$is_json = strpos($content_type, 'application/json') !== false;

if ($method === 'POST') {
    if ($is_json) {
        // Handle JSON request (AJAX)
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
    } else {
        // Handle form submission
        $input = $_POST;
    }
    
    // Validate required fields
    $required_fields = ['fullName', 'email', 'phone', 'password'];
    $errors = [];
    
    foreach ($required_fields as $field) {
        if (!isset($input[$field]) || empty(trim($input[$field]))) {
            $errors[] = ucfirst($field) . ' is required';
        }
    }
    
    // Validate email format
    if (isset($input['email']) && !filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format';
    }
    
    // Validate password strength
    if (isset($input['password']) && strlen($input['password']) < 8) {
        $errors[] = 'Password must be at least 8 characters long';
    }
    
    // Validate phone number (basic validation)
    if (isset($input['phone']) && !preg_match('/^[\+]?[1-9][\d]{0,15}$/', $input['phone'])) {
        $errors[] = 'Invalid phone number format';
    }
    
    if (!empty($errors)) {
        if ($is_json) {
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit;
        } else {
            // For form submission, store errors in session and redirect
            $_SESSION['signup_errors'] = $errors;
            $_SESSION['form_data'] = $input;
            header('Location: signup.html?error=1');
            exit;
        }
    }
    
    // Check if user already exists (simple session-based check)
    if (!isset($_SESSION['users'])) {
        $_SESSION['users'] = [];
    }
    
    $email = strtolower(trim($input['email']));
    
    // Check for existing user
    foreach ($_SESSION['users'] as $user) {
        if (strtolower($user['email']) === $email) {
            if ($is_json) {
                echo json_encode([
                    'success' => false,
                    'errors' => ['Email address already registered']
                ]);
                exit;
            } else {
                $_SESSION['signup_errors'] = ['Email address already registered'];
                $_SESSION['form_data'] = $input;
                header('Location: signup.html?error=1');
                exit;
            }
        }
    }
    
    // Create new user
    $newUser = [
        'id' => uniqid(),
        'fullName' => trim($input['fullName']),
        'email' => $email,
        'phone' => trim($input['phone']),
        'password' => password_hash($input['password'], PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s'),
        'verified' => false
    ];
    
    $_SESSION['users'][] = $newUser;
    
    if ($is_json) {
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'user_id' => $newUser['id']
        ]);
    } else {
        // For form submission, redirect to login with success message
        $_SESSION['signup_success'] = 'Account created successfully! Please sign in to continue.';
        header('Location: login.php');
        exit;
    }
    
} else {
    // Handle GET request or other methods
    if ($is_json) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Method not allowed'
        ]);
    } else {
        header('Location: signup.html');
        exit;
    }
}
?>
        'success' => true,
        'message' => 'Account created successfully! You can now sign in.',
        'user' => [
            'id' => $newUser['id'],
            'fullName' => $newUser['fullName'],
            'email' => $newUser['email'],
            'phone' => $newUser['phone']
        ]
    ]);
    
} else {
    // GET request - return signup page stats or redirect
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
