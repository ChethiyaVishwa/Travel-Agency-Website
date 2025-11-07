<?php
    // Start output buffering to prevent "headers already sent" errors
    ob_start();
    
    // Database connection
    require_once 'admin/config.php';
    
    // Start the session if one doesn't exist already
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    $is_logged_in = isset($_SESSION['user_id']);
    $user_name = $is_logged_in ? $_SESSION['full_name'] : '';
    $username = $is_logged_in ? $_SESSION['username'] : '';

    // Handle logout
    if (isset($_GET['logout'])) {
        // Clear all session variables
        $_SESSION = array();
        
        // Destroy the session
        session_destroy();
        
        // Redirect to login page
        header("Location: login.php");
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Contact Us - Adventure Travel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add Google Fonts - Dancing Script for signature-style text -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <!-- Add Josefin Sans Google Font -->
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
            background-color: var(--bg-color);
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
            color: var(--primary-color);
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
            background: var(--header-bg);
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Josefin Sans', 'Arial', sans-serif;
        }

        :root {
            --primary-color: rgb(23, 108, 101);
            --secondary-color: linear-gradient(to right,rgb(0, 255, 204) 0%,rgb(206, 255, 249) 100%);
            --dark-color: #333;
            --light-color: #f4f4f4;
            --text-color: #333;
            --bg-color: #f0f2f5;
            --card-bg: #fff;
            --header-bg: rgb(0, 255, 204);
            --footer-bg: #333;
            --footer-text: #fff;
            --card-shadow: rgba(0, 0, 0, 0.1);
            --border-alt-color: rgb(23, 108, 101);
        }

        .dark-mode {
            --primary-color: rgb(20, 170, 145);
            --secondary-color: linear-gradient(to right,rgb(0, 205, 164) 0%,rgb(0, 9, 8) 100%);
            --dark-color: #f0f0f0;
            --light-color: #222;
            --text-color:rgb(255, 255, 255);
            --bg-color: #121212;
            --card-bg: #2d2d2d;
            --header-bg: rgb(0, 205, 164);
            --footer-bg: #1a1a1a;
            --footer-text: #ddd;
            --card-shadow: rgba(0, 0, 0, 0.5);
            --border-alt-color: rgb(0, 179, 143);
        }
        
        /* Ensure page loader adapts to dark mode */
        .dark-mode .page-loader {
            background-color: #121212;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.5s ease, color 0.5s ease;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: var(--header-bg);
            box-shadow: 0 2px 10px rgb(0, 0, 0);
            padding: 10px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 1000;
            transition: transform 0.3s ease, background 0.5s ease, border-color 0.5s ease;
            border-radius: 0 0 30px 30px;
        }

        .header.hide {
            transform: translateY(-100%);
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            margin-right: 10px;
        }

        .navbar a {
            font-size: 16px;
            color: var(--text-color);
            text-decoration: none;
            margin-left: 25px;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .navbar a:hover {
            color: rgb(255, 0, 0);
        }

        /* Contact Page Specific Styles */
        .contact-section {
            padding: 120px 0 80px;
            background-color: var(--bg-alt-color);
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .contact-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .contact-header h1 {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .contact-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            color: var(--text-color);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            margin-top: 20px;
            padding: 10px 20px;
            background:rgb(23, 108, 101);
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .back-btn i {
            margin-right: 10px;
        }
        
        .back-btn:hover {
            background: rgb(18, 88, 82);
            color: rgb(255, 255, 255);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        .contact-content {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 50px;
        }

        .contact-info {
            flex: 1;
            min-width: 300px;
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgb(0, 0, 0);
            padding: 30px;
        }

        .contact-info h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 20px;
            position: relative;
        }

        .info-item {
            display: flex;
            margin-bottom: 20px;
            align-items: flex-start;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;
            background: var(--secondary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: var(--text-color);
            font-size: 1.2rem;
        }

        .info-details h4 {
            margin: 0 0 5px;
            color: var(--primary-color);
            font-size: 1.1rem;
        }

        .info-details p {
            margin: 0;
            color: var(--text-color);
            line-height: 1.5;
        }

        .contact-form {
            flex: 1;
            min-width: 300px;
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgb(0, 0, 0);
            padding: 30px;
        }

        .contact-form h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 20px;
            position: relative;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background-color: var(--bg-color);
            color: var(--text-color);
            font-size: 16px; /* Fixed 16px font size to prevent zoom */
            transition: all 0.3s ease;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            touch-action: manipulation; /* Prevents browser manipulation */
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(23, 108, 101, 0.2);
            outline: none;
        }

        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }

        .btn-submit {
            background:rgb(23, 108, 101);
            color: white;
            border: none;
            border-radius: 30px;
            padding: 12px 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-submit:hover {
            background: rgb(18, 88, 82);
            color: rgb(255, 255, 255);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            transform: translateY(-3px);
        }

        .social-links {
            text-align: center;
            margin-top: 50px;
        }

        .social-links h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 30px;
            position: relative;
            display: inline-block;
        }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .social-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--card-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
            box-shadow: 0 5px 15px var(--card-shadow);
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-icon:hover {
            transform: translateY(-5px);
            color: white;
        }

        .whatsapp:hover {
            background-color: #25D366;
        }

        .facebook:hover {
            background-color: #3b5998;
        }

        .instagram:hover {
            background-color: #E1306C;
        }

        .email:hover {
            background-color: #D44638;
        }

        .map-container {
            margin-top: 50px;
            padding: 20px;
            background-color: var(--card-bg);
            border-radius: 15px;
            box-shadow: 0 5px 20px var(--card-shadow);
        }

        .map-container h3 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .map-wrapper {
            width: 100%;
            height: 400px;
            border-radius: 10px;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Theme toggle button - Stylish switch design */
        .theme-toggle {
            position: fixed;
            left: 20px;
            top: 180px; /* Positioned further down */
            z-index: 999;
            width: 60px;
            height: 30px;
            border-radius: 15px;
            background: linear-gradient(to right, #2c3e50, #4ca1af);
            border: none;
            cursor: pointer;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            padding: 0;
            overflow: hidden;
            display: flex;
            align-items: center;
        }

        .theme-toggle:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.4);
        }

        .dark-mode .theme-toggle {
            background: linear-gradient(to right, #4ca1af, #2c3e50);
        }

        .toggle-handle {
            position: absolute;
            left: 5px;
            width: 20px;
            height: 20px;
            background-color: #fff;
            border-radius: 50%;
            transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transform: translateX(0);
        }
        
        .dark-mode .toggle-handle {
            transform: translateX(30px);
            background-color: #222;
        }

        .toggle-icons {
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 7px;
            box-sizing: border-box;
            pointer-events: none;
        }

        .toggle-icons i {
            font-size: 12px;
            color: #fff;
            z-index: 1;
        }

        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 24px;
            color: var(--text-color);
            transition: color 0.3s ease;
        }

        /* Footer Styles */
        footer {
            background-color: var(--footer-bg);
            color: var(--footer-text);
            padding: 40px 0;
            margin-top: 80px;
            transition: background-color 0.5s ease, color 0.5s ease;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-section {
            flex: 1;
            min-width: 300px;
            margin-bottom: 20px;
            text-align: center;
            padding: 0 15px;
            background-color: transparent;
        }

        .footer-section.about-section,
        .footer-section.links-section,
        .footer-section.contact-section {
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: transparent;
        }

        .footer-section h3 {
            color: var(--secondary-color);
            margin-bottom: 20px;
            font-size: 1.2rem;
            display: inline-block;
            position: relative;
            font-weight: 600;
        }

        .footer-section p, .footer-section ul {
            color: #bbb;
            max-width: 80%;
            margin: 0 auto;
        }

        .footer-section ul {
            list-style: none;
            padding-left: 0;
        }

        .footer-section ul li {
            margin-bottom: 10px;
        }

        .footer-section ul li a {
            color: #bbb;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-section ul li a:hover {
            color: var(--secondary-color);
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            margin-top: 20px;
            text-align: center;
            color: var(--footer-text);
            transition: border-color 0.5s ease, color 0.5s ease;
        }

        /* Responsive Styles */
                @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
            
            .navbar {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--card-bg);
                border-top: 1px solid var(--card-shadow);
                padding: 0;
                clip-path: polygon(0 0, 100% 0, 100% 0, 0 0);
                transition: 0.5s ease;
                display: block; /* Added for proper mobile display */
            }
            
            .navbar.active {
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
                background-color: var(--bg-color);
                padding-bottom: 15px;
                z-index: 1000;
                overflow-y: auto;
                max-height: 85vh;
            }
            
            .navbar a {
                display: block;
                margin: 15px 0;
                padding: 15px 30px;
                font-size: 20px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .contact-content {
                flex-direction: column;
            }
            
            .contact-info, .contact-form {
                width: 100%;
                margin-bottom: 30px;
            }
            
            /* Improved contact info responsive styles */
            .contact-info {
                padding: 25px 20px;
            }
            
            .info-item {
                margin-bottom: 25px;
            }
            
            .info-icon {
                width: 45px;
                height: 45px;
                min-width: 45px;
            }
            
            .info-details h4 {
                font-size: 1.2rem;
                margin-bottom: 8px;
            }
            
            .info-details p {
                font-size: 0.95rem;
            }
            
            /* Form responsive styles for tablets */
            .contact-form {
                padding: 25px 20px;
            }
            
            .contact-form h3 {
                margin-bottom: 25px;
            }
            
            .form-group {
                margin-bottom: 20px;
            }
            
            .form-control {
                padding: 12px 15px;
            }
            
            textarea.form-control {
                min-height: 120px;
            }
            
            .note-text {
                font-size: 0.9rem;
                margin-bottom: 18px;
            }
            
            .btn-submit {
                padding: 12px 30px;
            }
            
            .social-icons {
                gap: 15px;
            }
            
            .footer-content {
                flex-direction: column;
                text-align: center;
            }
            
            .footer-section {
                margin-bottom: 40px;
                min-width: 100%;
                background-color: transparent;
            }

            .footer-section:nth-child(2):before,
            .footer-section:nth-child(3):before {
                content: '';
                width: 80px;
                height: 1px;
                background-color: rgba(255, 255, 255, 0.1);
                position: absolute;
                top: -20px;
                left: 50%;
                transform: translateX(-50%);
            }

            .footer-section:last-child {
                margin-bottom: 20px;
            }
            
            .footer-section h3 {
                color: var(--secondary-color);
                margin-bottom: 15px;
                display: block;
                text-align: center;
            }

            .footer-section p, .footer-section ul {
                max-width: 90%;
            }
        }

        @media (max-width: 576px) {
            .theme-toggle {
                top: 130px;
                left: 10px;
                width: 46px;
                height: 24px;
                border-radius: 12px;
            }
            
            .toggle-handle {
                width: 16px;
                height: 16px;
                left: 4px;
            }
            
            .dark-mode .toggle-handle {
                transform: translateX(22px);
            }
            
            .toggle-icons {
                padding: 0 5px;
            }
            
            .toggle-icons i {
                font-size: 9px;
            }
            
            /* Improved mobile menu styles */
            .navbar a {
                padding: 10px 20px;
                font-size: 17px;
                margin: 10px 0;
            }
            
            /* Make menu items more compact for mobile */
            .navbar {
                max-height: 80vh;
                overflow-y: auto;
            }
            
            .contact-header h1 {
                font-size: 2rem;
            }
            
            .contact-header p {
                font-size: 1rem;
            }
            
            /* Smaller device contact info styles */
            .contact-info {
                padding: 20px 15px;
            }
            
            .info-item {
                margin-bottom: 20px;
            }
            
            .info-icon {
                width: 38px;
                height: 38px;
                min-width: 38px;
                margin-right: 12px;
            }
            
            .info-details h4 {
                font-size: 1.05rem;
                margin-bottom: 5px;
            }
            
            .info-details p {
                font-size: 0.9rem;
                line-height: 1.4;
            }
            
            /* Form responsive styles for mobile */
            .contact-form {
                padding: 20px 15px;
            }
            
            .contact-form h3 {
                font-size: 1.3rem;
                margin-bottom: 20px;
            }
            
            .form-group {
                margin-bottom: 15px;
            }
            
            .form-control {
                padding: 10px 12px;
                border-radius: 6px;
            }
            
            textarea.form-control {
                min-height: 100px;
            }
            
            .note-text {
                font-size: 0.85rem;
                margin-bottom: 15px;
            }
            
            .btn-submit {
                width: 100%;
                padding: 12px 0;
                font-size: 0.95rem;
            }
            
            /* Prevent zoom on mobile form inputs */
            .form-control {
                font-size: 16px !important; /* iOS won't zoom if font size is at least 16px */
                transform: scale(1); /* Helps prevent zoom on some Android devices */
                transform-origin: left top;
                touch-action: manipulation; /* Prevents browser manipulation */
            }
            
            .social-icons {
                gap: 12px;
            }
            
            .social-icon {
                width: 40px;
                height: 40px;
                font-size: 1.1rem;
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
    
    <!-- Header Section -->
    <header class="header">
        <a href="index.php" class="logo">
            <img src="images/logo-5.PNG" alt="Adventure Travel Logo">
        </a>
        
        <div class="menu-toggle">☰</div>

        <nav class="navbar">
            <a href="index.php">Home</a>
            <a href="tour_packages/tour_packages.php">Tour Packages</a>
            <a href="one_day_tour_packages/one_day_tour.php">One Day Tours</a>
            <a href="special_tour_packages/special_tour.php">Special Tours</a>
            <a href="index.php#vehicle-hire">Vehicle Hire</a>
            <a href="destinations/destinations.php">Destinations</a>
            <a href="contact_us.php">Contact Us</a>
            <a href="about_us/about_us.php">About Us</a>
        </nav>
    </header>

    <!-- Theme toggle button -->
    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle dark mode">
        <div class="toggle-icons">
            <i class="fas fa-sun"></i>
            <i class="fas fa-moon"></i>
        </div>
        <div class="toggle-handle"></div>
    </button>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="contact-container">
            <div class="contact-header">
                <h1><em style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-size: 1.1em; font-weight: bold;">Get In</em> <span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: bold;">Touch</span></h1>
                <p>Have a question or need assistance with your travel plans? We're here to help you plan your perfect adventure.</p>
                <a href="javascript:history.back()" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Previous Page</a>
            </div>

            <div class="contact-content">
                <div class="contact-info">
                    <h3><span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: 900; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">Contact Information</span></h3>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-details">
                            <h4>Our Location</h4>
                            <p>Narammala, Kurunegala, Sri Lanka</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="info-details">
                            <h4>Phone Number</h4>
                            <p>+94 71 538 0080</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-details">
                            <h4>Email Address</h4>
                            <p>adventuretravelsrilanka@gmail.com</p>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-details">
                            <h4>Working Hours</h4>
                            <p>Monday - Friday: 9am - 6pm</p>
                            <p>Saturday: 9am - 2pm</p>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <h3><span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: 900; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">Send us a Message</span></h3>
                    <?php
                    // Process form submission
                    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact_submit'])) {
                        $name = mysqli_real_escape_string($conn, $_POST['name']);
                        $email = mysqli_real_escape_string($conn, $_POST['email']);
                        $subject = mysqli_real_escape_string($conn, $_POST['subject']);
                        $message = mysqli_real_escape_string($conn, $_POST['message']);
                        $whatsapp = mysqli_real_escape_string($conn, $_POST['whatsapp']);
                        
                        // Insert into database
                        $insert_query = "INSERT INTO contact_messages (name, email, subject, message, whatsapp_number, ip_address, submitted_at) 
                                        VALUES ('$name', '$email', '$subject', '$message', '$whatsapp', '{$_SERVER['REMOTE_ADDR']}', NOW())";
                        
                        if (mysqli_query($conn, $insert_query)) {
                            // Store success message in session and redirect
                            $_SESSION['contact_message'] = 'success';
                            header("Location: contact_us.php");
                            exit;
                        } else {
                            // Store error message in session and redirect
                            $_SESSION['contact_message'] = 'error';
                            header("Location: contact_us.php");
                            exit;
                        }
                    }
                    
                    // Display messages from session if they exist
                    if (isset($_SESSION['contact_message'])) {
                        if ($_SESSION['contact_message'] == 'success') {
                            echo '<div class="alert alert-success mb-3">Your message has been sent! We\'ll get back to you soon.</div>';
                        } else {
                            echo '<div class="alert alert-danger mb-3">Sorry, there was an error sending your message. Please try again later.</div>';
                        }
                        // Clear the message from session to prevent showing it again on future page loads
                        unset($_SESSION['contact_message']);
                    }
                    
                    /*
                    -- SQL Query to create the contact_messages table:
                    
                    CREATE TABLE `contact_messages` (
                      `message_id` int(11) NOT NULL AUTO_INCREMENT,
                      `name` varchar(100) NOT NULL,
                      `email` varchar(100) NOT NULL,
                      `subject` varchar(255) DEFAULT NULL,
                      `message` text NOT NULL,
                      `whatsapp_number` varchar(20) DEFAULT NULL,
                      `ip_address` varchar(45) DEFAULT NULL,
                      `is_read` tinyint(1) NOT NULL DEFAULT 0,
                      `is_responded` tinyint(1) NOT NULL DEFAULT 0,
                      `submitted_at` datetime NOT NULL,
                      `read_at` datetime DEFAULT NULL,
                      `responded_at` datetime DEFAULT NULL,
                      PRIMARY KEY (`message_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
                    */
                    ?>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="whatsapp" class="form-control" placeholder="Your WhatsApp Number">
                        </div>
                        <div class="form-group">
                            <input type="text" name="subject" class="form-control" placeholder="Subject">
                        </div>
                        <div class="form-group">
                            <textarea name="message" class="form-control" placeholder="Your Message" required></textarea>
                        </div>
                        <div class="note-container" style="background-color: rgba(var(--primary-color-rgb, 23, 108, 101), 0.1); border-left: 3px solid var(--primary-color); padding: 10px 15px; border-radius: 5px; margin-bottom: 20px;">
                            <p class="note-text" style="margin: 0; color: var(--primary-color); font-weight: 500; display: flex; align-items: center;">
                                <i class="fas fa-info-circle" style="margin-right: 8px; font-size: 1.1em;"></i> 
                                NOTE: We will send the answer to your question to your email address. Please check it out.
                            </p>
                        </div>
                        <button type="submit" name="contact_submit" class="btn-submit">Send Message</button>
                    </form>
                </div>
            </div>

            <div class="social-links">
                <h3><span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: 900; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">Connect With Us</span></h3>
                <p>Follow us on social media for the latest travel updates, offers and adventure inspiration.</p>
                <div class="social-icons">
                    <a href="https://wa.me/+94715380080" class="social-icon whatsapp" target="_blank">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://www.facebook.com/share/1FpJmkvUn8/?mibextid=wwXIfr" class="social-icon facebook" target="_blank">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/adventuretravelsrilanka?igsh=dncyeXJyYjRqNDRq&utm_source=qr" class="social-icon instagram" target="_blank">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="mailto:adventuretravelsrilanka@gmail.com" class="social-icon email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
            </div>

            <div class="map-container">
                <h3><span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: 900; text-shadow: 1px 1px 3px rgba(0,0,0,0.3);">Find Us On The Map</span></h3>
                <div class="map-wrapper">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126755.15232321359!2d80.3064801716797!3d7.484586899999992!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae33598ed3c1049%3A0xf1c8efa3f5e3f603!2sNarammala!5e0!3m2!1sen!2slk!4v1655555555555!5m2!1sen!2slk" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section about-section">
                    <h3>About Us</h3>
                    <p>Adventure Travel is a premier travel agency specializing in adventure tours and memorable experiences across Sri Lanka.</p>
                </div>
                <div class="footer-section links-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="tour_packages/tour_packages.php">Tour Packages</a></li>
                        <li><a href="one_day_tour_packages/one_day_tour.php">One Day Tours</a></li>
                        <li><a href="special_tour_packages/special_tour.php">Special Tours</a></li>
                    </ul>
                </div>
                <div class="footer-section contact-section">
                    <h3>Contact Us</h3>
                    <ul>
                        <li>Email: adventuretravelsrilanka@gmail.com</li>
                        <li>Phone: +94 71 538 0080</li>
                        <li>Address: Narammala, Kurunegala, Sri Lanka</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Adventure Travel. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Page loader
            setTimeout(function() {
                $('.page-loader').addClass('fade-out');
                setTimeout(function() {
                    $('.page-loader').hide();
                }, 500);
            }, 1000);
            
            // User dropdown functionality
            $('.profile-btn').on('click', function(e) {
                e.stopPropagation();
                $('.user-dropdown').toggleClass('active');
            });
            
            // Close dropdown when clicking elsewhere
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.user-dropdown').length) {
                    $('.user-dropdown').removeClass('active');
                }
            });
            
            // Menu toggle functionality
            $('.menu-toggle').on('click', function() {
                $('.navbar').toggleClass('active');
            });
            
            // Close navigation when a nav link is clicked
            $('.navbar a').on('click', function() {
                $('.navbar').removeClass('active');
            });
            
            // Header show/hide on scroll
            let lastScrollTop = 0;
            $(window).scroll(function() {
                let scrollTop = $(this).scrollTop();
                if (scrollTop > lastScrollTop && scrollTop > 100) {
                    $('.header').addClass('hide');
                } else {
                    $('.header').removeClass('hide');
                }
                lastScrollTop = scrollTop;
            });
            
            // Theme toggle functionality
            $('#theme-toggle').on('click', function() {
                $('body').toggleClass('dark-mode');
                
                // Save preference to localStorage
                if ($('body').hasClass('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }
            });
            
            // Check for saved theme preference or use device preference
            const savedTheme = localStorage.getItem('theme');
            const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
            
            if (savedTheme === 'dark' || (!savedTheme && prefersDarkScheme.matches)) {
                $('body').addClass('dark-mode');
            }
        });
    </script>
</body>
</html>
<?php
// Flush the output buffer and send content to browser
ob_end_flush();
?>
