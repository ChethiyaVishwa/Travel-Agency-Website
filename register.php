<?php
// Include database configuration
require_once 'admin/config.php';

// Initialize variables
$username = $email = $full_name = $phone = $address = '';
$errors = [];
$success_message = '';

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = sanitize_input($_POST['full_name']);
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    
    // Form validation
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    if (empty($full_name)) {
        $errors[] = "Full name is required";
    }
    
    // Check if table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'users'");
    if (mysqli_num_rows($check_table) == 0) {
        // Create users table if it doesn't exist
        $create_table_sql = "CREATE TABLE IF NOT EXISTS users (
            user_id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            full_name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            is_admin BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB AUTO_INCREMENT=1;";
        
        if (!mysqli_query($conn, $create_table_sql)) {
            $errors[] = "Database setup error: " . mysqli_error($conn);
        }
    }
    
    // Check if username or email already exists
    if (empty($errors)) {
        $check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
        $check_stmt = mysqli_prepare($conn, $check_query);
        mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
        mysqli_stmt_execute($check_stmt);
        $result = mysqli_stmt_get_result($check_stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if ($user['username'] === $username) {
                $errors[] = "Username already exists";
            }
            if ($user['email'] === $email) {
                $errors[] = "Email already registered";
            }
        }
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into database
        $insert_query = "INSERT INTO users (username, password, email, full_name, phone, address) 
                         VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param($insert_stmt, "ssssss", $username, $hashed_password, $email, $full_name, $phone, $address);
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $success_message = "Registration successful! You can now <a href='login.php'>login</a>.";
            // Clear form data
            $username = $email = $full_name = $phone = $address = '';
        } else {
            $errors[] = "Registration failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Adventure Travels</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: rgba(101, 255, 193, 0.9);
            font-family: 'Arial', sans-serif;
        }
        .register-container {
            max-width: 600px;
            margin: 30px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 2px solid rgb(23, 108, 101);
        }
        .register-header {
            background: linear-gradient(135deg, rgb(23, 108, 101), rgb(101, 255, 193));
            color: #fff;
            padding: 15px;
            text-align: center;
        }
        .register-header h1 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }
        .register-header p {
            font-size: 0.9rem;
            margin-bottom: 0;
        }
        .register-form {
            padding: 20px;
        }
        .form-control {
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
        }
        .form-control:focus {
            border-color: rgb(23, 108, 101);
            box-shadow: 0 0 0 0.25rem rgba(23, 108, 101, 0.25);
        }
        .btn-primary {
            background-color: rgb(23, 108, 101);
            border-color: rgb(23, 108, 101);
            padding: 0.375rem 0.75rem;
            font-size: 0.9rem;
        }
        .btn-primary:hover {
            background-color: rgb(19, 89, 83);
            border-color: rgb(19, 89, 83);
        }
        .alert {
            border-radius: 5px;
            padding: 0.75rem;
            font-size: 0.9rem;
        }
        .register-footer {
            padding: 10px 20px;
            text-align: center;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
        }
        .form-label {
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        .form-text {
            font-size: 0.8rem;
        }
        .mb-3 {
            margin-bottom: 0.75rem !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h1>Create Your Account</h1>
                <p>Join Adventure Travels and start exploring!</p>
            </div>
            
            <div class="register-form">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success">
                        <?php echo $success_message; ?>
                    </div>
                <?php else: ?>
                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                                <div class="form-text">At least 4 characters long.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="form-text">At least 6 characters long.</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($full_name); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($address); ?></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="register-footer">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
