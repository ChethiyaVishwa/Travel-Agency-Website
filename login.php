<?php
// Include database configuration
require_once 'admin/config.php';

// Start the session if one doesn't exist already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Initialize variables
$username = '';
$email = '';
$fullname = '';
$phone = '';
$address = '';
$errors = [];
$form_type = isset($_GET['form']) ? $_GET['form'] : 'login';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which form was submitted
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'login') {
            // Login form processing
            // Get form data and sanitize
            $username = sanitize_input($_POST['username']);
            $password = $_POST['password'];
            
            // Form validation
            if (empty($username)) {
                $errors[] = "Username is required";
            }
            
            if (empty($password)) {
                $errors[] = "Password is required";
            }
            
            // Check if table exists
            $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
            if (mysqli_num_rows($check_table) == 0) {
                $errors[] = "User database not found. Please register first.";
            }
            
            // If no errors, proceed with login
            if (empty($errors)) {
                $query = "SELECT * FROM users WHERE username = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "s", $username);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) === 1) {
                    $user = mysqli_fetch_assoc($result);
                    
                    // Verify password
                    if (password_verify($password, $user['password'])) {
                        // Password is correct, set session variables
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['full_name'] = $user['full_name'];
                        $_SESSION['is_admin'] = $user['is_admin'];
                        
                        // Redirect to home page
                        header("Location: index.php");
                        exit;
                    } else {
                        $errors[] = "Invalid password";
                    }
                } else {
                    $errors[] = "User not found";
                }
            }
        } else if ($_POST['action'] == 'register') {
            // Registration form processing
            // Get form data and sanitize
            $username = sanitize_input($_POST['username']);
            $email = sanitize_input($_POST['email']);
            $fullname = sanitize_input($_POST['fullname']);
            $phone = sanitize_input($_POST['phone']);
            $address = sanitize_input($_POST['address']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            
            // Form validation
            if (empty($username)) {
                $errors[] = "Username is required";
            }
            
            if (empty($email)) {
                $errors[] = "Email is required";
            } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Invalid email format";
            }
            
            if (empty($fullname)) {
                $errors[] = "Full name is required";
            }
            
            if (empty($password)) {
                $errors[] = "Password is required";
            }
            
            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match";
            }
            
            // Check if username already exists
            $check_query = "SELECT * FROM users WHERE username = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "s", $username);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                $errors[] = "Username already exists";
            }
            
            // Check if email already exists
            $check_query = "SELECT * FROM users WHERE email = ?";
            $check_stmt = mysqli_prepare($conn, $check_query);
            mysqli_stmt_bind_param($check_stmt, "s", $email);
            mysqli_stmt_execute($check_stmt);
            $check_result = mysqli_stmt_get_result($check_stmt);
            
            if (mysqli_num_rows($check_result) > 0) {
                $errors[] = "Email already exists";
            }
            
            // If no errors, proceed with registration
            if (empty($errors)) {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert new user
                $query = "INSERT INTO users (username, email, full_name, phone, address, password, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "ssssss", $username, $email, $fullname, $phone, $address, $hashed_password);
                
                if (mysqli_stmt_execute($stmt)) {
                    // Set success message and redirect to login
                    $_SESSION['registration_success'] = "Registration successful! Please log in.";
                    header("Location: login.php");
                    exit;
                } else {
                    $errors[] = "Registration failed: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>Adventure Travel - Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap">
    <link rel="icon" href="images/domain-img.png" type="image/x-icon">
    <style>
        /* Page Loader Styles */
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .loader {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .loader-text {
            font-family: 'Dancing Script', cursive;
            font-size: 28px;
            font-weight: bold;
            color: rgb(0, 255, 204);
            margin-bottom: 15px;
            letter-spacing: 2px;
        }
        
        .loader-dots {
            display: flex;
            gap: 8px;
        }
        
        .dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: rgb(0, 255, 204);
            animation: bounce 1.4s infinite ease-in-out both;
        }
        
        .dot:nth-child(1) {
            animation-delay: -0.32s;
        }
        
        .dot:nth-child(2) {
            animation-delay: -0.16s;
        }
        
        @keyframes bounce {
            0%, 80%, 100% { 
                transform: scale(0);
                opacity: 0.5;
            }
            40% { 
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .page-loader.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: 'Josefin Sans', 'Arial', sans-serif;
            height: 100vh;
            overflow: hidden;
            background: url('https://images.unsplash.com/photo-1519681393784-d120267933ba?q=80&w=2070') no-repeat center center;
            background-size: cover;
            scroll-behavior: smooth; /* Enable smooth scrolling */
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.3);
        }

        .login-container {
            display: flex;
            height: 100vh;
            width: 100%;
            position: relative;
            z-index: 1;
            padding-top: 50px; /* Add more space at the top to push content down */
        }

        .travel-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-left: 10%;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.6);
        }

        .travel-info h1 {
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0;
        }

        .travel-info h2 {
            font-size: 3.5rem;
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 10px;
            line-height: 1.1;
        }

        .travel-info p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        .login-form-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-form {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 16px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .form-control {
            height: 45px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            transition: all 0.3s;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .form-control:focus {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
        }
        
        textarea.form-control {
            min-height: 80px;
        }

        .btn-primary {
            background: linear-gradient(to right, rgb(0, 255, 204) 0%, rgb(206, 255, 249) 100%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            height: 45px;
            border-radius: 8px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            color: rgba(0, 59, 47, 0.8);
        }

        .btn-primary:hover {
            background: linear-gradient(to right, rgb(0, 230, 184) 0%, rgb(186, 255, 245) 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 255, 204, 0.2);
            color: rgba(0, 59, 47, 1);
        }

        #login-form, #register-form {
            display: none;
            animation: fadeIn 0.5s forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #fff;
            font-weight: 500;
        }

        .form-footer a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover {
            text-decoration: underline;
            color: #fff;
        }
        
        .form-check-label {
            color: #fff;
        }
        
        .alert {
            background: rgba(220, 53, 69, 0.2);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #fff;
        }
        
        .alert-success {
            background: rgba(25, 135, 84, 0.2);
            border: 1px solid rgba(25, 135, 84, 0.3);
            color: #fff;
        }
        
        .text-center.mt-4 .btn-outline-secondary {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .text-center.mt-4 .btn-outline-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
            text-decoration: none;
        }
        
        a {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
        }
        
        a:hover {
            color: #fff;
            text-decoration: underline;
        }
        
        /* Prevent underline on button hover */
        .btn:hover {
            text-decoration: none;
        }

        /* Mobile responsiveness */
        @media (max-width: 992px) {
            body {
                overflow-y: auto; /* Enable scrolling on mobile */
                -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
            }
            
            .login-container {
                flex-direction: column;
                overflow-y: auto;
                padding-top: 40px; /* Keep some spacing on medium screens */
                min-height: 100vh; /* Ensure full height */
            }

            .travel-info {
                padding: 40px 20px;
                text-align: center;
            }

            .login-form-container {
                padding: 40px 20px;
            }
        }
        
        /* Tablet and smaller screens */
        @media (max-width: 768px) {
            body {
                overflow-y: auto;
            }
            
            .travel-info {
                padding: 30px 15px;
            }
            
            .travel-info h2 {
                font-size: 2.5rem;
            }
            
            .travel-info p {
                font-size: 1rem;
            }
            
            .login-form {
                padding: 25px;
            }
        }
        
        /* Mobile phones */
        @media (max-width: 576px) {
            body {
                height: auto; /* Allow body to expand with content */
            }
            
            .travel-info h1 {
                font-size: 1.2rem;
            }
            
            .travel-info h2 {
                font-size: 2rem;
            }
            
            .travel-info {
                padding: 20px 15px;
                flex: 0 0 auto; /* Prevent travel info from taking too much space */
            }
            
            .login-container {
                justify-content: flex-start; /* Align content from the top */
                padding-top: 60px; /* Increase space at the top */
                height: auto; /* Allow container to expand with content */
                min-height: 100vh; /* Ensure minimum height */
            }
            
            .login-form-container {
                padding: 0 15px 30px;
                flex: 1;
                align-items: flex-start; /* Align form to the top */
                margin-top: 20px; /* Add space between info and form */
            }
            
            .login-form {
                padding: 20px;
            }
            
            .form-control {
                height: 40px;
                margin-bottom: 15px;
            }
            
            .btn-primary {
                height: 40px;
            }
        }
        
        /* Very small screens - mobile phones */
        @media (max-width: 375px) {
            .travel-info h1 {
                font-size: 1.1rem;
            }
            
            .travel-info h2 {
                font-size: 1.8rem;
            }
            
            .travel-info {
                padding: 10px 15px; /* Reduce padding to push form higher */
            }
            
            .login-form-container {
                margin-top: 10px; /* Adjust for very small screens */
            }
            
            .login-form {
                padding: 15px;
            }
            
            .form-control {
                height: 38px;
                margin-bottom: 12px;
            }
            
            .btn-primary {
                height: 38px;
                font-size: 0.9rem;
            }
            
            .form-footer {
                font-size: 0.9rem;
            }
        }
        
        /* Landscape orientation on mobile */
        @media (max-height: 500px) and (orientation: landscape) {
            .login-container {
                flex-direction: row;
            }
            
            .travel-info {
                padding: 10px 20px;
            }
            
            .travel-info h1 {
                font-size: 1rem;
            }
            
            .travel-info h2 {
                font-size: 1.8rem;
                margin-bottom: 3px;
            }
            
            .travel-info p {
                font-size: 0.9rem;
                margin-bottom: 8px;
            }
            
            .login-form {
                padding: 12px;
                max-height: 90vh;
                overflow-y: auto;
            }
            
            .form-control, .btn-primary {
                height: 36px;
                margin-bottom: 8px;
            }
            
            .d-flex.justify-content-between.mb-3 {
                margin-bottom: 8px !important;
            }
        }
        
        /* Larger screens */
        @media (min-width: 1200px) {
            .login-form {
                max-width: 450px;
            }
            
            .travel-info h1 {
                font-size: 1.8rem;
            }
            
            .travel-info h2 {
                font-size: 4rem;
            }
            
            .travel-info p {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <!-- Page Loader -->
    <div class="page-loader">
        <div class="loader">
            <div class="loader-text">Adventure Travel</div>
            <div class="loader-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>
    </div>
    
    <div class="overlay"></div>
    <div class="login-container">
        <div class="travel-info">
            <h1>Travel</h1>
            <h2>Explore Horizons</h2>
            <p>Where Your Dream Destinations<br>Become Reality</p>
        </div>

        <div class="login-form-container">
            <div class="login-form">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['registration_success'])): ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['registration_success']; ?>
                        <?php unset($_SESSION['registration_success']); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?php 
    // Use HTTPS if available, otherwise fallback to current protocol
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    echo $protocol . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER["PHP_SELF"]);
?>" id="login-form" style="<?php echo $form_type === 'login' ? 'display:block;' : ''; ?>" autocomplete="off">
                    <input type="hidden" name="action" value="login">
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Username" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password" required>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember password</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Sign In</button>
                    
                    <div class="form-footer">
                        Don't have an account? <a href="?form=register" id="show-register">Create an Account</a>
                    </div>
                </form>
                
                <form method="POST" action="<?php 
    // Use HTTPS if available, otherwise fallback to current protocol
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    echo $protocol . $_SERVER['HTTP_HOST'] . htmlspecialchars($_SERVER["PHP_SELF"]);
?>" id="register-form" style="<?php echo $form_type === 'register' ? 'display:block;' : ''; ?>" autocomplete="off">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Full Name" id="fullname" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <input type="text" class="form-control" placeholder="Username" id="reg-username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <input type="tel" class="form-control" placeholder="Phone Number" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <textarea class="form-control" placeholder="Address" id="address" name="address" rows="2" required><?php echo isset($address) ? htmlspecialchars($address) : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <input type="password" class="form-control" placeholder="Password" id="reg-password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <input type="password" class="form-control" placeholder="Confirm Password" id="confirm-password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Create Account</button>
                    
                    <div class="form-footer">
                        Already have an account? <a href="?form=login" id="show-login">Sign In</a>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Page loader
            setTimeout(function() {
                document.querySelector('.page-loader').classList.add('fade-out');
                setTimeout(function() {
                    document.querySelector('.page-loader').style.display = 'none';
                }, 500);
            }, 1000);
            
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const showRegisterLink = document.getElementById('show-register');
            const showLoginLink = document.getElementById('show-login');
            
            if (showRegisterLink) {
                showRegisterLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    loginForm.style.display = 'none';
                    registerForm.style.display = 'block';
                    history.pushState(null, null, '?form=register');
                });
            }
            
            if (showLoginLink) {
                showLoginLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    registerForm.style.display = 'none';
                    loginForm.style.display = 'block';
                    history.pushState(null, null, '?form=login');
                });
            }
            
            // Show correct form on page load
            if ('<?php echo $form_type; ?>' === 'register') {
                loginForm.style.display = 'none';
                registerForm.style.display = 'block';
            } else {
                loginForm.style.display = 'block';
                registerForm.style.display = 'none';
            }
        });
    </script>
</body>
</html>
