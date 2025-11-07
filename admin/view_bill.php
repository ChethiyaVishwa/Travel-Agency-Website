<?php
// Include database configuration and helper functions
require_once 'config.php';
require_once 'chat_functions.php'; // Add chat functions include

// Check if admin is logged in
require_admin_login();

// Initialize variables
$error_message = '';
$bill = null;

// Check if bill ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $error_message = "Invalid bill ID.";
} else {
    $bill_id = intval($_GET['id']);
    
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

// Get company information
$company_name = "Adventure Travel";
$company_address = "Narammala, Kurunegala, Sri Lanka";
$company_phone = "+94 71 538 0080";
$company_email = "adventuretravelsrilanka@gmail.com";
$company_website = "adventuretravels.wuaze.com";

// Calculate number of days
$nights = 0;
if ($bill) {
    $arrival = new DateTime($bill['arrival_date']);
    $departure = new DateTime($bill['departure_date']);
    $interval = $arrival->diff($departure);
    $nights = $interval->days;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bill #<?php echo isset($bill) ? $bill['bill_id'] : ''; ?> - Adventure Travel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        
        :root {
            --primary-color: rgb(23, 108, 101);
            --secondary-color: linear-gradient(to right,rgb(0, 255, 204) 0%,rgb(206, 255, 249) 100%);
            --dark-color: #333;
            --light-color: #f4f4f4;
            --danger-color: #dc3545;
            --success-color: #28a745;
            --accent-color: #ff9800;
        }
        
        body {
            background-color: #f8f9fa;
            padding: 10px;
            color: #444;
            line-height: 1.5;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .invoice-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
            position: relative;
            border: 1px solid #eaeaea;
        }
        
        .invoice-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            border-radius: 12px 12px 0 0;
        }
        
        .print-only {
            display: none;
        }
        
        .invoice-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 25px;
            border-bottom: 1px solid #eaeaea;
            padding-bottom: 15px;
        }
        
        .company-info {
            flex: 1;
        }
        
        .company-info h1 {
            color: var(--primary-color);
            font-size: 24px;
            margin-bottom: 6px;
            font-weight: 600;
        }
        
        .company-info p {
            margin: 3px 0;
            font-size: 13px;
            color: #666;
            line-height: 1.4;
        }
        
        .logo-container {
            text-align: right;
            padding-left: 20px;
        }
        
        .logo-container img {
            max-width: 150px;
            height: auto;
            border-radius: 0;
            padding: 0;
            background-color: transparent;
            box-shadow: none;
        }
        
        .invoice-title {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }
        
        .invoice-title h2 {
            font-size: 28px;
            color: var(--primary-color);
            position: relative;
            display: inline-block;
            letter-spacing: 2px;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .bill-details {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            background-color: #f9fcfb;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #eaeaea;
        }
        
        .bill-to {
            flex: 1;
            padding-right: 15px;
        }
        
        .bill-info {
            text-align: right;
            flex: 1;
            border-left: 1px solid #eaeaea;
            padding-left: 15px;
        }
        
        .section-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-size: 16px;
            position: relative;
            display: inline-block;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--secondary-color);
        }
        
        .customer-info p {
            margin: 5px 0;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .customer-info strong {
            color: #444;
            font-weight: 600;
        }
        
        .dates-info {
            margin-top: 10px;
            background-color: #f2f9f8;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #e0f0ee;
        }
        
        .dates-info p {
            margin: 3px 0;
            font-size: 13px;
        }
        
        .dates-info strong {
            color: #444;
            font-weight: 600;
            display: inline-block;
            width: 85px;
        }
        
        .service-details {
            margin-bottom: 25px;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #eaeaea;
        }
        
        table th, table td {
            padding: 12px;
            text-align: left;
            font-size: 14px;
        }
        
        table th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 1px;
        }
        
        table tr:not(:last-child) td {
            border-bottom: 1px solid #eaeaea;
        }
        
        table tr:nth-child(even) {
            background-color: #f9fcfb;
        }
        
        table tr:hover td {
            background-color: #f2f9f8;
        }
        
        .total-section {
            margin-top: 20px;
            text-align: right;
            background: #f9fcfb;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #eaeaea;
        }
        
        .total {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            display: inline-block;
            padding: 6px 16px;
            background-color: #f2f9f8;
            border-radius: 50px;
            border: 1px solid #e0f0ee;
        }
        
        /* Paid Stamp Styles */
        .paid-stamp {
            position: relative;
            display: inline-block;
            margin-left: 20px;
            vertical-align: middle;
        }
        
        .paid-stamp-inner {
            position: relative;
            display: inline-block;
            padding: 6px 15px;
            font-size: 18px;
            font-weight: 700;
            color: white;
            background-color: var(--success-color);
            border-radius: 8px;
            transform: rotate(-15deg);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .paid-stamp:before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: repeating-linear-gradient(
                45deg,
                var(--success-color),
                var(--success-color) 10px,
                rgba(40, 167, 69, 0.8) 10px,
                rgba(40, 167, 69, 0.8) 20px
            );
            border-radius: 10px;
            opacity: 0.3;
            z-index: -1;
            transform: rotate(-5deg);
        }
        
        @media print {
            .paid-stamp-inner {
                background-color: #28a745 !important;
                color: white !important;
                box-shadow: none !important;
                border: 1px solid #28a745 !important;
            }
            
            .paid-stamp:before {
                background: none !important;
                border: 2px dashed #28a745 !important;
            }
        }
        
        .notes {
            margin-top: 25px;
            padding: 15px;
            background-color: #f9fcfb;
            border-radius: 10px;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #eaeaea;
        }
        
        .notes .section-title {
            margin-bottom: 10px;
        }
        
        .notes p {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
        }
        
        .thank-you {
            margin-top: 25px;
            text-align: center;
            color: var(--primary-color);
            font-size: 18px;
            font-weight: 600;
            padding: 12px;
            background: #f9fcfb;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #eaeaea;
        }
        
        .buttons {
            margin: 20px 0;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.3s ease;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn i {
            font-size: 16px;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background: #145a55;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .error-container {
            background-color: #fff3f5;
            color: #721c24;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            border: 1px solid #f8d7da;
            border-left: 5px solid #dc3545;
        }
        
        .error-container h3 {
            color: #dc3545;
            margin-bottom: 12px;
            font-size: 20px;
        }
        
        footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 1px solid #eaeaea;
            color: #777;
            font-size: 12px;
        }
        
        /* Print styles */
        @media print {
            html, body {
                background-color: #fff;
                padding: 0;
                margin: 0;
                color: #000;
                font-size: 12pt;
                width: 100%;
                height: 100%;
            }
            
            .container {
                max-width: 100%;
                width: 100%;
                padding: 0;
                margin: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
                min-height: 100%;
            }
            
            .invoice-container {
                box-shadow: none;
                border: none;
                padding: 0;
                margin: 0 auto;
                width: 100%;
                max-width: 210mm; /* A4 width */
                min-height: 250mm; /* A bit less than A4 height to ensure it fits */
                position: relative;
                page-break-after: avoid;
                page-break-inside: avoid;
                display: flex;
                flex-direction: column;
                justify-content: space-between;
            }
            
            .invoice-content {
                flex: 1;
                padding: 10mm 5mm;
            }
            
            .invoice-container::before {
                display: none;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block;
            }
            
            .bill-details, .total-section, .notes, .thank-you {
                background-color: #fff !important;
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            
            .dates-info {
                background-color: #fff !important;
                border: 1px solid #ddd !important;
            }
            
            table {
                width: 100% !important;
                page-break-inside: avoid;
            }
            
            table th {
                background-color: #eee !important;
                color: #000 !important;
                font-size: 10pt;
            }
            
            table td {
                font-size: 10pt;
            }
            
            .invoice-header {
                margin-bottom: 10mm;
                padding-bottom: 5mm;
                display: flex;
                justify-content: space-between;
                page-break-inside: avoid;
                border-bottom: 1px solid #ddd;
            }
            
            .company-info h1 {
                font-size: 16pt;
                margin-bottom: 2mm;
            }
            
            .company-info p {
                font-size: 8pt;
                margin: 1mm 0;
                line-height: 1.3;
            }
            
            .logo-container img {
                max-width: 40mm;
                border-radius: 0;
                padding: 0;
                background-color: transparent;
                box-shadow: none;
            }
            
            .invoice-title {
                margin: 7mm 0;
                page-break-after: avoid;
            }
            
            .invoice-title h2 {
                font-size: 18pt;
            }
            
            .section-title {
                font-size: 11pt;
                margin-bottom: 2mm;
            }
            
            .bill-details {
                margin-bottom: 7mm;
                padding: 3mm;
                display: flex;
                page-break-inside: avoid;
            }
            
            .customer-info p, .bill-info p {
                font-size: 9pt;
                margin: 1mm 0;
                line-height: 1.3;
            }
            
            .dates-info {
                margin-top: 2mm;
                padding: 2mm;
            }
            
            .dates-info p {
                font-size: 9pt;
                margin: 1mm 0;
            }
            
            .service-details {
                margin-bottom: 7mm;
                page-break-inside: avoid;
            }
            
            table th, table td {
                padding: 2mm;
            }
            
            .total-section {
                margin-top: 5mm;
                padding: 3mm;
                text-align: right;
                page-break-inside: avoid;
            }
            
            .total {
                font-size: 12pt;
                padding: 2mm 4mm;
            }
            
            .notes {
                margin-top: 7mm;
                padding: 3mm;
                page-break-inside: avoid;
            }
            
            .notes p {
                font-size: 9pt;
                line-height: 1.3;
            }
            
            .thank-you {
                font-size: 12pt;
                margin-top: 7mm;
                padding: 3mm;
                page-break-inside: avoid;
            }
            
            footer {
                margin-top: 10mm;
                padding-top: 3mm;
                text-align: center;
                border-top: 1px solid #eee;
            }
            
            footer p {
                font-size: 8pt;
                margin: 1mm 0;
            }
            
            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (!empty($error_message)): ?>
            <div class="error-container">
                <h3>Error</h3>
                <p><?php echo $error_message; ?></p>
                <a href="billing.php" class="btn btn-primary">Back to Billing</a>
            </div>
        <?php elseif ($bill): ?>
            <div class="buttons no-print">
                <a href="billing.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Billing</a>
                <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Invoice</button>
            </div>
            
            <div class="invoice-container" id="invoice">
                <div class="invoice-content">
                    <div class="invoice-header">
                        <div class="company-info">
                            <h1><?php echo $company_name; ?></h1>
                            <p><?php echo $company_address; ?></p>
                            <p>Phone: <?php echo $company_phone; ?></p>
                            <p>Email: <?php echo $company_email; ?></p>
                            <p>Website: <?php echo $company_website; ?></p>
                        </div>
                        <div class="logo-container">
                            <img src="../images/logo-5.PNG" alt="Adventure Travel Logo">
                        </div>
                    </div>
                    
                    <div class="invoice-title">
                        <h2>INVOICE</h2>
                    </div>
                    
                    <div class="bill-details">
                        <div class="bill-to">
                            <div class="section-title">BILL TO:</div>
                            <div class="customer-info">
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($bill['customer_name']); ?></p>
                                <p><strong>Email:</strong> <?php echo htmlspecialchars($bill['email']); ?></p>
                                <p><strong>Phone:</strong> <?php echo htmlspecialchars($bill['mobile']); ?></p>
                                <p><strong>Country:</strong> <?php echo htmlspecialchars($bill['country']); ?></p>
                            </div>
                        </div>
                        
                        <div class="bill-info">
                            <p><strong>Invoice no:</strong> <?php echo str_pad($bill['bill_id'], 5, '0', STR_PAD_LEFT); ?></p>
                            <p><strong>Date:</strong> <?php echo date('M d, Y', strtotime($bill['created_at'])); ?></p>
                            <div class="dates-info">
                                <p><strong>Arrival:</strong> <?php echo date('M d, Y', strtotime($bill['arrival_date'])); ?></p>
                                <p><strong>Departure:</strong> <?php echo date('M d, Y', strtotime($bill['departure_date'])); ?></p>
                                <p><strong>Duration:</strong> <?php echo $nights; ?> nights</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="service-details">
                        <div class="section-title">SERVICE DETAILS:</div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Duration</th>
                                    <th>Airport</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo htmlspecialchars($bill['service_name']); ?></td>
                                    <td><?php echo $nights; ?> nights</td>
                                    <td><?php echo htmlspecialchars($bill['airport_name']); ?></td>
                                    <td>$<?php echo number_format($bill['total_price'], 2); ?></td>
                                </tr>
                                <!-- Optional: Add more rows for additional services -->
                            </tbody>
                        </table>
                        
                        <div class="total-section">
                            <p class="total">Total Amount: $<?php echo number_format($bill['total_price'], 2); ?></p>
                            <div class="paid-stamp">
                                <div class="paid-stamp-inner">PAID</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="thank-you">
                        Thank you for choosing Adventure Travel!
                    </div>
                    
                    <footer class="print-only">
                        <p>This is a computer-generated invoice and doesn't require a signature.</p>
                        <p>Invoice generated on <?php echo date('Y-m-d H:i:s'); ?></p>
                    </footer>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 