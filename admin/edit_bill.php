<?php
// Include database configuration and helper functions
require_once 'config.php';
require_once 'package_functions.php';
require_once 'chat_functions.php';

// Check if admin is logged in
require_admin_login();

// Initialize variables
$success_message = '';
$error_message = '';
$bill = null;

// Check if bill ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error_message = "Invalid bill ID.";
} else {
    $bill_id = intval($_GET['id']);
    
    // Handle form submission for bill update
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_bill'])) {
        $customer_name = sanitize_input($_POST['customer_name']);
        $email = sanitize_input($_POST['email']);
        $mobile = sanitize_input($_POST['mobile']);
        $country = sanitize_input($_POST['country']);
        $arrival_date = sanitize_input($_POST['arrival_date']);
        $departure_date = sanitize_input($_POST['departure_date']);
        $airport_name = sanitize_input($_POST['airport_name']);
        $service_name = sanitize_input($_POST['service_name']);
        $total_price = floatval($_POST['total_price']);
        $description = sanitize_input($_POST['description']);
        
        $update_query = "UPDATE bill_customers SET 
            customer_name = ?, 
            email = ?, 
            mobile = ?, 
            country = ?, 
            arrival_date = ?, 
            departure_date = ?, 
            airport_name = ?, 
            service_name = ?, 
            total_price = ?, 
            description = ? 
            WHERE bill_id = ?";
            
        $update_stmt = mysqli_prepare($conn, $update_query);
        mysqli_stmt_bind_param(
            $update_stmt, 
            "ssssssssdsi", 
            $customer_name, $email, $mobile, $country, $arrival_date,
            $departure_date, $airport_name, $service_name, $total_price, $description, $bill_id
        );
        
        if (mysqli_stmt_execute($update_stmt)) {
            $success_message = "Bill updated successfully!";
            
            // Redirect to view the updated bill
            header("Location: view_bill.php?id=" . $bill_id . "&updated=true");
            exit;
        } else {
            $error_message = "Error updating bill: " . mysqli_error($conn);
        }
    }
    
    // Get bill details
    $bill_query = "SELECT * FROM bill_customers WHERE bill_id = ?";
    $bill_stmt = mysqli_prepare($conn, $bill_query);
    mysqli_stmt_bind_param($bill_stmt, "i", $bill_id);
    mysqli_stmt_execute($bill_stmt);
    $bill_result = mysqli_stmt_get_result($bill_stmt);
    
    if ($bill_result && mysqli_num_rows($bill_result) > 0) {
        $bill = mysqli_fetch_assoc($bill_result);
    } else {
        $error_message = "Bill not found.";
    }
}

// Function to get all packages for the dropdown
function get_all_packages() {
    global $conn;
    $packages = [];
    
    $query = "SELECT package_id, name FROM packages ORDER BY name ASC";
    $result = mysqli_query($conn, $query);
    
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $packages[] = $row;
        }
    }
    
    return $packages;
}

// Get all tour packages for dropdown
$tour_packages = get_all_packages();

// Get unread messages count for admin dashboard
$unread_messages_count = get_unread_count(0, true);

// Get unreadContact Messages count
$contact_messages_query = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
$contact_result = mysqli_query($conn, $contact_messages_query);
$unread_contact_count = 0;
if ($contact_result && $row = mysqli_fetch_assoc($contact_result)) {
    $unread_contact_count = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bill - Adventure Travel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        :root {
            --primary-color: rgb(23, 108, 101);
            --secondary-color: linear-gradient(to right,rgb(0, 255, 204) 0%,rgb(206, 255, 249) 100%);
            --dark-color: #333;
            --light-color: #f4f4f4;
            --danger-color: #dc3545;
            --success-color: #28a745;
        }

        body {
            background-color: rgb(68, 202, 148);
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color:rgb(4, 39, 37);
            color: #fff;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header img {
            height: 60px;
            width: 130px;
            margin-right: 10px;
            border: 2px solid var(--secondary-color);
            border-radius: 8px;
            padding: 5px;
            background-color: rgba(255, 255, 255, 0.74);
            transition: all 0.3s ease;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu ul {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .sidebar-menu .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 50%;
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            min-width: 20px;
            height: 20px;
        }

        .sidebar-menu a.active, .sidebar-menu a:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--secondary-color);
            border-left: 4px solid var(--secondary-color);
        }

        .sidebar-menu a i {
            margin-right: 10px;
        }

        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .header {
            background-color: #fff;
            padding: 15px 20px;
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: var(--dark-color);
            font-size: 1.5rem;
        }

        .admin-user {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-user img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .logout-btn {
            margin-left: 15px;
            padding: 5px 15px;
            background-color: var(--danger-color);
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #c82333;
        }

        /* Form Styles */
        .section {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 15px;
            margin-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .section-header h2 {
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover {
            background-color: #145a55;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #fff;
            margin-right: 10px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Form grid layout */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .form-col {
            flex: 1;
            padding: 0 10px;
            min-width: 250px;
        }

        /* Alert Messages */
        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Error container */
        .error-container {
            background-color: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Responsive design */
        @media (max-width: 991px) {
            .sidebar {
                width: 70px;
                text-align: center;
            }

            .sidebar-header h2, .sidebar-menu a span {
                display: none;
            }
            
            .sidebar-menu a i {
                margin-right: 0;
                font-size: 1.3rem;
            }

            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }

            .form-col {
                width: 100%;
                margin-bottom: 15px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .admin-user {
                width: 100%;
                justify-content: space-between;
            }
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 50%;
            background-color: var(--danger-color);
            color: white;
        }

        /* Message button styles */
        .message-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background-color: var(--primary-color);
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
            white-space: nowrap;
            margin-right: 15px;
        }

        .message-btn:hover {
            background-color: #124d47;
        }

        .message-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            width: 22px;
            height: 22px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            font-size: 0.75rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Form buttons container */
        .form-buttons {
            margin-top: 20px;
            display: flex;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="../images/logo-5.PNG" alt="Adventure Travel Logo">
            </div>
            <div class="sidebar-menu">
                <ul>
                    <li><a href="admin.php"><span><i class="fas fa-home"></i> Dashboard</span></a></li>
                    <li><a href="admin.php#tour-packages-section"><span><i class="fas fa-suitcase"></i> Tour Packages</span></a></li>
                    <li><a href="admin.php#one-day-tour-packages-section"><span><i class="fas fa-clock"></i> One Day Tours</span></a></li>
                    <li><a href="admin.php#special-tour-packages-section"><span><i class="fas fa-star"></i> Special Tours</span></a></li>
                    <li><a href="admin.php#vehicles-section"><span><i class="fas fa-car"></i> Vehicles</span></a></li>
                    <li><a href="admin.php#team-members-section"><span><i class="fas fa-user-tie"></i> Team Members</span></a></li>
                    <li><a href="admin.php#reviews-section"><span><i class="fas fa-star"></i> Reviews</span></a></li>
                    <li><a href="admin.php#videos-section"><span><i class="fas fa-video"></i> Videos</span></a></li>
                    <li><a href="billing.php" class="active"><span><i class="fas fa-file-invoice-dollar"></i> Bills</span></a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Edit Bill</h1>
                <div class="admin-user">
                    <a href="user_messages.php" class="message-btn">
                        <i class="fas fa-comments"></i> User Messages
                        <?php if ($unread_messages_count > 0): ?>
                            <span class="message-badge"><?php echo $unread_messages_count; ?></span>
                        <?php endif; ?>
                    </a>
                    <div style="display: flex; align-items: center;">
                        <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Admin">
                        <span style="margin: 0 10px;"><?php echo isset($_SESSION['admin_username']) ? htmlspecialchars($_SESSION['admin_username']) : 'Admin User'; ?></span>
                        <a href="admin.php?logout=1" class="logout-btn">Logout</a>
                    </div>
                </div>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <!-- Edit Bill Form -->
            <?php if ($bill): ?>
                <div class="section">
                    <div class="section-header">
                        <h2>Edit Bill #<?php echo str_pad($bill['bill_id'], 5, '0', STR_PAD_LEFT); ?></h2>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="customer_name">Customer Name</label>
                                    <input type="text" id="customer_name" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($bill['customer_name']); ?>" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($bill['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="mobile">Mobile Number</label>
                                    <input type="text" id="mobile" name="mobile" class="form-control" value="<?php echo htmlspecialchars($bill['mobile']); ?>" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" id="country" name="country" class="form-control" value="<?php echo htmlspecialchars($bill['country']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="arrival_date">Arrival Date</label>
                                    <input type="date" id="arrival_date" name="arrival_date" class="form-control" value="<?php echo htmlspecialchars($bill['arrival_date']); ?>" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="departure_date">Departure Date</label>
                                    <input type="date" id="departure_date" name="departure_date" class="form-control" value="<?php echo htmlspecialchars($bill['departure_date']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="airport_name">Airport Name</label>
                                    <input type="text" id="airport_name" name="airport_name" class="form-control" value="<?php echo htmlspecialchars($bill['airport_name']); ?>" required>
                                </div>
                            </div>
                            <div class="form-col">
                                <div class="form-group">
                                    <label for="service_name">Service Name</label>
                                    <input type="text" id="service_name" name="service_name" list="service_list" class="form-control" value="<?php echo htmlspecialchars($bill['service_name']); ?>" required>
                                    <datalist id="service_list">
                                        <?php foreach ($tour_packages as $package): ?>
                                            <option value="<?php echo htmlspecialchars($package['name']); ?>">
                                        <?php endforeach; ?>
                                    </datalist>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="total_price">Total Price ($)</label>
                            <input type="number" id="total_price" name="total_price" step="0.01" min="0" class="form-control" value="<?php echo htmlspecialchars($bill['total_price']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Additional Description</label>
                            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($bill['description']); ?></textarea>
                        </div>
                        
                        <div class="form-buttons">
                            <a href="billing.php" class="btn btn-secondary">Cancel</a>
                            <button type="submit" name="update_bill" class="btn btn-primary">Update Bill</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="error-container">
                    <h3>Error</h3>
                    <p><?php echo $error_message; ?></p>
                    <a href="billing.php" class="btn btn-primary">Back to Billing</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Date validation
            const arrivalDateInput = document.getElementById('arrival_date');
            const departureDateInput = document.getElementById('departure_date');
            
            if (arrivalDateInput && departureDateInput) {
                arrivalDateInput.addEventListener('change', function() {
                    departureDateInput.min = this.value;
                    
                    // If departure date is before new arrival date, update it
                    if (departureDateInput.value && departureDateInput.value < this.value) {
                        departureDateInput.value = this.value;
                    }
                });
                
                departureDateInput.addEventListener('change', function() {
                    if (arrivalDateInput.value && this.value < arrivalDateInput.value) {
                        alert("Departure date cannot be earlier than arrival date");
                        this.value = arrivalDateInput.value;
                    }
                });
            }
        });
    </script>
</body>
</html> 