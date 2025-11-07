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
$search_id = '';

// Check if bill_customers table exists, create if not
function create_bills_table_if_not_exists() {
    global $conn;
    $check_table = "SHOW TABLES LIKE 'bill_customers'";
    $table_exists = mysqli_query($conn, $check_table);
    
    if (mysqli_num_rows($table_exists) == 0) {
        $create_table = "CREATE TABLE bill_customers (
            bill_id INT(11) AUTO_INCREMENT PRIMARY KEY,
            customer_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            mobile VARCHAR(50) NOT NULL,
            country VARCHAR(100) NOT NULL,
            arrival_date DATE NOT NULL,
            departure_date DATE NOT NULL,
            airport_name VARCHAR(255) NOT NULL,
            service_name VARCHAR(255) NOT NULL,
            total_price DECIMAL(10,2) NOT NULL,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        if (mysqli_query($conn, $create_table)) {
            return true;
        } else {
            return false;
        }
    }
    return true;
}

// Initialize bills table
create_bills_table_if_not_exists();

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

// Get unread messages count for admin dashboard
$unread_messages_count = get_unread_count(0, true);

// Get unreadContact Messages count
$contact_messages_query = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
$contact_result = mysqli_query($conn, $contact_messages_query);
$unread_contact_count = 0;
if ($contact_result && $row = mysqli_fetch_assoc($contact_result)) {
    $unread_contact_count = $row['count'];
}

// Get all tour packages for dropdown
$tour_packages = get_all_packages();

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new bill
    if (isset($_POST['add_bill'])) {
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
        
        $insert_query = "INSERT INTO bill_customers (
            customer_name, email, mobile, country, arrival_date, 
            departure_date, airport_name, service_name, total_price, description
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = mysqli_prepare($conn, $insert_query);
        mysqli_stmt_bind_param(
            $insert_stmt, 
            "ssssssssds", 
            $customer_name, $email, $mobile, $country, $arrival_date,
            $departure_date, $airport_name, $service_name, $total_price, $description
        );
        
        if (mysqli_stmt_execute($insert_stmt)) {
            $success_message = "Bill created successfully!";
            $bill_id = mysqli_insert_id($conn);
            // Redirect to view the bill
            header("Location: view_bill.php?id=" . $bill_id);
            exit;
        } else {
            $error_message = "Error creating bill: " . mysqli_error($conn);
        }
    }
    
    // Delete bill
    if (isset($_POST['delete_bill'])) {
        $bill_id = intval($_POST['bill_id']);
        
        $delete_query = "DELETE FROM bill_customers WHERE bill_id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $bill_id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            $success_message = "Bill deleted successfully!";
        } else {
            $error_message = "Error deleting bill: " . mysqli_error($conn);
        }
    }
}

// Handle search
if (isset($_GET['search_id']) && !empty($_GET['search_id'])) {
    $search_id = intval($_GET['search_id']);
    $bills_query = "SELECT * FROM bill_customers WHERE bill_id = $search_id ORDER BY created_at DESC";
} else {
$bills_query = "SELECT * FROM bill_customers ORDER BY created_at DESC";
}

$bills_result = mysqli_query($conn, $bills_query);
$bills = [];
if ($bills_result) {
    while ($bill = mysqli_fetch_assoc($bills_result)) {
        $bills[] = $bill;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adventure Travel - Billing Management</title>
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

        /* Dropdown menu */
        .dropdown-container {
            display: none;
            background-color: rgba(0, 0, 0, 0.2);
            padding-left: 0;
        }
        
        .dropdown-container a {
            padding-left: 35px;
            font-size: 0.95em;
        }
        
        .dropdown-btn {
            position: relative;
        }
        
        .dropdown-btn::after {
            content: '\f107';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            position: absolute;
            right: 20px;
            transition: all 0.3s ease;
        }
        
        .dropdown-btn.active::after {
            transform: rotate(180deg);
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

        .sidebar-header img:hover {
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(101, 255, 193, 0.5);
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

        /* Badge styles for consistent positioning */
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            color: #fff;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative; /* Ensure position relative for badge positioning */
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

        .admin-user span {
            font-weight: bold;
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

        /* Bill Section Styles */
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

        .add-btn {
            padding: 8px 15px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-btn:hover {
            background-color: #145a55;
        }

        /* Bills Table Styles */
        .bills-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        .bills-table th, .bills-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .bills-table th {
            background-color: var(--primary-color);
            color: #fff;
            font-weight: 600;
            white-space: nowrap;
        }
        
        .bills-table tr:last-child td {
            border-bottom: none;
        }
        
        .bills-table tr:hover {
            background-color: rgba(101, 255, 193, 0.1);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            overflow: auto;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            width: 70%;
            max-width: 800px;
            border-radius: 5px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s;
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .modal-header h2 {
            color: var(--primary-color);
        }

        .close-btn {
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
        }

        .close-btn:hover {
            color: var(--danger-color);
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

        .form-submit {
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .form-submit:hover {
            background-color: #145a55;
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

        /* Action Buttons */
        .action-btns {
            display: flex;
            gap: 5px;
        }

        .view-btn, .edit-btn, .delete-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .view-btn {
            background-color: var(--primary-color);
            color: #fff;
        }

        .view-btn:hover {
            background-color: #145a55;
        }

        .edit-btn {
            background-color: #f0ad4e;
            color: #fff;
        }

        .edit-btn:hover {
            background-color: #ec971f;
        }

        .delete-btn {
            background-color: var(--danger-color);
            color: #fff;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        /* Search Box Styles */
        .search-box {
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-input-group {
            display: flex;
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(23, 108, 101, 0.2);
            outline: none;
        }

        .search-btn {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            padding: 0 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            background-color: #145a55;
        }

        .reset-search {
            padding: 8px 15px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .reset-search:hover {
            background-color: #5a6268;
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
            
            .modal-content {
                width: 95%;
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
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
            animation: pulse 1.5s infinite;
            z-index: 10;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
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
                    <li>
                        <a href="javascript:void(0);" class="dropdown-btn"><span><i class="fas fa-cog"></i> Manage</span></a>
                        <div class="dropdown-container">
                            <a href="manage_destinations.php"><span><i class="fas fa-map-marker-alt"></i> Destinations</span></a>
                            <a href="manage_hotels.php"><span><i class="fas fa-hotel"></i> Hotels</span></a>
                            <a href="manage_policies.php"><span><i class="fas fa-file-alt"></i> Policies</span></a>
                            <a href="manage_admins.php"><span><i class="fas fa-users-cog"></i> Admins</span></a>
                        </div>
                    </li>
                    <li><a href="user_messages.php"><span><i class="fas fa-comment-dots"></i> User Messages</span> 
                        <?php if ($unread_messages_count > 0): ?>
                            <span class="badge" style="background-color: rgb(255, 255, 255); color:rgb(255, 0, 0);"><?php echo $unread_messages_count; ?></span>
                        <?php endif; ?>
                    </a></li>
                    <li><a href="contact_messages.php"><span><i class="fas fa-envelope"></i> Contact Messages</span>
                        <?php if ($unread_contact_count > 0): ?>
                            <span class="badge" style="background-color: rgb(255, 0, 0); color:white;"><?php echo $unread_contact_count; ?></span>
                        <?php endif; ?>
                    </a></li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Billing Management</h1>
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

            <!-- Bills Section -->
            <div class="section">
                <div class="section-header">
                    <h2>Customer Bills</h2>
                    <button class="add-btn" onclick="openModal('billModal')">Create New Bill</button>
                </div>

                <!-- Search Form -->
                <div class="search-box">
                    <form action="" method="GET" class="search-form">
                        <div class="search-input-group">
                            <input type="number" name="search_id" placeholder="Search by Bill ID" value="<?php echo $search_id; ?>" class="search-input">
                            <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                        </div>
                        <?php if(!empty($search_id)): ?>
                            <a href="billing.php" class="reset-search">Reset</a>
                        <?php endif; ?>
                    </form>
                </div>

                <?php if (count($bills) > 0): ?>
                    <table class="bills-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Service</th>
                                <th>Arrival Date</th>
                                <th>Departure Date</th>
                                <th>Total Price</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bills as $bill): ?>
                                <tr>
                                    <td><?php echo $bill['bill_id']; ?></td>
                                    <td><?php echo htmlspecialchars($bill['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($bill['service_name']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($bill['arrival_date'])); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($bill['departure_date'])); ?></td>
                                    <td>$<?php echo number_format($bill['total_price'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($bill['created_at'])); ?></td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="view_bill.php?id=<?php echo $bill['bill_id']; ?>" class="view-btn">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="edit_bill.php?id=<?php echo $bill['bill_id']; ?>" class="edit-btn">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button type="button" class="delete-btn" onclick="deleteBill(<?php echo $bill['bill_id']; ?>, '<?php echo htmlspecialchars($bill['customer_name']); ?>')">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div style="text-align: center; padding: 30px;">
                        <i class="fas fa-file-invoice-dollar" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                        <p style="font-size: 18px; color: #666;">No bills found. Create your first bill using the "Create New Bill" button.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Add Bill Modal -->
    <div id="billModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Create New Bill</h2>
                <span class="close-btn" onclick="closeModal('billModal')">&times;</span>
            </div>
            <form id="billForm" method="POST" enctype="multipart/form-data">
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="customer_name">Customer Name</label>
                            <input type="text" id="customer_name" name="customer_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="mobile">Mobile Number</label>
                            <input type="text" id="mobile" name="mobile" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="country">Country</label>
                            <input type="text" id="country" name="country" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="arrival_date">Arrival Date</label>
                            <input type="date" id="arrival_date" name="arrival_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="departure_date">Departure Date</label>
                            <input type="date" id="departure_date" name="departure_date" class="form-control" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="airport_name">Airport Name</label>
                            <input type="text" id="airport_name" name="airport_name" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-col">
                        <div class="form-group">
                            <label for="service_name">Service Name</label>
                            <input type="text" id="service_name" name="service_name" list="service_list" class="form-control" required>
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
                    <input type="number" id="total_price" name="total_price" step="0.01" min="0" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Additional Description</label>
                    <textarea id="description" name="description" class="form-control">Thank you for choosing Adventure Travel. We look forward to serving you again!</textarea>
                </div>
                
                <input type="hidden" name="add_bill" value="1">
                <button type="submit" class="form-submit">Create Bill</button>
            </form>
        </div>
    </div>

    <!-- Delete Bill Confirmation Modal -->
    <div id="deleteBillModal" class="modal">
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header">
                <h2>Confirm Deletion</h2>
                <span class="close-btn" onclick="closeModal('deleteBillModal')">&times;</span>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the bill for: <span id="deleteBillCustomerName"></span>?</p>
                <p>This action cannot be undone.</p>
            </div>
            <div class="modal-footer" style="display: flex; justify-content: flex-end; margin-top: 20px;">
                <button class="btn" style="background: #ccc; margin-right: 10px;" onclick="closeModal('deleteBillModal')">Cancel</button>
                <form id="deleteBillForm" method="POST">
                    <input type="hidden" id="bill_id" name="bill_id">
                    <input type="hidden" name="delete_bill" value="1">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
            document.body.style.overflow = "hidden"; // Prevent scrolling when modal is open
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
            document.body.style.overflow = ""; // Restore scrolling
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = "none";
                document.body.style.overflow = ""; // Restore scrolling
            }
        }

        // Delete bill confirmation
        function deleteBill(billId, customerName) {
            document.getElementById('bill_id').value = billId;
            document.getElementById('deleteBillCustomerName').textContent = customerName;
            openModal('deleteBillModal');
        }
        
        // Setup dropdown menu
        document.addEventListener('DOMContentLoaded', function() {
            var dropdown = document.querySelector('.dropdown-btn');
            if (dropdown) {
                dropdown.addEventListener("click", function() {
                    this.classList.toggle("active");
                    var dropdownContent = document.querySelector(".dropdown-container");
                    if (dropdownContent.style.display === "block") {
                        dropdownContent.style.display = "none";
                    } else {
                        dropdownContent.style.display = "block";
                    }
                });
            }
            
            // Date validation
            const arrivalDateInput = document.getElementById('arrival_date');
            const departureDateInput = document.getElementById('departure_date');
            
            if (arrivalDateInput && departureDateInput) {
                arrivalDateInput.addEventListener('change', function() {
                    departureDateInput.min = this.value;
                });
                
                departureDateInput.addEventListener('change', function() {
                    if (arrivalDateInput.value && this.value < arrivalDateInput.value) {
                        alert("Departure date cannot be earlier than arrival date");
                        this.value = arrivalDateInput.value;
                    }
                });
                
                // Set default dates
                const today = new Date();
                const tomorrow = new Date(today);
                tomorrow.setDate(today.getDate() + 1);
                
                // Format dates as YYYY-MM-DD for the input fields
                const formatDate = (date) => {
                    const d = new Date(date);
                    let month = '' + (d.getMonth() + 1);
                    let day = '' + d.getDate();
                    const year = d.getFullYear();
                    
                    if (month.length < 2) month = '0' + month;
                    if (day.length < 2) day = '0' + day;
                    
                    return [year, month, day].join('-');
                };
                
                // Set min attribute to today
                const todayFormatted = formatDate(today);
                arrivalDateInput.min = todayFormatted;
                departureDateInput.min = todayFormatted;
                
                // Optionally set default values
                if (!arrivalDateInput.value) arrivalDateInput.value = todayFormatted;
                if (!departureDateInput.value) departureDateInput.value = formatDate(tomorrow);
            }
        });
    </script>
</body>
</html> 