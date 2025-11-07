<?php
    // Database connection
    require_once 'admin/config.php';
    
    // Fetch vehicles from database
    $vehicles_query = "SELECT * FROM vehicles WHERE available = 1 ORDER BY vehicle_id DESC LIMIT 6";
    $vehicles_result = mysqli_query($conn, $vehicles_query);
    $vehicles = [];
    if ($vehicles_result) {
        while ($vehicle = mysqli_fetch_assoc($vehicles_result)) {
            $vehicles[] = $vehicle;
        }
    }
    
    // Check if team_members table exists, create if not
    $check_table = "SHOW TABLES LIKE 'team_members'";
    $table_exists = mysqli_query($conn, $check_table);
    
    if (mysqli_num_rows($table_exists) == 0) {
        $create_table = "CREATE TABLE team_members (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            position VARCHAR(255) NOT NULL,
            bio TEXT NOT NULL DEFAULT 'Experienced travel professional with a passion for creating unforgettable adventures. Expert in Sri Lanka tourism and committed to exceptional customer service.',
            image VARCHAR(255) NOT NULL,
            facebook VARCHAR(255),
            twitter VARCHAR(255),
            instagram VARCHAR(255),
            linkedin VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        
        mysqli_query($conn, $create_table);
    }
    
    // Fetch team members from database
    $team_members_query = "SELECT * FROM team_members ORDER BY id ASC";
    $team_members_result = mysqli_query($conn, $team_members_query);
    $team_members = [];
    if ($team_members_result) {
        while ($member = mysqli_fetch_assoc($team_members_result)) {
            $team_members[] = $member;
        }
    }
    
    // Fetch approved reviews from database
    $reviews_query = "SELECT * FROM reviews WHERE status = 'approved' ORDER BY created_at DESC LIMIT 8";
    $reviews_result = mysqli_query($conn, $reviews_query);
    $approved_reviews = [];
    if ($reviews_result) {
        while ($review = mysqli_fetch_assoc($reviews_result)) {
            $approved_reviews[] = $review;
        }
    }

    // Start the session if one doesn't exist already
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    $is_logged_in = isset($_SESSION['user_id']);
    $user_name = $is_logged_in ? $_SESSION['full_name'] : '';
    $username = $is_logged_in ? $_SESSION['username'] : '';

    // We'll mark messages as read only when the user opens the chat box, not on page load

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
<html lang="en" style="scroll-behavior: smooth; height: 100%;">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" href="images/domain-img.png" type="image/x-icon">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://adventuretravelsrilanka.lk/">
    <meta property="og:title" content="Adventure Travel Sri Lanka">
    <meta property="og:description" content="Discover Sri Lanka with our premium travel packages, guided tours, and vehicle hire services.">
    <meta property="og:image" content="images/logo-5.PNG">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    
    <!-- Mobile Web App -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#176C65">
    
    <title>Adventure Travel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Add Google Fonts - Dancing Script for signature-style text -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap">
    <!-- Add Josefin Sans Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Josefin+Sans:ital,wght@0,100..700;1,100..700&display=swap">
    <!-- Add Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
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
            --bg-color:rgb(255, 255, 255);
            --bg-alt-color: #ffffff;
            --card-bg: #fff;
            --card-bg-rgb: 255, 255, 255;
            --header-bg: rgb(0, 255, 204);
            --footer-bg: #333;
            --footer-text: #fff;
            --card-shadow: rgba(0, 0, 0, 0.1);
            --border-alt-color: rgb(23, 108, 101);
            --section-filter: linear-gradient(rgba(255, 255, 255, 0.45), rgba(255, 255, 255, 0.45));
        }

        .dark-mode {
            --primary-color: rgb(20, 170, 145);
            --secondary-color: linear-gradient(to right,rgb(0, 205, 164) 0%,rgb(0, 9, 8) 100%);
            --dark-color: #f0f0f0;
            --light-color: #222;
            --text-color:rgb(255, 255, 255);
            --bg-color:rgb(0, 0, 0);
            --bg-alt-color: #1a1a1a;
            --card-bg: #2d2d2d;
            --card-bg-rgb: 45, 45, 45;
            --header-bg: rgb(0, 205, 164);
            --footer-bg: #1a1a1a;
            --footer-text: #ddd;
            --card-shadow: rgba(0, 0, 0, 0.5);
            --border-alt-color: rgb(0, 179, 143);
            --section-filter: linear-gradient(rgba(0, 0, 0, 0.70), rgba(0, 0, 0, 0.70));
        }

        html {
            scroll-behavior: smooth;
            height: 100%;
            -webkit-overflow-scrolling: touch; /* For smooth scrolling on iOS */
        }
        
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.5s ease, color 0.5s ease;
            overflow-y: scroll; /* Always show scrollbar to prevent layout shifts */
            overscroll-behavior-y: contain; /* Prevents pull-to-refresh and bounce effects */
            min-height: 110%; /* Set minimum height to 100% */
            height: 110%; /* Important for mobile */
            -webkit-font-smoothing: antialiased; /* Improved font rendering */
            -moz-osx-font-smoothing: grayscale; /* Improved font rendering in Firefox */
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

        /* Sri Lanka Clock Styles - Claymorphism Design */
        .sri-lanka-clock {
            display: flex;
            align-items: center;
            background-color: #2A2438;
            border-radius: 18px;
            padding: 8px 16px;
            margin-right: 16px;
            position: relative;
            transition: all 0.4s ease;
            box-shadow: 
                5px 5px 10px rgba(0, 0, 0, 0.4),
                -5px -5px 10px rgba(82, 67, 110, 0.25),
                inset -1px -1px 3px rgba(255, 255, 255, 0.05),
                inset 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        /* Depth effect on hover */
        .sri-lanka-clock:hover {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 
                7px 7px 14px rgba(0, 0, 0, 0.5),
                -7px -7px 14px rgba(82, 67, 110, 0.3),
                inset -1px -1px 3px rgba(255, 255, 255, 0.05),
                inset 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        /* Accent color ring around the container */
        .sri-lanka-clock::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(
                135deg, 
                rgba(255, 107, 107, 0.6), 
                rgba(255, 107, 107, 0)
            );
            border-radius: 20px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        
        .sri-lanka-clock:hover::before {
            opacity: 1;
        }
        
        /* Clay-style flag container */
        .sri-lanka-clock .sl-flag {
            height: 30px;
            width: auto;
            margin-right: 12px;
            border-radius: 10px;
            padding: 3px;
            position: relative;
            z-index: 2;
            box-shadow: 
                3px 3px 6px rgba(0, 0, 0, 0.3),
                -3px -3px 6px rgba(82, 67, 110, 0.2);
            transition: all 0.4s ease;
            background: linear-gradient(145deg, #352F44, #2A2438);
        }
        
                .sri-lanka-clock:hover .sl-flag {
            box-shadow: 
                4px 4px 8px rgba(0, 0, 0, 0.4),
                -4px -4px 8px rgba(82, 67, 110, 0.25);
        }

        /* Time display with soft appearance */
        #sl-time {
            font-weight: 600;
            color: #E9E8E8;
            font-size: 16px;
            position: relative;
            z-index: 2;
            font-family: 'Inter', 'Segoe UI', sans-serif;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .sri-lanka-clock:hover #sl-time {
            color: #FAF0E6;
            transform: translateY(-1px);
        }
        
        /* Time periods styling */
        #sl-time span {
            color: #B9B4C7;
            transition: all 0.3s ease;
        }
        
        .sri-lanka-clock:hover #sl-time span {
            color: #E9E8E8;
        }
        
        /* Dark mode styles - dark clay look */
        .dark-mode .sri-lanka-clock {
            background-color: #2A2438;
            box-shadow: 
                5px 5px 10px rgba(0, 0, 0, 0.4),
                -5px -5px 10px rgba(82, 67, 110, 0.25),
                inset -1px -1px 3px rgba(255, 255, 255, 0.05),
                inset 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .dark-mode .sri-lanka-clock:hover {
            box-shadow: 
                7px 7px 14px rgba(0, 0, 0, 0.5),
                -7px -7px 14px rgba(82, 67, 110, 0.3),
                inset -1px -1px 3px rgba(255, 255, 255, 0.05),
                inset 1px 1px 3px rgba(0, 0, 0, 0.3);
        }
        
        .dark-mode .sri-lanka-clock::before {
            background: linear-gradient(
                135deg, 
                rgba(255, 107, 107, 0.6), 
                rgba(255, 107, 107, 0)
            );
        }
        
        .dark-mode .sri-lanka-clock .sl-flag {
            box-shadow: 
                3px 3px 6px rgba(0, 0, 0, 0.3),
                -3px -3px 6px rgba(82, 67, 110, 0.2);
            background: linear-gradient(145deg, #352F44, #2A2438);
        }
        
        .dark-mode #sl-time {
            color: #E9E8E8;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.3);
        }
        
        .dark-mode #sl-time span {
            color: #B9B4C7;
        }
        
        .dark-mode .sri-lanka-clock:hover #sl-time {
            color: #FAF0E6;
        }
        
        .dark-mode .sri-lanka-clock:hover #sl-time span {
            color: #E9E8E8;
        }
        
        /* Responsive styles */
        @media (max-width: 768px) {
            .sri-lanka-clock {
                padding: 6px 12px;
                border-radius: 10px;
            }
            
            .sri-lanka-clock::before {
                border-radius: 12px;
            }
            
            .sri-lanka-clock .sl-flag {
                height: 24px;
                margin-right: 10px;
                border-radius: 5px;
                padding: 2px;
            }
            
            #sl-time {
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .sri-lanka-clock {
                padding: 4px 8px;
                margin-right: 10px;
            }
            
            .sl-flag {
                height: 20px;
            }
            
            #sl-time {
                font-size: 14px;
            }
        }

        .navbar a {
            font-size: 16px;
            color: var(--text-color);
            text-decoration: none;
            margin-left: 25px;
            font-weight: 700; /* or use 'bold' */
            transition: color 0.3s ease;
        }


        .navbar a:hover {
            color:rgb(255, 0, 0);
        }
        
        /* Glass-morphism User Dropdown Menu Style */
        .user-dropdown {
            position: relative;
            display: inline-block;
            margin-left: 25px;
        }
        
        .profile-btn {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-weight: bold;
            font-size: 18px;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .profile-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 20px rgba(0, 123, 255, 0.4);
        }
        
        .profile-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            width: 280px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 12px;
            opacity: 0;
            visibility: hidden;
            transform: scale(0.95);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            z-index: 1000;
            overflow: hidden;
        }
        
        .user-dropdown.active .profile-dropdown {
            opacity: 1;
            visibility: visible;
            transform: scale(1);
        }
        
        .profile-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
            background: linear-gradient(to right,rgb(255, 0, 0) 0%,rgb(60, 0, 0) 100%);
        }
        
        .profile-avatar {
            width: 70px;
            height: 70px;
            margin: 0 auto 12px;
            border-radius: 18px;
            background: linear-gradient(135deg,rgb(15, 107, 84) 0%,rgb(0, 234, 191) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            font-weight: bold;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .profile-header h4 {
            margin: 0;
            color: #fff;
            font-size: 18px;
            font-weight: 600;
        }
        
        .profile-header p {
            margin: 5px 0 0;
            color: #999;
            font-size: 14px;
            font-weight: 400;
        }
        
        .menu-section {
            padding: 15px;
        }
        
        .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 8px;
            color: #333;
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s ease;
            font-weight: 500;
        }
        
        .menu-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: translateX(5px);
        }
        
        .menu-item .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 10px;
            margin-right: 12px;
            font-size: 18px;
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .menu-item:nth-child(1) .icon {
            background-color: rgba(52, 152, 219, 0.1);
            color: #3498db;
        }
        
        .menu-item:nth-child(2) .icon {
            background-color: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
        }
        
        .menu-item:nth-child(3) .icon {
            background-color: rgba(46, 204, 113, 0.1);
            color: #2ecc71;
        }
        
        .menu-item.logout {
            color: #e74c3c;
        }
        
        .menu-item.logout .icon {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
        
        .menu-item.logout:hover {
            background-color: rgba(231, 76, 60, 0.05);
        }
        
        .account-actions {
            display: flex;
            padding: 0 15px 15px;
        }
        
        .account-actions a {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
            margin: 0 5px;
            border-radius: 10px;
            background: rgba(0, 0, 0, 0.03);
            color: #666;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        
        .account-actions a:hover {
            background: rgba(0, 0, 0, 0.06);
            color: #333;
            transform: translateY(-3px);
        }
        
        /* Dark mode adjustments */
        .dark-mode .profile-dropdown {
            background: rgba(30, 30, 30, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .dark-mode .profile-header {
            background: linear-gradient(to right,rgb(255, 0, 0) 0%,rgb(60, 0, 0) 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .dark-mode .profile-header h4 {
            color: #fff;
        }
        
        .dark-mode .profile-header p {
            color: #aaa;
        }
        
        .dark-mode .menu-item {
            color: #ccc;
        }
        
        .dark-mode .menu-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .dark-mode .account-actions a {
            background: rgba(255, 255, 255, 0.05);
            color: #aaa;
        }
        
        .dark-mode .account-actions a:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        /* Video Section Styles */
        .video-section {
            padding: 6rem 2rem;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .video-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .featured-video {
            display: flex;
            flex-direction: column;
            margin-bottom: 3rem;
            background: var(--secondary-color);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgb(0, 0, 0);
        }

        .video-wrapper {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
            overflow: hidden;
        }

        .video-wrapper iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .video-info {
            padding: 1.5rem;
        }

        .video-info h3 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--text-color);
        }

        .video-info p {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-color);
        }

        .video-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .video-item {
            perspective: 1000px;
            height: 200px;
            background: #000;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgb(0, 0, 0);
            position: relative;
            cursor: pointer;
        }

        .video-item-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            transform-style: preserve-3d;
        }

        .video-item:hover .video-item-inner {
            transform: rotateY(180deg);
        }

        .video-front, .video-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgb(0, 0, 0);
        }

        .video-front {
            background-color: #000;
            color: var(--text-color);
            z-index: 2;
            backface-visibility: hidden;
        }

        .video-thumbnail {
            position: relative;
            height: 100%;
            width: 100%;
            overflow: hidden;
        }

        .video-thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .video-front-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 10px;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.5) 20%, rgba(0, 0, 0, 0.8));
            text-align: center;
            z-index: 3;
            box-shadow: 0 -8px 16px rgba(0, 0, 0, 0.3);
        }

        .video-front-content h4 {
            font-size: 1.1rem;
            margin: 0 0 6px 0;
            color: #fff;
            text-align: center;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            background: linear-gradient(90deg, rgba(255,255,255,0.8), rgba(255,255,255,1), rgba(255,255,255,0.8));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: shine 2s infinite;
            padding: 4px 8px;
            border-radius: 4px;
            backdrop-filter: blur(2px);
            position: relative;
            z-index: 5;
        }
        
        @keyframes shine {
            0% {
                background-position: -100% center;
            }
            100% {
                background-position: 200% center;
            }
        }

        .video-flip-hint {
            font-size: 0.75rem;
            color: #fff;
            position: relative;
            padding: 4px 10px;
            border-radius: 15px;
            background: rgb(23, 108, 101);
            animation: pulse 2s infinite;
            display: inline-block;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);
            margin-top: 3px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .video-back {
            background: linear-gradient(145deg, rgba(23, 108, 101, 0.9), rgba(101, 255, 193, 0.9));
            color: white;
            transform: rotateY(180deg);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 10px;
            z-index: 1;
            backface-visibility: hidden;
        }
        
        .video-back h4 {
            color: white;
            margin-bottom: 10px;
            font-size: 0.95rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
            font-weight: bold;
        }

        .play-button {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            transition: all 0.3s ease;
            border: 2px solid white;
        }

        .video-back:hover .play-button {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        /* Video Modal Styles */
        .video-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.95);
            overflow: auto;
        }

        .modal-content {
            position: relative;
            margin: 5% auto;
            width: 90%;
            max-width: 900px;
        }

        .close-video {
            position: absolute;
            top: -40px;
            right: 0;
            color: #fff;
            font-size: 30px;
            font-weight: bold;
            cursor: pointer;
            z-index: 1001;
        }

        .modal-video-container {
            position: relative;
            width: 100%;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
        }

        .modal-video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Responsive Styles for Video Section */
        @media (min-width: 992px) {
            .featured-video {
                flex-direction: row;
                align-items: stretch;
            }

            .video-wrapper {
                width: 60%;
                padding-top: 33.75%; /* 16:9 aspect of 60% width */
            }

            .video-info {
                width: 40%;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .video-section {
                padding: 4rem 1rem;
            }

            .video-info h3 {
                font-size: 1.5rem;
            }

            .video-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            
            /* Move only the video name and button up, not the background */
            .video-front-content h4 {
                transform: translateY(-10px); /* Move only the title up */
            }
            
            .video-flip-hint {
                transform: translateY(-20px); /* Move the button up more */
            }
            
            .video-item {
                height: 200px; /* Reduced height */
                perspective: 1000px !important;
            }
            
            .video-item-inner {
                transform-style: preserve-3d !important;
            }
            
            .video-front, .video-back {
                backface-visibility: hidden !important;
                position: absolute !important;
                top: 0 !important;
                left: 0 !important;
            }
            
            .video-front {
                z-index: 2 !important;
                transform: rotateY(0deg) !important;
            }
            
            .video-back {
                transform: rotateY(180deg) !important;
            }
        }

        @media (max-width: 576px) {
            .video-grid {
                grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            }

            .video-item {
                height: 180px; /* Even smaller for mobile */
            }
            
            /* Move only the video name and button up more on small mobile */
            .video-front-content h4 {
                transform: translateY(-15px); /* Move only the title up more */
            }
            
            .video-flip-hint {
                transform: translateY(-25px); /* Move the button up even more */
            }

            .play-button {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .modal-content {
                margin: 15% auto;
                width: 95%;
            }
        }
        
        /* Compact Responsive Media Queries */
        /* Large devices (desktops, less than 1200px) */
        @media (max-width: 1199.98px) {
            .profile-dropdown {
                right: -15px;
            }
            
            .profile-avatar {
                width: 60px;
                height: 60px;
                margin: 0 auto 8px;
            }
            
            .profile-header {
                padding: 15px;
            }
            
            .menu-item {
                padding: 8px 15px;
                margin-bottom: 5px;
            }
        }
        
        /* Medium devices (tablets, less than 992px) */
        @media (max-width: 991.98px) {
            .user-dropdown {
                margin: 10px 30px;
            }
            
            .profile-dropdown {
                width: 250px;
                position: absolute;
                right: 0;
                top: calc(100% + 10px);
            }
            
            .profile-avatar {
                width: 50px;
                height: 50px;
                font-size: 20px;
                margin-bottom: 5px;
            }
            
            .profile-header {
                padding: 12px;
            }
            
            .menu-section {
                padding: 10px;
            }
            
            .navbar.active .user-dropdown {
                display: block;
                width: 100%;
            }
            
            .navbar.active .profile-dropdown {
                width: calc(100% - 30px);
                margin: 5px 15px;
                position: relative;
                top: 0;
                right: 0;
                background: var(--card-bg);
                z-index: 1002;
                opacity: 0;
                visibility: hidden;
                transform: scale(0.95);
            }
            
            .navbar.active .user-dropdown.active .profile-dropdown {
                opacity: 1;
                visibility: visible;
                transform: scale(1);
            }
        }
        
        /* Small devices (landscape phones, less than 768px) */
@media (max-width: 767.98px) {
    .profile-dropdown {
        width: 240px;
    }
    
    .profile-btn {
        width: 110px;
        height: 38px;
        font-size: 14px;
    }
    
    .profile-btn .avatar {
        width: 26px;
        height: 26px;
        font-size: 14px;
    }
    
    .profile-btn .user-name {
        max-width: 50px;
    }
            
            .menu-item {
                padding: 7px 12px;
                margin-bottom: 3px;
            }
            
            .menu-item .icon {
                width: 28px;
                height: 28px;
                font-size: 14px;
                margin-right: 8px;
            }
            
            .profile-header h4 {
                font-size: 15px;
            }
            
            .profile-header p {
                font-size: 11px;
                margin-top: 2px;
            }
            
            .account-actions {
                padding: 0 10px 10px;
            }
            
            .account-actions a {
                padding: 6px;
                font-size: 11px;
            }
            
            /* Ensure mobile dropdown menu displays properly */
            .navbar.active .user-dropdown {
                width: 100%;
                margin: 0;
                padding: 10px 0;
            }
            
            .navbar.active .profile-btn {
                margin-left: 30px;
            }
        }
        
        /* Extra small devices (portrait phones, less than 576px) */
@media (max-width: 575.98px) {
    .user-dropdown {
        margin: 8px 15px;
    }
    
    .profile-btn {
        width: 100px;
        height: 35px;
        font-size: 13px;
        padding: 0 8px;
    }
    
    .profile-btn .avatar {
        width: 24px;
        height: 24px;
        font-size: 12px;
        margin-right: 6px;
    }
    
    .profile-btn .user-name {
        max-width: 45px;
    }
    
    .profile-btn .dropdown-icon {
        font-size: 10px;
    }
            
            .profile-dropdown {
                width: 220px;
            }
            
            .profile-avatar {
                width: 40px;
                height: 40px;
                font-size: 16px;
                margin-bottom: 5px;
                border-radius: 12px;
            }
            
            .menu-section {
                padding: 8px;
            }
            
            .menu-item {
                padding: 6px 10px;
                margin-bottom: 2px;
                font-size: 13px;
            }
            
            .menu-item .icon {
                width: 25px;
                height: 25px;
                font-size: 12px;
                margin-right: 7px;
            }
            
            .account-actions {
                padding: 0 8px 8px;
            }
            
            .account-actions a {
                padding: 5px;
                font-size: 10px;
            }
            
            /* Full-width dropdown when in collapsed navbar */
            .navbar.active .profile-dropdown {
                width: calc(100% - 20px);
                margin: 5px 10px;
            }
            
            /* Fix for mobile dropdown display */
            .navbar.active .user-dropdown .profile-dropdown {
                display: none;
            }
            
            .navbar.active .user-dropdown.active .profile-dropdown {
                display: block;
                position: relative;
                top: 10px;
                right: auto;
                left: auto;
                z-index: 1002;
            }
        }
        
        /* Fix for very small screens */
        @media (max-width: 359.98px) {
            .profile-dropdown {
                width: 190px;
            }
            
            .profile-header {
                padding: 10px;
            }
            
            .profile-avatar {
                width: 35px;
                height: 35px;
                font-size: 14px;
                margin-bottom: 3px;
                border-radius: 10px;
            }
            
            .menu-item {
                padding: 5px 8px;
                font-size: 12px;
            }
            
            .menu-item .icon {
                width: 22px;
                height: 22px;
                font-size: 11px;
                margin-right: 6px;
            }
            
            .profile-header h4 {
                font-size: 13px;
            }
            
            .profile-header p {
                font-size: 10px;
            }
        }

        /* Theme toggle button - Stylish switch design */
        .theme-toggle {
            position: fixed;
            left: 20px;
            top: 180px; /* Moved further down */
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

        /* Media queries for responsive design */
        @media (max-width: 991px) {
            .theme-toggle {
                top: 170px; /* Moved further down */
                left: 20px;
                width: 54px;
                height: 28px;
                border-radius: 14px;
            }
            
            .toggle-handle {
                width: 18px;
                height: 18px;
                left: 5px;
            }
            
            .dark-mode .toggle-handle {
                transform: translateX(26px);
            }
        }
        
        @media (max-width: 768px) {
            .theme-toggle {
                top: 150px; /* Moved further down */
                left: 15px;
                width: 50px;
                height: 26px;
                border-radius: 13px;
            }
            
            .toggle-handle {
                width: 18px;
                height: 18px;
                left: 4px;
            }
            
            .dark-mode .toggle-handle {
                transform: translateX(24px);
            }
            
            .toggle-icons {
                padding: 0 6px;
            }
            
            .toggle-icons i {
                font-size: 10px;
            }
        }
        
        @media (max-width: 576px) {
            .theme-toggle {
                top: 130px; /* Moved further down */
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
        }
        
        /* When page is scrolled and header is hidden */
        @media (max-height: 500px) {
            .theme-toggle {
                top: 70px;
            }
        }

        /* For landscape orientation on mobile */
        @media (max-height: 450px) and (orientation: landscape) {
            .theme-toggle {
                top: 70px;
                left: 10px;
            }
        }

        /* Enhanced Login Button with Shine Animation */
        .login-btn {
            background: var(--secondary-color);
            color: var(--text-color);
            padding: 10px 25px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            z-index: 1;
            /* Adding shine border effect */
            border: 2px solid var(--text-color);
            background-clip: padding-box;
            animation: shine-border 3s linear infinite;
        }

        @keyframes shine-border {
            0% {
                border-color: var(--text-color);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            25% {
                border-color: var(--text-color);
                box-shadow: 0 4px 20px rgba(255, 255, 255, 0.5), 0 0 15px var(--secondary-color);
            }
            50% {
                border-color: var(--text-color);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
            75% {
                border-color: var(--text-color);
                box-shadow: 0 4px 20px rgba(255, 255, 255, 0.5), 0 0 15px var(--secondary-color);
            }
            100% {
                border-color: var(--text-color);
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            }
        }

        /* Create a shine sweep effect */
        .login-btn::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                to bottom right,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0) 40%,
                rgba(255, 255, 255, 0.6) 50%,
                rgba(255, 255, 255, 0) 60%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: rotate(45deg);
            pointer-events: none;
            z-index: 2;
            animation: shine-sweep 4s linear infinite;
        }

        @keyframes shine-sweep {
            0% {
                transform: rotate(45deg) translateX(-150%);
            }
            100% {
                transform: rotate(45deg) translateX(150%);
            }
        }

        .login-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--secondary-color);
            z-index: -1;
            transition: opacity 0.3s ease;
            opacity: 0;
            border-radius: 30px;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15), 0 0 20px var(--secondary-color);
            color: #fff;
            animation: shine-border 1.5s linear infinite;
        }
        
        .login-btn:hover::after {
            animation: shine-sweep 2s linear infinite;
        }
        
        .login-btn:hover:before {
            opacity: 1;
        }

        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 24px;
            color: var(--text-color);
            transition: color 0.3s ease;
        }

        /* Home section styles */
        .home {
            min-height: 110vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            /* Added for cross-browser compatibility */
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        /* Home section bottom fade effect */
        .home::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to bottom, transparent, var(--bg-color));
            z-index: 5;
            pointer-events: none;
        }

        /* Add responsive home section height for small screens */
        @media (max-width: 767.98px) {
            .home {
                min-height: 110vh; /* Set exact viewport height */
                max-height: 110vh;
                height: 110vh;
                /* Safari and iOS fixes */
                height: -webkit-fill-available;
                min-height: -webkit-fill-available;
                /* Prevent content overflow */
                overflow-y: hidden;
            }
        }

        .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1s ease-in-out;
            /* Fix for Safari and older browsers */
            -webkit-backface-visibility: hidden;
            -moz-backface-visibility: hidden;
            backface-visibility: hidden;
            -webkit-transform: translateZ(0);
            -moz-transform: translateZ(0);
            transform: translateZ(0);
        }

        .slide.active {
            opacity: 1;
        }

        .slide:nth-child(1) {
            background-image:url('images/home-bg-34.jpg');
        }

        .slide:nth-child(2) {
            background-image:url('images/home-bg-11.jpg');
        }

        .slide:nth-child(3) {
            background-image:url('images/home-bg-12.jpg');
        }

        .home-content {
            text-align: center;
            position: relative;
            z-index: 10;
            max-width: 800px;
            padding: 0 20px;
        }

        .content-box span {
            font-size: 4rem;
            text-shadow: 2px 2px 5px rgb(0, 0, 0);
            margin-bottom: 20px;
            color: #000a1a;
            text-transform: uppercase;
            font-weight: 700;
            
        }

        .content-box {
            display: none;
            animation: fadeIn 1s ease-in-out;
        }

        .content-box.active {
            display: block;
        }

        .content-box .btn {
            z-index: 10;
            margin-top: 1rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.75);
            display: inline-block;
            border-radius: 20rem;
            color:rgb(255, 255, 255);
            cursor: pointer;
            background: rgb(23, 108, 101);
            font-size: 1rem;
            padding: 0.5rem 1.5rem;
            position: relative;
            transform: scale(1);
            transition: transform 0.3s ease;
            text-decoration: none;
        }
        .content-box .btn:hover {
            background:rgb(0, 255, 204);
            color: black;
            transform: scale(1.1);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.75);
        }

        /* Social links styling for home section */
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
        }

        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(0, 0, 0, 0.47);
            border-radius: 10px;
            color: #fff;
            font-size: 22px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
            position: relative;
            overflow: hidden;
            z-index: 1;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.75);
        }
        
        /* Platform-specific colors */
        .social-link.whatsapp {
            background: rgba(37, 211, 102, 0.8); /* WhatsApp green */
        }
        
        .social-link.facebook {
            background: rgba(66, 103, 178, 0.8); /* Facebook blue */
        }
        
        .social-link.instagram {
            background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); /* Instagram gradient */
        }
        
        .social-link.email {
            background: rgba(234, 67, 53, 0.8); /* Gmail red */
        }

        .social-link:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgb(0, 255, 204);
            transform: translateY(100%);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: -1;
            opacity: 0.3;
        }
        
        /* Custom hover effects for each platform */
        .social-link.whatsapp:before {
            background: #25D366; /* WhatsApp green */
        }
        
        .social-link.facebook:before {
            background: #4267B2; /* Facebook blue */
        }
        
        .social-link.instagram:before {
            background: #E1306C; /* Instagram primary pink */
        }
        
        .social-link.email:before {
            background: #EA4335; /* Gmail red */
        }

        .social-link:hover {
            color: #fff;
            transform: translateY(-8px) rotate(8deg);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.75);
            filter: brightness(1.2);
        }

        .social-link:hover:before {
            transform: translateY(0);
        }

        .social-link i {
            position: relative;
            z-index: 2;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .home-content h1 {
            font-size: 4rem;
            color: #666;
            text-shadow: 2px 2px 5px rgb(0, 0, 0);
            margin-bottom: 20px;
        }

        .home-content p {
            font-size: 1.2rem;
            color: #f5f5f5;
            margin-bottom: 30px;
            text-shadow: 1px 1px 3px rgb(0, 0, 0);
        }

        .slider-controls {
            position: absolute;
            bottom: 30px;
            display: flex;
            justify-content: center;
            width: 100%;
            z-index: 10;
        }

        .slider-dot {
            width: 15px;
            height: 10px;
            border-radius: 40%;
            background: rgba(255, 255, 255, 0.5);
            margin: 0 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .slider-dot.active {
            background: var(--text-color);
        }

        .slider-dots {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 2px;
            z-index: 10;
        }

        /* Stats Counter Section Styles */
        .stats-counter {
            padding: 5rem 0;
            background: var(--bg-color);
            position: relative;
            overflow: hidden;
        }

        /* Statistics section top fade effect */
        .stats-counter::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 100px;
            background: linear-gradient(to bottom, var(--bg-color), transparent);
            z-index: 1;
            pointer-events: none;
        }
        

        
        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            padding: 0 1rem;
            position: relative;
            z-index: 2;
        }
        
        .stats-counter::before {
            top: -150px;
            left: -100px;
        }
        
        .stats-counter::after {
            bottom: -150px;
            right: -100px;
        }
        
        .stat-item {
            flex: 1;
            min-width: 220px;
            padding: 2rem 1rem;
            text-align: center;
            position: relative;
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        
        .stat-item:hover {
            transform: translateY(-10px);
        }
        
        /* Reveal animation for counter items */
        .stat-item {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        
        .stat-item.reveal {
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Add subtle dividers between stat items */
        .stat-item:not(:last-child)::after {
            content: "";
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: linear-gradient(to bottom, transparent, var(--border-alt-color), transparent);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: #fff;
            margin-bottom: 1.5rem;
            display: inline-flex;
            position: relative;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, rgba(0, 255, 204, 0.8) 100%);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        
        .stat-item:hover .stat-icon {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.25);
        }
        
        /* Add icon pulse effect */
        .stat-icon::after {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-color) 0%, rgba(0, 255, 204, 0.8) 100%);
            border-radius: 50%;
            left: 0;
            top: 0;
            opacity: 0.3;
            z-index: -1;
            transform: scale(0.8);
            animation: pulse-animation 2.5s infinite;
            filter: blur(5px);
        }
        
        @keyframes pulse-animation {
            0% {
                transform: scale(0.8);
                opacity: 0.1;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.15;
            }
            100% {
                transform: scale(0.8);
                opacity: 0.1;
            }
        }
        
        .stat-counter {
            font-size: 3rem;
            font-weight: 700;
            color: var(--text-color);
            line-height: 1;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .stat-counter-value {
            display: inline-block;
            background: linear-gradient(135deg, var(--text-color) 0%, var(--primary-color) 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            position: relative;
        }
        
        .modern-plus {
            position: relative;
            display: inline-flex;
            width: 20px;
            height: 20px;
            margin-left: 5px;
            vertical-align: super;
            transition: transform 0.3s ease;
        }
        
        .stat-item:hover .modern-plus {
            transform: rotate(90deg);
        }
        
        .modern-plus::before,
        .modern-plus::after {
            content: '';
            position: absolute;
            background: linear-gradient(135deg, var(--text-color) 0%, var(--primary-color) 100%);
            border-radius: 2px;
        }
        
        .modern-plus::before {
            width: 12px;
            height: 3px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .modern-plus::after {
            width: 3px;
            height: 12px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        .stat-title {
            font-size: 1.2rem;
            color: var(--text-color);
            font-weight: 500;
            position: relative;
            display: inline-block;
            padding-bottom: 0.5rem;
        }
        
        /* Responsive styles for stats counter */
        @media (max-width: 991px) {
            .stats-container {
                flex-wrap: nowrap;
                justify-content: space-between;
            }
            
            .stat-item {
                min-width: 120px;
                padding: 2rem 0.5rem;
                flex-basis: auto;
                flex: 1;
            }
            
            .stat-item:nth-child(2)::after {
                display: none;
            }
            
            .stat-icon {
                font-size: 1.8rem;
                margin-bottom: 1.2rem;
                width: 70px;
                height: 70px;
            }
            
            .stat-counter {
                font-size: 2.5rem;
            }
            
            .stat-title {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 768px) {
            .stats-counter {
                padding: 3.5rem 0;
            }
            
            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
                margin-bottom: 0.8rem;
            }
            
            .stat-counter {
                font-size: 2rem;
            }
            
            .modern-plus {
                width: 16px;
                height: 16px;
            }
            
            .modern-plus::before {
                width: 10px;
                height: 2.5px;
            }
            
            .modern-plus::after {
                width: 2.5px;
                height: 10px;
            }
            
            .stat-title {
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .stats-counter {
                padding: 3rem 0;
            }
            
            .stats-container {
                gap: 0.5rem;
                justify-content: space-between;
            }
            
            .stat-item {
                flex-basis: auto;
                min-width: 0;
                padding: 0.8rem 0.3rem;
                margin-bottom: 0;
                flex: 1;
                max-width: 25%;
            }
            
            .stat-item::after {
                display: none;
            }
            
            .stat-icon {
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
                margin-bottom: 0.5rem;
            }
            
            .stat-counter {
                font-size: 1.5rem;
            }
            
            .modern-plus {
                width: 14px;
                height: 14px;
                margin-left: 3px;
            }
            
            .modern-plus::before {
                width: 8px;
                height: 2px;
            }
            
            .modern-plus::after {
                width: 2px;
                height: 8px;
            }
            
            .stat-title {
                font-size: 0.75rem;
            }
        }
        
        /* Extra small devices */
        @media (max-width: 375px) {
            .stats-container {
                gap: 0.2rem;
            }
            
            .stat-icon {
                width: 32px;
                height: 32px;
                font-size: 1rem;
                margin-bottom: 0.3rem;
            }
            
            .stat-counter {
                font-size: 1.2rem;
            }
            
            .modern-plus {
                width: 12px;
                height: 12px;
                margin-left: 2px;
            }
            
            .modern-plus::before {
                width: 7px;
                height: 1.8px;
            }
            
            .modern-plus::after {
                width: 1.8px;
                height: 7px;
            }
            
            .stat-title {
                font-size: 0.65rem;
            }
        }

        /* Responsive design */
        @media (max-width: 991px) {
            .header {
                padding: 15px 20px;
            }
            
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
                display: block; /* Change from flex to block for mobile view */
                border-radius: 30px; /* Added rounded bottom corners */
            }
            
            .navbar.active {
                clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%);
            }
            
            .navbar a {
                display: block;
                margin: 15px 0;
                padding: 12px 30px;
                font-size: 18px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .login-btn {
                margin: 15px 30px;
                display: inline-block;
                padding: 8px 20px;
                font-size: 14px;
                width: auto;
                max-width: 120px;
                text-align: center;
                position: relative;
                left: 25px;
            }
            
            /* Adjust user dropdown for mobile */
            .user-dropdown {
                margin: 15px 30px;
            }
            
            .profile-btn {
                width: 120px;
                max-width: 120px;
                height: 38px;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: center;
            }

            .home-content h1 {
                font-size: 2.5rem;
            }

            .home-content p {
                font-size: 1rem;
            }

            .home-content span{
                font-size: 3rem;
            }
        }

        /* Packages Section Styles */
        .packages {
            padding: 6rem 2rem;
            background-image: var(--section-filter), url('images/section-bg43.PNG');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: var(--text-color);
        }
        
        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 3rem;
            color: var(--text-color);
            position: relative;
        }

        .section-title span {
            color: rgb(23, 108, 101);
        }

        .packages-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Ensure consistent card sizing and spacing */
        .package-card, .vehicle-card {
            margin: 0 auto 1.5rem;
            height: 400px;
        }
        
        /* Swiper container styles */
        .swiper {
            width: 100%;
            padding: 20px 0;
            overflow: visible;
        }

        .swiper-slide {
            display: flex;
            justify-content: center;
            align-items: center;
            height: auto;
        }

        .swiper-pagination {
            position: relative;
            margin-top: 20px;
        }

        .swiper-pagination-bullet {
            width: 15px;
            height: 10px;
            border-radius: 40%;
            background: var(--text-color);
            opacity: 0.5;
        }

        .swiper-pagination-bullet-active {
            opacity: 1;
            background: var(--text-color);
        }

        .swiper-button-next, .swiper-button-prev {
            color: rgb(23, 108, 101);
            background: rgba(255, 255, 255, 0.8);
            width: 40px;
            height: 40px;
            border-radius: 40%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-top: -20px;
            top: 50%;
        }

        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 18px;
            font-weight: bold;
        }
        
        .swiper-button-next {
            right: 10px;
        }
        
        .swiper-button-prev {
            left: 10px;
        }

        .swiper-button-disabled {
            opacity: 0.35;
        }

        .package-card {
            width: 350px;
            background: rgb(0, 0, 0);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgb(0, 0, 0);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .package-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 5px 15px rgb(0, 0, 0);
        }

        .card-image {
            height: 200px;
            overflow: hidden;
            position: relative;
        }

        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .package-card:hover .card-image img {
            transform: scale(1.1);
        }

        /* Popular ribbon sticker */
        .ribbon-wrapper {
            width: 85px;
            height: 88px;
            overflow: hidden;
            position: absolute;
            top: -3px;
            right: -3px;
            z-index: 10;
        }
        
        .ribbon {
            font-size: 12px;
            font-weight: bold;
            text-align: center;
            transform: rotate(45deg);
            position: relative;
            padding: 7px 0;
            left: -5px;
            top: 15px;
            width: 120px;
            background: linear-gradient(135deg, #f44336, #e91e63);
            color: white;
            box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
            overflow: hidden;
        }
        
        /* Shine effect */
        .ribbon-corner-left {
            content: "";
            position: absolute;
            left: 0;
            bottom: -3px;
            border-top: 3px solid #a20000;
            border-left: 3px solid transparent;
            border-right: 3px solid transparent;
        }
        
        .ribbon-corner-right {
            content: "";
            position: absolute;
            right: 0;
            bottom: -3px;
            border-top: 3px solid #a20000;
            border-left: 3px solid transparent;
            border-right: 3px solid transparent;
        }
        
        .ribbon::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 200%;
            height: 100%;
            background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,0.3) 50%, rgba(255,255,255,0) 100%);
            transform: translateX(-100%);
            animation: ribbonShine 2s infinite ease-in-out;
            z-index: 1;
        }
        
        @keyframes ribbonShine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        
        .card-content {
            padding: 20px;
        }

        .card-content h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: var(--text-color);
        }

        .card-content p {
            font-size: 0.9rem;
            color: var(--text-color);
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .card-features {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        .feature {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .feature-icon {
            font-style: normal;
            font-size: 1.2rem;
        }

        .feature span {
            font-size: 0.9rem;
            color: #fff;
        }

        .card-btn {
            display: block;
            text-align: center;
            background: rgb(23, 108, 101);
            color: white;
            padding: 12px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s, transform 0.3s, border 0.3s;
        }

        .card-btn:hover {
            background: rgb(0, 255, 204);
            color: rgb(62, 62, 62);
            transform: scale(1.05);
            border: 2px solid rgb(23, 108, 101);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .packages {
                padding: 4rem 1rem;
                background-attachment: scroll;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .package-card {
                width: 100%;
                max-width: 400px;
                background: rgb(0, 0, 0);
            }
        }

        /* Vehicle Hire Section Styles */
        .vehicle-hire {
            padding: 6rem 2rem;
            background-color: var(--bg-color);
            color: var(--text-color);
        }

        .vehicles-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Responsive adjustments for Swiper */
        @media (max-width: 768px) {
            .packages-container, .vehicles-container {
                display: none; /* Hide standard containers on mobile */
            }
            
            .packages-swiper, .vehicles-swiper {
                display: block; /* Show swiper on mobile */
                max-width: 100%;
                overflow: hidden;
            }
            
            .swiper-button-next, .swiper-button-prev {
                width: 35px;
                height: 35px;
            }
            
            .swiper-button-next:after, .swiper-button-prev:after {
                font-size: 16px;
            }
            
            .swiper-slide {
                height: auto;
                width: 100%;
                max-width: 350px;
            }
            
            .package-card, .vehicle-card {
                width: 100%;
                margin: 0 auto;
            }
        }

        @media (min-width: 769px) {
            .packages-swiper, .vehicles-swiper {
                display: none; /* Hide swiper on desktop */
            }
            
            .packages-container, .vehicles-container {
                display: flex; /* Show standard containers on desktop */
            }
        }

        .vehicle-card {
            width: 350px;
            background: rgb(0, 0, 0);
            box-shadow: 0 5px 15px rgb(0, 0, 0);
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .vehicle-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 5px 15px rgb(0, 0, 0);
        }

        .vehicle-card .card-image {
            height: 220px;
            overflow: hidden;
        }

        .vehicle-card .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .vehicle-card:hover .card-image img {
            transform: scale(1.1);
        }

        /* Rest of the styling for vehicles can reuse the package card styling */
        .vehicle-card .card-content h3 {
            color: rgb(23, 108, 101);
        }
        
        /* Responsive adjustment for vehicle section */
        @media (max-width: 768px) {
            .vehicle-hire {
                padding: 4rem 1rem;
            }
            
            .vehicle-card {
                width: 100%;
                max-width: 400px;
                background: rgb(0, 0, 0);
            }
        }

        /* Destinations Section Styles */
        .destinations {
            padding: 6rem 2rem;
            background-image: var(--section-filter), url('images/section-bg38.PNG');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: var(--text-color);
        }

        .destinations-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .destination-card {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgb(0, 0, 0);
            height: 320px;
            transition: transform 0.3s ease;
        }

        .destination-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgb(0, 0, 0);
        }

        .destination-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .destination-card:hover img {
            transform: scale(1.1);
        }

        .destination-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: white;
            padding: 20px;
            transition: all 0.3s ease;
        }

        .destination-card:hover .destination-content {
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.9));
        }

        .destination-content h3 {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        .destination-content p {
            font-size: 0.9rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .destination-btn {
            display: inline-block;
            background: rgb(23, 108, 101);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .destination-btn:hover {
            background: rgb(18, 88, 82);
            color: rgb(255, 255, 255);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .destinations {
                padding: 4rem 1rem;
                background-attachment: scroll;
            }
            
            .destinations-container {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
            
            .destination-card {
                height: 280px;
            }
        }

        .explore-more-container {
            text-align: center;
            margin-top: 2.5rem;
        }

        .explore-more-btn {
            display: inline-block;
            background: rgb(23, 108, 101);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        .explore-more-btn:hover {
            background: rgb(18, 88, 82);
            color: rgb(255, 255, 255);
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }

        /* Reviews Section Styles */
        .reviews {
            padding: 6rem 2rem;
            background-image: var(--section-filter), url('images/section-bg38.PNG');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: var(--text-color);
        }
        
        /* Tripadvisor Widget Styles */
        .tripadvisor-container {
            max-width: 900px;
            margin: 0 auto 40px;
        }
        
        .reviews-subtitle {
            text-align: center;
            margin-bottom: 15px;
            color: var(--text-color);
            font-size: 1.3rem;
            font-weight: 500;
        }
        
        .elfsight-app-afdb59ed-6945-45ea-908d-df2311efbab9 {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgb(0, 0, 0);
            background-color: var(--card-bg);
            padding: 20px;
        }
        
        .reviews-container {
            background-color: var(--card-bg);
            max-width: 900px;
            margin: 0 auto;
            position: relative;
            box-shadow: 0 5px 15px rgb(0, 0, 0);
            border-radius: 15px;
        }
        
        .reviews-subtitle  {
            padding-top: 20px;
        }

        .reviews-slider {
            position: relative;
            min-height: 320px;
            overflow: hidden;
        }
        
        .review-card {
            position: absolute;
            top: 0;
            left: 0;
            width: 87%;
            margin-left: 6.5%;
            margin-right: 6.5%;
            background-color: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px var(--card-shadow);
            opacity: 0;
            transition: all 0.5s ease, background-color 0.3s ease, box-shadow 0.3s ease;
            transform: translateX(50px);
            display: none;
            height: auto;
        }
        
        .review-card.active {
            opacity: 1;
            transform: translateX(0);
            display: block;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .user-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 15px;
            border: 3px solid rgb(23, 108, 101);
        }
        
        .user-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .user-details h3 {
            margin: 0 0 5px;
            font-size: 1.2rem;
            color: var(--text-color);
            transition: color 0.3s ease;
        }
        
        .rating {
            color: #ffc107;
            font-size: 1.2rem;
        }
        
        .star.half {
            position: relative;
            display: inline-block;
        }
        
        .star.half:after {
            content: "★";
            position: absolute;
            left: 0;
            top: 0;
            width: 50%;
            overflow: hidden;
            color: #e0e0e0;
        }
        
        .review-text {
            font-size: 1rem;
            line-height: 1.6;
            color: var(--text-color);
            margin-bottom: 20px;
            font-style: italic;
            max-height: 200px;
            overflow-y: auto;
            padding-right: 5px;
            transition: color 0.3s ease;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.3) transparent;
        }
        
        .review-text::-webkit-scrollbar {
            width: 6px;
        }
        
        .review-text::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .review-text::-webkit-scrollbar-thumb {
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 6px;
        }
        
        .dark-mode .review-text::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.3);
        }
        
        .tour-type {
            display: inline-block;
            background-color: rgb(23, 108, 101);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .review-date {
            font-size: 0.8rem;
            color: #888;
            margin-top: 10px;
            text-align: right;
            transition: color 0.3s ease;
        }
        
        .dark-mode .review-date {
            color: #aaa;
        }
        
        .slider-controls {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-top: 30px;
        }
        
        .prev-btn, .next-btn {
            background-color: rgba(255, 255, 255, 0.8);
            color: rgb(23, 108, 101);
            width: 40px;
            height: 40px;
            border-radius: 40%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.2rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 2;
            position: relative;
        }
        
        .prev-btn:hover, .next-btn:hover {
            background-color: rgba(255, 255, 255, 0.8);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .dots-container {
            display: flex;
            margin: 0 15px;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 70%;
        }
        
        .dot {
            width: 15px;
            height: 10px;
            background-color: #aaa;
            border-radius: 40%;
            margin: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .dot.active {
            background-color: var(--text-color);
        }
        
        .review-cta {
            text-align: center;
            margin-top: 50px;
        }
        
        .review-cta p {
            margin-bottom: 15px;
            color: var(--text-color);
        }
        
        .review-btn {
            display: inline-block;
            background: rgb(23, 108, 101);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            font-size: 1rem;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }
        
        .review-btn:hover {
            background: rgb(18, 88, 82);
            color: rgb(255, 255, 255);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        
        /* Responsive adjustments */
        @media (max-width: 992px) {
            .reviews {
                padding: 5rem 1.5rem;
            }
            
            .reviews-slider {
                min-height: 350px;
            }
            
            .review-card {
                padding: 25px;
            }
            
            .user-img {
                width: 55px;
                height: 55px;
            }
            
            .user-details h3 {
                font-size: 1.1rem;
            }
        }
        
        @media (max-width: 768px) {
            .reviews {
                padding: 4rem 1rem;
                background-attachment: scroll;
            }
            
            .reviews-slider {
                min-height: 380px;
            }
            
            .review-card {
                padding: 20px;
            }
            
            .prev-btn, .next-btn {
                width: 36px;
                height: 36px;
                font-size: 1rem;
            }
            
            .review-text {
                max-height: 180px;
                font-size: 0.95rem;
            }
            
            .dots-container {
                max-width: 65%;
            }
            
            .slider-controls {
                margin-top: 20px;
            }
            
            .review-cta {
                margin-top: 35px;
            }
        }
        
        @media (max-width: 576px) {
            .reviews-slider {
                min-height: 420px;
            }
            
            .dots-container {
                max-width: 60%;
            }
            
            .dot {
                border-radius: 40%;
                width: 9.5px;
                height: 7px;
                margin: 3px;
            }
            
            .prev-btn, .next-btn {
                width: 32px;
                height: 32px;
            }
            
            .review-text {
                font-size: 0.92rem;
                line-height: 1.5;
                max-height: 160px;
                margin-bottom: 15px;
            }
            
            .user-img {
                width: 50px;
                height: 50px;
                border-width: 2px;
            }
            
            .user-info {
                margin-bottom: 15px;
            }
            
            .user-details h3 {
                font-size: 1rem;
            }
            
            .rating {
                font-size: 1.1rem;
            }
            
            .review-date {
                font-size: 0.7rem;
                margin-top: 8px;
            }
        }
        
        @media (max-width: 480px) {
            .reviews-slider {
                min-height: 430px;
            }
            
            .review-card {
                padding: 18px;
            }
            
            .review-text {
                font-size: 0.9rem;
                max-height: 150px;
            }
            
            .slider-controls {
                margin-top: 15px;
            }
            
            .dots-container {
                max-width: 55%;
            }
        }
        
        @media (max-width: 375px) {
            .reviews-slider {
                min-height: 450px;
            }
            
            .review-card {
                padding: 15px;
            }
            
            .tour-type {
                padding: 4px 12px;
                font-size: 0.85rem;
            }
            
            .user-img {
                width: 45px;
                height: 45px;
                margin-right: 10px;
            }
            
            .review-text {
                font-size: 0.85rem;
                line-height: 1.45;
                max-height: 140px;
            }
            
            .dots-container {
                max-width: 50%;
            }
        }
        
        /* Special breakpoint for very small devices */
        @media (max-width: 320px) {
            .reviews-slider {
                min-height: 470px;
            }
            
            .review-card {
                padding: 12px;
            }
            
            .user-img {
                width: 40px;
                height: 40px;
            }
            
            .user-details h3 {
                font-size: 0.9rem;
            }
            
            .rating {
                font-size: 1rem;
            }
            
            .review-text {
                font-size: 0.8rem;
                line-height: 1.4;
                max-height: 130px;
            }
            
            .prev-btn, .next-btn {
                width: 28px;
                height: 28px;
                font-size: 0.9rem;
            }
        }
        
        /* Review Modal Styles */
        .review-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            overflow-y: auto;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .review-modal.active {
            display: block;
            opacity: 1;
        }
        
        .review-modal-content {
            position: relative;
            background-color: var(--card-bg);
            color: var(--text-color);
            margin: 20px auto;
            width: 95%;
            max-width: 600px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            padding: 20px 15px;
            transform: translateY(-30px);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(var(--primary-color), 0.1);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        @media (min-width: 768px) {
            .review-modal-content {
                padding: 30px;
                margin: 50px auto;
                width: 90%;
            }
        }
        
        .review-modal.active .review-modal-content {
            transform: translateY(0);
        }
        
        .close-review-modal {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            color: var(--text-color);
            opacity: 0.7;
            cursor: pointer;
            transition: all 0.2s ease;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, 0.05);
            z-index: 10;
        }
        
        @media (min-width: 768px) {
            .close-review-modal {
                top: 15px;
                right: 20px;
                font-size: 28px;
                background-color: transparent;
            }
        }
        
        .close-review-modal:hover {
            background-color: rgba(0, 0, 0, 0.1);
            opacity: 1;
            transform: rotate(90deg);
        }
        
        .review-modal h3 {
            color: var(--primary-color);
            font-size: clamp(1.2rem, 5vw, 1.5rem);
            margin-bottom: 15px;
            text-align: center;
            font-weight: 700;
            padding-top: 10px;
        }
        
        @media (min-width: 768px) {
            .review-modal h3 {
                margin-bottom: 25px;
                font-size: 24px;
                padding-top: 0;
            }
        }
        
        /* Form action buttons */
        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 20px;
        }
        
        .form-actions button {
            padding: 12px 0;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            flex: 1;
            text-align: center;
            font-size: 0.9rem;
        }
        
        @media (min-width: 768px) {
            .form-actions button {
                font-size: 1rem;
                padding: 12px 20px;
            }
        }
        
        .cancel-review {
            background-color: transparent;
            color: var(--text-color);
            border: 1px solid rgba(0, 0, 0, 0.2);
        }
        
        .cancel-review:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        
        .submit-review {
            background: linear-gradient(90deg, #4776E6 0%, #8E54E9 100%);
            color: white;
            border: none;
        }
        
        .submit-review:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Footer Styles */
        .footer {
            background-color: var(--footer-bg);
            color: var(--footer-text);
            padding-top: 3rem;
        }
        
        .footer-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem 3rem;
        }
        
        .footer h3 {
            color: #fff;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer h3::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 2px;
            background: rgb(23, 108, 101);
        }
        
        .footer-about p {
            line-height: 1.6;
            margin-bottom: 1.5rem;
            color: #ccc;
        }
        
        .social-icons {
            display: flex;
            gap: 1rem;
        }
        
        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgb(23, 108, 101);
            border-radius: 50%;
            color: white;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background: #fff;
            color: rgb(23, 108, 101);
            transform: translateY(-3px);
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.8rem;
        }
        
        .footer-links a {
            color: #ccc;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .footer-links a:hover {
            color: rgb(101, 255, 193);
            transform: translateX(5px);
        }
        
        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .contact-icon {
            font-style: normal;
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        .contact-item p {
            color: #ccc;
            line-height: 1.4;
        }
        
        .newsletter-form {
            display: flex;
            margin-top: 1.5rem;
        }
        
        .newsletter-form input {
            flex: 1;
            padding: 0.8rem;
            border: none;
            border-radius: 4px 0 0 4px;
            outline: none;
        }
        
        .newsletter-form button {
            background: rgb(23, 108, 101);
            color: white;
            border: none;
            padding: 0 1rem;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .newsletter-form button:hover {
            background: rgb(18, 88, 82);
        }
        
        .footer-bottom {
            background: #111;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .copyright p {
            margin: 0;
            color: #888;
        }
        
        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
        }
        
        .footer-bottom-links a {
            color: #888;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-bottom-links a:hover {
            color: #fff;
        }
        
        @media (max-width: 768px) {
            .footer-container {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }
            
            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .footer-bottom-links {
                justify-content: center;
            }
        }

        /* User profile dropdown - Modern minimalist style */
        .user-dropdown {
            display: inline-block;
            position: relative;
            margin-left: 25px;
            z-index: 1002; /* Ensure dropdown is above other elements */
        }

        .profile-btn {
            width: 120px;
            height: 40px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            background: var(--secondary-color);
            border: 1px solid var(--text-color);
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0 10px;
            color: var(--text-color);
            font-weight: 600;
            font-size: 14px;
        }

        .profile-btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--secondary-color);
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 12px;
        }

        .profile-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px var(--text-color);
        }
        
        .profile-btn:hover:before {
            opacity: 1;
        }
        
        .profile-btn .avatar {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            font-weight: bold;
            position: relative;
            z-index: 2;
        }
        
        .profile-btn .user-name {
            position: relative;
            z-index: 2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 65px;
        }
        
        .profile-btn .dropdown-icon {
            margin-left: auto;
            position: relative;
            z-index: 2;
            font-size: 12px;
            opacity: 0.8;
        }

        .profile-btn img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-dropdown {
            position: absolute;
            top: 120%;
            right: -10px;
            width: 240px;
            background: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            transform-origin: top right;
            transform: scale(0.95);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1001;
            overflow: hidden;
        }

        .profile-dropdown::before {
            content: '';
            position: absolute;
            top: -6px;
            right: 22px;
            width: 12px;
            height: 12px;
            background: var(--card-bg);
            transform: rotate(45deg);
            box-shadow: -2px -2px 5px rgba(0, 0, 0, 0.04);
        }

        .user-dropdown.active .profile-dropdown {
            transform: scale(1);
            opacity: 1;
            visibility: visible;
        }

        .menu-section {
            padding: 12px;
        }

        .user-brief {
            display: flex;
            align-items: center;
            padding-bottom: 12px;
            border-bottom: 1px solid #f0f0f0;
            margin-bottom: 8px;
        }

        .user-brief .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            margin-right: 12px;
            flex-shrink: 0;
            border: 2px solid rgb(23, 108, 101);
        }

        .user-brief .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-brief .user-meta {
            overflow: hidden;
        }

        .user-brief .name {
            font-weight: 600;
            color: var(--text-color);
            margin: 0;
            font-size: 0.95rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-brief .username {
            color: var(--text-color);
            opacity: 0.7;
            font-size: 0.8rem;
            margin: 0;
        }

        .menu-section .menu-item {
            display: flex;
            align-items: center;
            padding: 8px 0;
            color: var(--text-color);
            text-decoration: none;
            transition: color 0.2s;
        }

        .menu-section .menu-item:hover {
            color: rgb(23, 108, 101);
        }

        .menu-item .icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            color: rgb(23, 108, 101);
            margin-right: 10px;
            font-size: 1rem;
        }

        .menu-item.logout {
            color: #dc3545;
        }

        .menu-item.logout .icon {
            color: #dc3545;
        }

        .account-actions {
            background: var(--bg-alt-color);
            border-top: 1px solid var(--border-alt-color);
            display: flex;
        }

        .account-actions a {
            flex: 1;
            padding: 12px 0;
            text-align: center;
            color: var(--text-color);
            text-decoration: none;
            font-size: 0.85rem;
            transition: background 0.2s;
        }

        .account-actions a:hover {
            background: rgba(0, 0, 0, 0.05);
        }

        .dark-mode .account-actions a:hover {
            background: rgba(255, 255, 255, 0.05);
        }

        .account-actions a:first-child {
            border-right: 1px solid var(--border-alt-color);
        }

    /* Mobile responsive styles for dropdown */
    @media (max-width: 991px) {
    .navbar.active {
        background-color: var(--bg-color);
        padding-bottom: 15px;
        z-index: 1000;
        overflow-y: auto;
        max-height: 85vh;
    }
    
    .user-dropdown {
        margin: 15px 30px;
        display: inline-block;
    }

    .navbar .user-dropdown .profile-btn {
        width: 140px;
        height: 40px;
        border-radius: 10px;
        padding: 0 10px;
        justify-content: flex-start;
    }
    
    .navbar .user-dropdown .profile-btn .user-name {
        max-width: 80px;
        font-size: 14px;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    
    .navbar .user-dropdown .profile-btn .avatar {
        width: 30px;
        height: 30px;
        font-size: 16px;
        min-width: 30px;
        margin-right: 8px;
    }
    
    .navbar .user-dropdown .profile-btn .dropdown-icon {
        margin-left: 5px;
    }
    
    .navbar .user-dropdown .profile-btn .avatar {
        width: 35px;
        height: 35px;
        font-size: 18px;
    }
    
    .navbar .user-dropdown .profile-btn .user-name {
        max-width: 110px;
        font-size: 16px;
    }
    
    .navbar .user-dropdown .profile-btn .avatar {
        width: 35px;
        height: 35px;
        font-size: 18px;
    }

    .navbar .user-dropdown {
        position: relative;
        z-index: 10;
    }
    
    .profile-btn {
        width: 120px;
        height: 40px;
    }
            
            .profile-dropdown {
                position: absolute;
                width: 230px;
                right: 0;
                top: calc(100% + 5px);
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            
            .user-dropdown.active .profile-dropdown {
                opacity: 1;
                visibility: visible;
                transform: scale(1);
            }
            
            .navbar.active .user-dropdown {
                display: block;
                width: 100%;
                text-align: left;
            }
            
            .navbar.active .profile-btn {
                margin-left: 30px;
            }
            
            .navbar.active .profile-dropdown {
                width: calc(100% - 60px);
                margin-left: 30px;
                margin-right: 30px;
                right: auto;
                left: 0;
                position: absolute;
                top: 100%;
            }
            
            .navbar.active .user-dropdown.active {
                z-index: 1002;
            }
        }

        /* Small Mobile Devices */
        @media (max-width: 576px) {
            .user-dropdown {
                margin: 10px 20px;
            }
            
            .profile-btn {
                width: 100px;
                height: 35px;
            }
            
            .profile-dropdown {
                width: 210px;
            }
            
            .navbar .user-dropdown .profile-btn {
                width: 120px;
                height: 35px;
            }
            
            .navbar .user-dropdown .profile-btn .user-name {
                max-width: 60px;
                font-size: 13px;
            }
            
            .navbar .user-dropdown .profile-btn .avatar {
                width: 25px;
                height: 25px;
                font-size: 14px;
                min-width: 25px;
                margin-right: 6px;
            }
            
            .navbar.active .profile-dropdown {
                width: calc(100% - 40px);
                margin-left: 20px;
                margin-right: 20px;
            }
            
            .navbar.active .profile-btn {
                margin-left: 20px;
            }
            
            .menu-section {
                padding: 8px;
            }
            
            .user-brief {
                padding-bottom: 8px;
                margin-bottom: 5px;
            }
            
            .user-brief .avatar {
                width: 30px;
                height: 30px;
                margin-right: 8px;
            }
            
            .user-brief .name {
                font-size: 0.85rem;
            }
            
            .user-brief .username {
                font-size: 0.75rem;
            }
            
            .menu-section .menu-item {
                padding: 8px 0;
                font-size: 0.9rem;
            }
            
            .menu-item .icon {
                width: 24px;
                font-size: 0.9rem;
            }
            
            .account-actions a {
                padding: 8px 0;
                font-size: 0.8rem;
            }
        }

        /* Extra Small Mobile Devices */
        @media (max-width: 375px) {
            .navbar .user-dropdown .profile-btn {
                width: 100px;
                height: 32px;
            }
            
            .navbar .user-dropdown .profile-btn .user-name {
                max-width: 45px;
                font-size: 12px;
            }
            
            .navbar .user-dropdown .profile-btn .avatar {
                width: 22px;
                height: 22px;
                font-size: 12px;
                min-width: 22px;
                margin-right: 5px;
            }
        }

        .chat-btn-container {
            position: fixed;
            bottom: 20px; /* Will be dynamically adjusted to match AI assistant */
            left: 20px; 
            z-index: 999;
        }
        
        .chat-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgb(23, 108, 101);
            color: white;
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 20px;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .chat-btn:hover {
            background-color: rgb(18, 87, 82);
            transform: scale(1.05);
        }
        
        .chat-notification {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            font-size: 12px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .chat-box {
            position: fixed;
            bottom: 80px; /* Same distance from bottom as AI assistant window */
            left: 20px; 
            width: 350px;
            height: 450px;
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            display: none;
            flex-direction: column;
            z-index: 998;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .chat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgb(23, 108, 101);
            color: white;
            padding: 15px;
        }
        
        .chat-header h3 {
            margin: 0;
            font-size: 18px;
        }
        
        .close-chat {
            background: none;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }
        
        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: var(--bg-color);
        }
        
        .chat-welcome {
            background-color: #e9eff1;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 15px;
        }
        
        .chat-welcome p {
            margin: 0;
            color: #555;
        }
        
        .message {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }
        
        .message.user {
            align-items: flex-end;
        }
        
        .message.admin {
            align-items: flex-start;
        }
        
        .message-content {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 18px;
            word-wrap: break-word;
        }
        
        .user .message-content {
            background-color: rgb(101, 255, 193);
            color: #333;
            border-bottom-right-radius: 0;
        }
        
        .admin .message-content {
            background-color: #e0e0e0;
            color: #333;
            border-bottom-left-radius: 0;
        }
        
        .message-time {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
        
        .chat-input {
            display: flex;
            padding: 10px;
            background-color: white;
            border-top: 1px solid #eee;
        }
        
        .chat-input textarea {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 10px 15px;
            resize: none;
            height: 40px;
            font-size: 16px; /* Increased font size to prevent zoom */
            outline: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            touch-action: manipulation; /* Prevent browser manipulation */
        }
        
        .chat-input button {
            margin-left: 10px;
            background-color: rgb(23, 108, 101);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .chat-input button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        .chat-input button:not(:disabled):hover {
            background-color: rgb(18, 87, 82);
        }
        
        @media (max-width: 576px) {
            .chat-box {
                width: 90%;
                right: 5%;
                left: 5%;
            }
            
            /* Fix for mobile zoom issues */
            .chat-input textarea {
                font-size: 16px !important; /* iOS won't zoom if font size is at least 16px */
                transform: scale(1); /* Helps prevent zoom on some Android devices */
                transform-origin: left top;
                touch-action: manipulation; /* Prevents browser manipulation */
            }
        }

        /* New styles for reply, edit, delete functionality */
        .message {
            position: relative;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .message .message-actions {
            display: none;
            position: absolute;
            right: 5px;
            top: -20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            padding: 3px 8px;
        }

        .admin .message-actions {
            left: 5px;
            right: auto;
        }

        .message:hover .message-actions {
            display: flex;
        }

        .action-btn {
            background: none;
            border: none;
            font-size: 12px;
            color: #555;
            margin: 0 3px;
            cursor: pointer;
            padding: 2px;
            transition: color 0.2s;
        }

        .action-btn:hover {
            color: rgb(23, 108, 101);
        }

        .action-btn.delete-btn:hover {
            color: #dc3545;
        }

        .reply-preview, .edit-preview {
            display: flex;
            background-color: rgba(23, 108, 101, 0.1);
            padding: 8px 10px;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            align-items: center;
        }

        .reply-content, .edit-content {
            flex: 1;
            overflow: hidden;
            padding-left: 10px;
            border-left: 2px solid rgb(23, 108, 101);
        }

        .reply-content p, .edit-content p {
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-size: 12px;
            color: #666;
        }

        .cancel-action {
            background: none;
            border: none;
            color: #999;
            cursor: pointer;
            padding: 0 5px;
        }

        .cancel-action:hover {
            color: #dc3545;
        }

        .replied-message {
            margin-bottom: 5px;
            font-size: 12px;
            background-color: rgba(23, 108, 101, 0.1);
            padding: 5px 8px;
            border-radius: 8px;
            border-left: 2px solid rgb(23, 108, 101);
            color: #666;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 85%;
        }

        .message-edited {
            font-style: italic;
            margin-left: 5px;
            font-size: 10px;
            color: #999;
        }

        /* Adjust the existing message containers to accommodate actions */
        .message-content {
            max-width: 80%;
            padding: 10px 15px;
            border-radius: 18px;
            word-wrap: break-word;
            position: relative;
        }

        .message-time {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
            display: flex;
            align-items: center;
        }

        /* Additional media queries for smaller screens */
        @media (max-width: 576px) {
            .login-btn {
                margin: 10px 20px;
                padding: 6px 15px;
                font-size: 13px;
                max-width: 100px;
                text-align: center;
                justify-content: center;
                display: flex;
                align-items: center;
                position: relative;
                left: 20px; /* Move button right */
            }
            
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
            
            /* Adjust user dropdown for small mobile */
            .user-dropdown {
                margin: 10px 20px;
            }
            
            .profile-btn {
                width: 100px;
                max-width: 100px;
                height: 35px;
                font-size: 13px;
            }
            
            .profile-btn .avatar {
                width: 25px;
                height: 25px;
                font-size: 12px;
                margin-right: 5px;
            }
            
            .profile-btn .user-name {
                max-width: 60px;
            }
        }
        
        @media (max-width: 375px) {
            .login-btn {
                margin: 8px 15px;
                padding: 5px 10px;
                font-size: 12px;
                max-width: 80px;
                min-width: 70px;
                position: relative;
                left: 15px; /* Move button further right for smallest screens */
            }
            
            .navbar a {
                padding: 8px 15px;
                font-size: 16px;
                margin: 8px 0;
            }
            
            /* Adjust user dropdown for extra small mobile */
            .user-dropdown {
                margin: 8px 15px;
            }
            
            .profile-btn {
                width: 80px;
                max-width: 80px;
                height: 32px;
                font-size: 12px;
            }
            
            .profile-btn .avatar {
                width: 22px;
                height: 22px;
                font-size: 11px;
                margin-right: 4px;
            }
            
            .profile-btn .user-name {
                max-width: 45px;
            }
        }

        /* Packages and Vehicle Hire Flip Card Styles */
        .flip-card {
            height: 380px;
            cursor: pointer;
            position: relative;
            margin-bottom: 2rem;
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.5s ease, transform 0.5s ease;
            width: 100%;
            max-width: 350px;
        }
        
        /* Specific adjustments for cards with features */
        .vehicle-card.flip-card,
        .package-card.flip-card {
            height: 400px;
        }
        
        /* Card reveal animation when in viewport */
        .flip-card.reveal {
            opacity: 1;
            transform: translateY(0);
        }
        
        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transition: transform 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        .flip-card.flipped .flip-card-inner {
            transform: rotateY(180deg);
        }
        
        .flip-card-front, .flip-card-back {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgb(0, 0, 0);
        }
        
        .flip-card-front {
            background-color: #000;
            color: var(--text-color);
            z-index: 2;
            backface-visibility: hidden;
        }
        
/* Modified front image to take full height */
        .front-image {
            height: 100%;
            width: 100%;
            overflow: hidden;
            position: relative;
        }
        
        .front-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .flip-card:hover .front-image img {
            transform: scale(1.05);
        }
        
        .flip-card.flipped .front-image img {
            transform: scale(1.1);
        }
        
/* Modified front content to overlay on the image */
        .front-content {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            padding: 30px 20px; /* Increased top padding to move content up */
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            text-align: center;
            z-index: 3;
        }
        
        .front-content h3 {
            margin-bottom: 20px; /* Increased spacing between title and button */
            font-size: 1.6rem;
            color: #fff;
            text-align: center;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
            letter-spacing: normal;
        }
        
        .flip-hint {
            font-size: 0.85rem;
            color: #fff;
            position: relative;
            padding: 6px 15px;
            border-radius: 18px;
            background: rgb(23, 108, 101);
            animation: pulse 2s infinite;
            display: inline-block;
            font-weight: 600;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            margin-top: -15px; /* Move button up by creating negative top margin */
            cursor: pointer;
            transition: all 0.3s ease;
}

        .flip-hint:hover {
            background: rgb(18, 88, 82);
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
}

@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(23, 108, 101, 0.7);
    }
    
    70% {
        transform: scale(1.05);
        box-shadow: 0 0 0 10px rgba(23, 108, 101, 0);
    }
    
    100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(23, 108, 101, 0);
    }
        }
        
        .flip-card-back {
            background: linear-gradient(145deg, rgba(23, 108, 101, 0.9), rgba(101, 255, 193, 0.9));
            color: white;
            transform: rotateY(180deg);
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
            z-index: 1;
            position: relative;
        }
        
        .flip-back-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            width: 30px;
            height: 30px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: white;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .flip-back-btn:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: rotate(180deg);
        }
        
        .back-content {
             width: 100%;
             padding: 15px;
             display: flex;
             flex-direction: column;
             justify-content: space-between;
             height: 100%;
             overflow-y: auto;
        }
         
        .back-content h3 {
             margin-bottom: 10px;
             color: white;
             font-size: 2rem;
             font-weight: bold;
        }
         
        .back-content p {
             color: rgba(255, 255, 255, 0.9);
             margin-bottom: 10px;
             font-size: 0.9rem;
             line-height: 1.4;
             max-height: 60px;
             overflow-y: auto;
        }
         
        .card-features {
             margin-bottom: 15px;
        }
         
        .feature {
             margin-bottom: 8px;
             color: white;
             display: flex;
             align-items: center;
             background: rgba(255, 255, 255, 0.1);
             padding: 5px 10px;
             border-radius: 8px;
             text-align: left;
        }
         
        .feature-icon {
             margin-right: 8px;
             display: flex;
             align-items: center;
             justify-content: center;
             width: 25px;
             height: 25px;
             background: rgba(255, 255, 255, 0.2);
             border-radius: 50%;
        }
        
        .card-btn {
            display: inline-block;
            padding: 10px 25px;
            background: rgb(23, 108, 101);
            color: white;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            margin-top: 10px;
            align-self: center;
            width: auto;
            min-width: 160px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .card-btn:hover {
            background: rgb(18, 88, 82);
            color: rgb(255, 255, 255);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
        }
        
        /* Vehicle-specific feature styles */
        .vehicle-card .feature {
            font-size: 0.85rem;
            padding: 4px 10px;
            margin-bottom: 6px;
        }
        
        .vehicle-card .feature span {
            white-space: normal;
            word-break: break-word;
            font-size: 0.85rem;
        }
        
        .vehicle-card .back-content {
            padding: 12px;
        }
        
        /* 3D effect enhancement */
        .flip-card-front, .flip-card-back {
            box-shadow: 
                0 15px 35px rgba(0, 0, 0, 0.1),
                0 3px 10px rgba(0, 0, 0, 0.07),
                0 1px 3px rgba(0, 0, 0, 0.05);
        }
        
        /* Responsive styles for flip cards - Keep consistent behavior across all devices */
        @media (max-width: 991px) {
            .flip-card {
                height: 380px; /* Keep same height as desktop */
            }
            
            .vehicle-card.flip-card,
            .package-card.flip-card {
                height: 400px; /* Keep same height as desktop */
            }
            
            /* Make sure card content remains visible and properly sized */
            .front-content {
                 padding: 8px;
                 z-index: 3;
                 background: linear-gradient(transparent, rgba(0, 0, 0, 0.5) 20%, rgba(0, 0, 0, 0.8) 60%, rgba(0, 0, 0, 0.95));
                 bottom: 10px; /* Move up by 10px from bottom */
            }
            
            .front-content h3 {
                 font-size: 1.4rem;
                 margin-bottom: 6px;
                 letter-spacing: normal;
                 text-shadow: 2px 2px 3px rgba(0, 0, 0, 0.7);
            }
            
            .flip-hint {
                 font-size: 0.8rem;
                 padding: 3px 10px;
                 border-radius: 15px;
                 margin-top: 2px;
            }
            
            /* Ensure flip cards work properly on all browsers */
            .flip-card-inner {
                transform-style: preserve-3d !important;
            }
            
            .flip-card-front, .flip-card-back {
                backface-visibility: hidden !important;
                position: absolute !important;
            }
            
            .card-btn {
                min-width: 160px;
            }
        }
        
        @media (max-width: 768px) {
            .flip-card {
                height: 370px; /* Increased by 50px from reduced height */
            }
            
            .vehicle-card.flip-card,
            .package-card.flip-card {
                height: 390px; /* Increased by 50px from reduced height */
            }
            
            /* Make sure features are properly spaced */
            .card-features {
                margin-bottom: 10px;
            }
            
            .feature {
                margin-bottom: 5px;
                padding: 4px 8px;
            }
            
            .feature-icon {
                width: 22px;
                height: 22px;
            }
            
            .back-content {
                padding: 12px;
            }
        }
        
        /* Make sure small devices maintain the same heights */
        @media (max-width: 576px) {
            .flip-card {
                height: 350px; /* Adjusted height for better display */
            }
            
            .vehicle-card.flip-card,
            .package-card.flip-card {
                height: 370px; /* Adjusted height for better display */
                margin-bottom: 0;
            }
            
                          /* Move only the name and button up, not the background */
            .front-content {
                bottom: 0; /* Reset to default */
            }
            
            .front-content h3 {
                transform: translateY(-10px); /* Move only the title up */
            }
            
            .flip-hint {
                transform: translateY(-30px); /* Move the button up even more */
                bottom: 10px; /* Move button up by creating negative top margin */
            }
            
            /* Adjust font sizes and content for smaller cards */
            .back-content {
                padding: 10px;
            }
            
            .back-content h3 {
                font-size: 1.5rem;
                margin-bottom: 8px;
                font-weight: 700;
            }
            
            .back-content p {
                font-size: 0.8rem;
                max-height: 40px;
                overflow-y: auto;
                margin-bottom: 5px;
            }
            
            .card-features {
                margin-bottom: 5px;
            }
            
            .feature {
                margin-bottom: 3px;
                padding: 2px 6px;
                font-size: 0.75rem;
                text-align: left;
                align-items: center;
            }
            
            .feature-icon {
                width: 18px;
                height: 18px;
            }
            
            .card-btn {
                padding: 5px 15px;
                font-size: 0.8rem;
                min-width: 140px;
                margin-top: 5px;
            }
        }
        
        /* Fix for very small screens */
        @media (max-width: 400px) {
            /* Adjusted heights for very small devices */
            .vehicle-card.flip-card,
            .package-card.flip-card {
                height: 350px; /* Adjusted height for better display */
                width: 90%;
                max-width: 320px;
            }
            
            /* Move only the name and button up more on very small screens */
            .front-content h3 {
                transform: translateY(-15px); /* Move only the title up more */
            }
            
            .flip-hint {
                transform: translateY(-35px); /* Move the button up significantly more */
                bottom: 10px; /* Move button up by creating negative top margin */
            }
            
            /* Additional adjustments for very small screens */
            .front-content h3 {
                 font-size: 1.25rem;
                 letter-spacing: normal;
                 font-weight: 700;
                 margin-bottom: 5px;
            }
            
            .flip-hint {
                 font-size: 0.75rem;
                 padding: 3px 8px;
                 border-radius: 14px;
            }
            
            .feature {
                padding: 2px 5px;
                font-size: 0.7rem;
            }
        }
        
        /* Special hover effects */
        @keyframes glow {
            0% {
                box-shadow: 0 0 5px rgba(23, 108, 101, 0.6);
            }
            50% {
                box-shadow: 0 0 20px rgba(23, 108, 101, 0.8), 0 0 30px rgba(101, 255, 193, 0.6);
            }
            100% {
                box-shadow: 0 0 5px rgba(23, 108, 101, 0.6);
            }
        }
        
        .flip-card:hover {
            animation: glow 2s infinite;
        }

        /* Original Packages Section Styles */
        
            /* Team Section Styles - Modern 3D Version */
    .team {
        padding: 8rem 2rem 10rem;
        background: var(--bg-color);
        position: relative;
        overflow: hidden;
        color: var(--text-color);
    }
    
    /* Background animation removed */
    
    .section-title {
        position: relative;
        z-index: 1;
    }
    
    .team-container {
    position: relative;
    z-index: 1;
    max-width: 1200px;
    margin: 2rem auto 0;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 2.5rem;
    perspective: 1000px;
    -webkit-perspective: 1000px;
    justify-content: center;
}
    
    .team-member {
    position: relative;
    height: 380px;
    border-radius: 20px;
    overflow: visible;
    margin-bottom: 10px;
    perspective: 1000px;
}

.member-inner {
    position: relative;
    width: 100%;
    height: 100%;
    text-align: center;
    transition: transform 0.8s;
    transform-style: preserve-3d;
}

.team-member.flipped .member-inner {
    transform: rotateY(180deg);
}
    
    .member-front,
.member-back {
    position: absolute;
    width: 100%;
    height: 100%;
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    border-radius: 20px;
    overflow: hidden;
}
    
.member-front {
    background: var(--card-bg);
    box-shadow: 0 5px 15px rgb(0, 0, 0);
    z-index: 1;
}
    
.member-back {
    background: linear-gradient(145deg, rgba(23, 108, 101, 0.9), rgba(101, 255, 193, 0.9));
    transform: rotateY(180deg);
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: center;
    padding: 1.5rem;
    padding-top: 1rem;
    color: white;
    text-align: center;
    box-shadow: 0 5px 15px rgb(0, 0, 0);
    overflow-y: scroll;
    -webkit-overflow-scrolling: touch;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
}
    
    .member-image {
        position: absolute;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    
    .member-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        transition: transform 0.5s ease;
    }
    
    .member-front::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 150px;
        background: linear-gradient(to top, 
            var(--card-bg) 30%, 
            rgba(var(--card-bg-rgb, 255, 255, 255), 0.9) 60%,
            rgba(var(--card-bg-rgb, 255, 255, 255), 0.1) 100%);
    }
    
    .member-info-front {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 1rem 1.5rem 1.5rem;
    text-align: center;
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
}
    
    .member-info-front h3 {
        margin: 0;
        font-size: 1.3rem;
        color: #000000;
        font-weight: 700;
        margin-bottom: 0.4rem;
    }
    
    .card-flip-hint {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(23, 108, 101, 0.9);
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.9rem;
    opacity: 0.95;
    transition: opacity 0.3s, transform 0.3s;
    z-index: 100;
    animation: pulse 2s infinite;
    box-shadow: 0 2px 5px rgba(0,0,0,0.3);
}
    
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(23, 108, 101, 0.7);
        }
        70% {
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(23, 108, 101, 0);
        }
        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(23, 108, 101, 0);
        }
    }
    
    .team-member.touch-flip .card-flip-hint {
    opacity: 0;
}
    
    .position-front {
    display: inline-block;
    color: var(--text-color);
    font-weight: 600;
    font-size: 0.85rem;
    padding: 4px 12px;
    background-color: rgba(23, 108, 101, 0.1);
    border-radius: 16px;
    margin-bottom: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.flip-card-btn {
    background: rgb(23, 108, 101);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 10px 20px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-left: auto;
    margin-right: auto;
    min-width: 130px;
    position: relative;
    z-index: 100;
}

.flip-card-btn i {
    transition: transform 0.3s ease;
}

.flip-card-btn:hover {
    background: rgb(19, 90, 84);
    transform: translateY(-2px);
    box-shadow: 0 5px 12px rgba(0, 0, 0, 0.3);
}

.flip-card-btn:hover i {
    transform: translateX(3px);
}

.back-btn {
    background: rgba(255, 255, 255, 0.25);
    color: white;
    margin-top: 15px;
    order: 999; /* Ensure it appears at the bottom */
}

.back-btn:hover {
    background: rgba(255, 255, 255, 0.4);
}

.back-btn i {
    margin-right: 5px;
    margin-left: 0;
    order: -1;
}

.back-btn:hover i {
    transform: translateX(-3px);
}
    
    .member-bio {
        margin-bottom: 1rem;
        font-size: 0.85rem;
        line-height: 1.4;
        max-height: 110px;
        overflow-y: scroll;
        -webkit-overflow-scrolling: touch; /* Enable smooth scrolling on iOS */
        padding-right: 5px;
        padding-left: 5px;
        position: relative;
        z-index: 10;
        width: 100%;
        touch-action: pan-y;
    }
    
    .member-bio::-webkit-scrollbar {
        width: 5px;
    }
    
    .member-bio::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    
    .member-bio::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
    }
    
    .member-bio::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }
    
    .member-social {
        position: relative;
        display: flex;
        justify-content: center;
        margin-top: 15px;
        gap: 10px;
        width: 100%;
    }
    
    .member-social:before {
        content: '';
        position: absolute;
        left: 15%;
        right: 15%;
        height: 2px;
        top: -15px;
        background: linear-gradient(to right, rgba(255, 255, 255, 0), rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0));
    }
    
    .member-social a {
        --size: 32px;
        position: relative;
        width: var(--size);
        height: var(--size);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        font-size: 0.9rem;
        overflow: visible;
        transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: 1;
    }
    
    .member-social a:before,
    .member-social a:after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 8px;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        z-index: -1;
    }
    
    .member-social a:before {
        background: var(--icon-bg, linear-gradient(45deg, #333, #555));
        opacity: 0.85;
        transform-origin: center bottom;
        box-shadow: 
            0 4px 8px rgba(0, 0, 0, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.08);
    }
    
    .member-social a:after {
        content: '';
        background: radial-gradient(circle at 50% 30%, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 60%);
        opacity: 0;
    }
    
    .member-social a:hover {
        transform: translateY(-4px) scale(1.1);
        z-index: 5;
    }
    
    .member-social a:hover:before {
        transform: perspective(400px) rotateX(5deg) scale(1.05);
        box-shadow: 
            0 8px 16px rgba(0, 0, 0, 0.3),
            0 0 0 1px rgba(255, 255, 255, 0.12);
    }
    
    .member-social a:hover:after {
        opacity: 1;
    }
    
    .member-social a i {
        position: relative;
        z-index: 2;
        filter: drop-shadow(0 2px 3px rgba(0, 0, 0, 0.2));
        transition: transform 0.3s ease;
    }
    
    .member-social a:hover i {
        transform: scale(1.2);
        filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.3));
    }
    
    /* Platform-specific styling */
    .member-social a.facebook {
        --icon-bg: linear-gradient(145deg, #1877f2, #0d65d9);
    }
    
    .member-social a.twitter {
        --icon-bg: linear-gradient(145deg, #25d366, #128c7e);
    }
    
    .member-social a.instagram {
        --icon-bg: linear-gradient(145deg, #833ab4, #fd1d1d, #fcb045);
    }
    
    .member-social a.linkedin {
        --icon-bg: linear-gradient(145deg, #0077b5, #00669c);
    }
    
    /* Interactive hover effect that affects neighbors */
    .member-social:hover a:not(:hover) {
        transform: scale(0.92);
        opacity: 0.7;
    }
    
    /* Staggered entrance animation */
    @keyframes slideUp {
        0% { transform: translateY(10px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
    
    .member-social a {
        animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }
    
    .member-social a:nth-child(1) { animation-delay: 0.1s; }
    .member-social a:nth-child(2) { animation-delay: 0.2s; }
    .member-social a:nth-child(3) { animation-delay: 0.3s; }
    .member-social a:nth-child(4) { animation-delay: 0.4s; }
    
    /* Shine effect */
    .member-social a:before {
        background-size: 200% 200%;
    }
    
    .member-social a:hover:before {
        animation: shine 1.5s linear infinite;
    }
    
    @keyframes shine {
        0% { background-position: 0% 0%; }
        25% { background-position: 100% 0%; }
        50% { background-position: 100% 100%; }
        75% { background-position: 0% 100%; }
        100% { background-position: 0% 0%; }
    }
    
    /* For mobile devices without hover capability */
    /* Specific styles for touch devices */
html.touch-device .team-member {
    perspective: 1000px;
}

html.touch-device .member-inner {
    transform-style: preserve-3d;
}

html.touch-device .member-front,
html.touch-device .member-back {
    backface-visibility: hidden;
    position: absolute;
}

html.touch-device .member-back {
    transform: rotateY(180deg);
    -webkit-transform: rotateY(180deg);
}

@media (hover: none) and (max-width: 768px) {
    /* Mobile-specific styles */
    .team-member {
        height: auto;
    }
    
    .member-inner {
        transform-style: preserve-3d;
    }
    
    .flip-card-btn {
        padding: 12px 20px;
        font-size: 1rem;
        margin-top: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
    }
    
    .back-btn {
        margin-top: 20px;
    }
}
    
    /* Responsive design for team section */
    @media (max-width: 992px) {
        .team-container {
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 2rem;
            padding: 0 1rem;
        }
        
        .team-member {
            height: 320px;
        }
    }
    
    @media (max-width: 768px) {
        .team {
            padding: 5rem 1rem 7rem;
        }
        
        .team-member {
            height: 300px;
        }
        
        .team-container {
            gap: 2rem;
        }
        
        .member-front::after {
            height: 130px;
        }
    }
    
    @media (max-width: 576px) {
    .team-container {
        grid-template-columns: 1fr;
        max-width: 260px;
        margin: 0 auto;
    }
    
    .team-member {
        height: 320px;
    }
    
    .member-back {
        padding: 1rem;
        overflow-y: scroll;
        -webkit-overflow-scrolling: touch;
        touch-action: pan-y;
        transform-style: flat;
    }
    
    /* Fix for Safari and iOS */
    .team-member.flipped .member-back {
        z-index: 10;
    }
    
    .member-info-front h3 {
        font-size: 1.1rem;
    }
    
    .position-front {
        font-size: 0.75rem;
        padding: 3px 10px;
    }
    
    .member-front::after {
        height: 120px;
    }
    
    .member-bio {
        max-height: 80px; /* Reduced height to ensure it fits */
        margin-bottom: 0.8rem;
        overflow-y: scroll !important;
        -webkit-overflow-scrolling: touch !important;
        touch-action: pan-y !important;
        padding: 0 5px;
        -webkit-transform: translateZ(0); /* Force hardware acceleration */
        transform: translateZ(0);
    }
    
    .card-flip-hint {
        top: 10px;
        right: 10px;
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
        opacity: 0.95;
        background-color: rgba(23, 108, 101, 0.95);
        box-shadow: 0 3px 10px rgba(0,0,0,0.5);
    }
    
    .flip-card-btn {
        padding: 8px 16px;
        font-size: 0.9rem;
        margin-top: 10px;
    }
    
    .back-btn {
        margin-top: 10px;
    }
}

    /* Review Form Section */
    .review-form-section {
        background: #f8f9fa;
        padding: 40px 15px;
        border-top: 1px solid #eee;
    }
    
    .review-form-container {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        padding: 25px 20px;
        max-width: 800px;
        margin: 0 auto;
        width: 100%;
    }
    
    .review-form-container h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #343a40;
        font-size: clamp(1.5rem, 4vw, 2rem);
    }
    
    .review-form {
        display: grid;
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    @media (min-width: 768px) {
        .review-form-section {
            padding: 60px 30px;
        }
        
        .review-form-container {
            padding: 30px;
        }
        
        .review-form {
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
    }
    
    .form-group {
        margin-bottom: 20px;
        position: relative;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text-color);
        font-size: 0.95rem;
    }
    
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        background-color: rgba(var(--card-bg-rgb), 0.8);
        color: var(--text-color);
        font-size: 16px;
        transition: all 0.3s ease;
    }
    
    .dark-mode .form-group input,
    .dark-mode .form-group select,
    .dark-mode .form-group textarea {
        border-color: rgba(255, 255, 255, 0.1);
        background-color: rgba(45, 45, 45, 0.8);
    }
    
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(var(--primary-color), 0.1);
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    /* Rating star styles */
    .rating-select {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 15px 10px;
        border-radius: 8px;
        background: linear-gradient(145deg, #f0f0f0, #ffffff);
        box-shadow: 5px 5px 10px #d9d9d9, -5px -5px 10px #ffffff;
        margin-top: 10px;
        position: relative;
        overflow: hidden;
    }
    
    @media (min-width: 768px) {
        .rating-select {
            padding: 20px;
        }
    }
    
    .stars-container {
        display: flex;
        justify-content: space-between;
        width: 100%;
        position: relative;
        margin-bottom: 5px;
    }
    
    .rating-scale {
        display: flex;
        justify-content: space-between;
        width: 100%;
        padding: 0 2%;
        margin-bottom: 10px;
    }
    
    .scale-point {
        font-size: 10px;
        color: #777;
        width: 20%;
        text-align: center;
    }
    
    @media (min-width: 768px) {
        .scale-point {
            font-size: 12px;
        }
    }
    
    .rating-slider {
        width: 100%;
        height: 8px;
        border-radius: 4px;
        background: #e0e0e0;
        margin-bottom: 5px;
        position: relative;
    }
    
    .rating-progress {
        position: absolute;
        height: 100%;
        width: 0%;
        left: 0;
        top: 0;
        background: linear-gradient(90deg, #4776E6 0%, #8E54E9 100%);
        border-radius: 4px;
        transition: width 0.3s ease;
        animation: progressPulse 2s infinite;
    }
    
    .rating-star {
        cursor: pointer;
        position: relative;
        font-size: 26px;
        width: 20%;
        text-align: center;
        z-index: 2;
        transition: all 0.3s ease;
        color: transparent;
        -webkit-background-clip: text;
        background-clip: text;
        background-image: linear-gradient(45deg, #ccc, #ddd);
    }
    
    @media (min-width: 768px) {
        .rating-star {
            font-size: 32px;
        }
    }
    
    /* Active star styles */
    .rating-star.fas, 
    .rating-star.selected {
        background-image: linear-gradient(45deg, #4776E6, #8E54E9);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    
    /* Rating value tooltip */
    .rating-value {
        position: absolute;
        top: -20px;
        left: 0;
        background: linear-gradient(90deg, #4776E6 0%, #8E54E9 100%);
        color: white;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        transform: translateX(0%);
        opacity: 0;
        transition: all 0.3s ease;
        z-index: 3;
    }
    
    @media (min-width: 768px) {
        .rating-value {
            top: -25px;
            padding: 2px 8px;
            font-size: 14px;
        }
    }
    
    .rating-select:hover .rating-value {
        opacity: 1;
    }
    
    /* Hover effects for stars */
    .rating-star:hover {
        transform: translateY(-5px);
    }
    
    /* Visual indicator for rating selection */
    .rating-select:after {
        content: "Select your rating";
        position: absolute;
        bottom: 0;
        right: 0;
        padding: 5px 8px;
        font-size: 10px;
        color: #777;
        opacity: 0.8;
    }
    
    @media (min-width: 768px) {
        .rating-select:after {
            padding: 5px 10px;
            font-size: 12px;
        }
    }
    
    /* Animation for the progress bar */
    @keyframes progressPulse {
        0% { opacity: 0.7; }
        50% { opacity: 1; }
        100% { opacity: 0.7; }
    }
    
    /* File upload styles */
    .review-upload {
        position: relative;
    }
    
    .review-upload input[type="file"] {
        background-color: transparent;
        padding: 10px 0;
        border: none;
    }
    
    .upload-preview {
        margin-top: 10px;
        max-width: 150px;
        max-height: 150px;
        border-radius: 10px;
        overflow: hidden;
        display: none;
    }
    
    @media (min-width: 768px) {
        .upload-preview {
            max-width: 200px;
            max-height: 200px;
        }
    }
    
    .upload-preview img {
        width: 100%;
        height: auto;
        object-fit: cover;
    }

            /* Welcome Popup Styles - Fullscreen Immersive Design */
        .welcome-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 9999;
            overflow: hidden;
        }
        
        .welcome-content {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            color: white;
            animation: fadeIn 1.2s ease-out;
        }
        
        .welcome-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            animation: zoomSlow 30s ease-in-out infinite alternate;
        }
        
        .welcome-bg img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            opacity: 1;
        }
        
        .welcome-bg::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(0deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0.8) 50%, rgba(0,0,0,0.7) 100%);
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
        
        @keyframes zoomSlow {
            from {
                transform: scale(1);
            }
            to {
                transform: scale(1.1);
            }
        }
    
    @keyframes popupFadeIn {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    
            .welcome-info {
            max-width: 800px;
            padding: 0 40px 60px;
            margin: 0 auto;
            width: 100%;
            position: relative;
            z-index: 5;
        }
        
        .welcome-location {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            opacity: 0;
            animation: slideUp 0.8s ease-out 0.6s forwards;
        }
        
        .welcome-location i {
            width: 24px;
            height: 24px;
            margin-right: 10px;
            color: rgb(101, 255, 193);
            font-size: 18px;
        }
        
        .welcome-location span {
            text-transform: uppercase;
            letter-spacing: 3px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .welcome-content h2 {
            color: white;
            font-size: 3.5rem;
            font-weight: 800;
            margin: 0 0 20px;
            line-height: 1.1;
            max-width: 80%;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            opacity: 0;
            animation: slideUp 0.8s ease-out 0.8s forwards;
        }
        
        .welcome-content p {
            color: rgba(255, 255, 255, 0.9);
            margin: 0 0 40px;
            font-size: 1.2rem;
            line-height: 1.6;
            max-width: 600px;
            opacity: 0;
            animation: slideUp 0.8s ease-out 1s forwards;
        }
        
        .welcome-highlight {
            color: rgb(101, 255, 193);
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
                .welcome-btn-container {
            display: flex;
            opacity: 0;
            animation: slideUp 0.8s ease-out 1.2s forwards;
        }
        
        .welcome-btn {
            background-color: rgb(101, 255, 193);
            color: rgb(20, 20, 20);
            border: none;
            padding: 16px 36px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-right: 20px;
            box-shadow: 0 5px 20px rgba(101, 255, 193, 0.4);
        }
        
        .welcome-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(101, 255, 193, 0.5);
            background-color: rgb(120, 255, 200);
        }
        
        .welcome-skip {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.7);
            font-size: 1rem;
            cursor: pointer;
            padding: 16px 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .welcome-skip:hover {
            color: rgb(255, 255, 255);
        }
        
        .welcome-skip i {
            margin-left: 8px;
            font-size: 0.9rem;
        }
        
                .close-welcome {
            position: absolute;
            top: 30px;
            right: 30px;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            cursor: pointer;
            color: white;
            transition: all 0.3s ease;
            z-index: 10;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            opacity: 0;
            animation: fadeIn 0.5s ease-out 1.5s forwards;
        }
        
        .close-welcome:hover {
            background: rgba(0, 0, 0, 0.5);
            border-color: rgba(255, 255, 255, 0.4);
            transform: rotate(90deg);
        }
        
        .welcome-badge {
            position: absolute;
            top: 30px;
            left: 40px;
            background-color: transparent;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            z-index: 10;
            opacity: 0;
            animation: fadeIn 0.5s ease-out 1.5s forwards;
        }
        
        .welcome-logo-img {
            height: 60px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }
        
        /* Media Queries for Welcome Popup */
        @media (max-width: 992px) {
            .welcome-info {
                padding: 0 30px 50px;
            }
            
            .welcome-content h2 {
                font-size: 3rem;
                max-width: 90%;
            }
            
            .welcome-badge {
                top: 20px;
                left: 20px;
                padding: 6px 12px;
            }
            
            .welcome-logo-img {
                height: 50px;
            }
            
            .close-welcome {
                top: 20px;
                right: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .welcome-info {
                padding: 0 25px 40px;
            }
            
            .welcome-content h2 {
                font-size: 2.5rem;
                max-width: 100%;
            }
            
            .welcome-content p {
                font-size: 1.1rem;
                margin-bottom: 30px;
                max-width: 100%;
            }
            
            .welcome-btn-container {
                flex-direction: column;
                width: 100%;
            }
            
            .welcome-btn {
                margin-right: 0;
                margin-bottom: 15px;
                width: 100%;
                text-align: center;
            }
            
            .welcome-skip {
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .welcome-info {
                padding: 0 20px 30px;
            }
            
            .welcome-content h2 {
                font-size: 2rem;
                margin-bottom: 15px;
            }
            
            .welcome-content p {
                font-size: 1rem;
                margin-bottom: 25px;
                line-height: 1.5;
            }
            
            .welcome-location {
                margin-bottom: 15px;
            }
            
            .welcome-location span {
                letter-spacing: 2px;
                font-size: 0.8rem;
            }
            
            .welcome-btn {
                padding: 14px 20px;
                font-size: 0.9rem;
            }
            
            .welcome-badge {
                padding: 5px 10px;
                top: 15px;
                left: 15px;
            }
            
            .welcome-logo-img {
                height: 45px;
            }
            
            .close-welcome {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
        }

        /* Card perspective and 3D effect */
.flip-card {
    perspective: 1000px;
}

.flip-card-inner {
    transform-style: preserve-3d;
    position: relative;
}

/* For all browsers */
.flip-card-back {
    backface-visibility: hidden;
}

/* Ensure consistent behavior across all devices */
@media (max-width: 991px) {
    .flip-card {
        perspective: 1000px !important;
    }
    
    .flip-card-inner {
        transform-style: preserve-3d !important;
    }
    
    .flip-card-front, .flip-card-back {
        backface-visibility: hidden !important;
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
    }
    
    .flip-card-front {
        z-index: 2 !important;
        transform: rotateY(0deg) !important;
    }
    
    .flip-card-back {
        transform: rotateY(180deg) !important;
    }
}

        /* Special hover effects */
        @keyframes glow {
            0% {
                box-shadow: 0 0 5px rgba(23, 108, 101, 0.6);
            }
            50% {
                box-shadow: 0 0 20px rgba(23, 108, 101, 0.8), 0 0 30px rgba(101, 255, 193, 0.6);
            }
            100% {
                box-shadow: 0 0 5px rgba(23, 108, 101, 0.6);
            }
        }
        
        .flip-card:hover {
            animation: glow 2s infinite;
        }

        /* Global fix for 3D transforms across all browsers and devices */
html.ios-device .flip-card,
html.ios-device .video-item {
    -webkit-perspective: 1000px;
    perspective: 1000px;
}

html.ios-device .flip-card-inner,
html.ios-device .video-item-inner {
    -webkit-transform-style: preserve-3d;
    transform-style: preserve-3d;
}

html.ios-device .flip-card-front,
html.ios-device .flip-card-back,
html.ios-device .video-front,
html.ios-device .video-back {
    -webkit-backface-visibility: hidden;
    backface-visibility: hidden;
    position: absolute;
}

html.ios-device .flip-card-front,
html.ios-device .video-front {
    z-index: 2;
    -webkit-transform: rotateY(0deg);
    transform: rotateY(0deg);
}

html.ios-device .flip-card-back,
html.ios-device .video-back {
    -webkit-transform: rotateY(180deg);
    transform: rotateY(180deg);
}

html.ios-device .flip-card:hover .flip-card-inner,
html.ios-device .video-item:hover .video-item-inner {
    -webkit-transform: rotateY(180deg);
    transform: rotateY(180deg);
}

/* Device detection script */
document.addEventListener('DOMContentLoaded', function() {
    // Detect iOS devices
    const iOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    // Detect Android or other touch devices
    const isTouch = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
    
    // Check for 3D transform support
    function supports3DTransforms() {
        var el = document.createElement('div'),
            transforms = {
                'webkitTransform':'-webkit-transform',
                'transform':'transform'
            };
        
        document.body.appendChild(el);
        
        for (var t in transforms) {
            if (el.style[t] !== undefined) {
                el.style[t] = 'translate3d(1px,1px,1px)';
                var has3d = window.getComputedStyle(el).getPropertyValue(transforms[t]);
                if (has3d && has3d !== 'none') {
                    document.body.removeChild(el);
                    return true;
                }
            }
        }
        
        document.body.removeChild(el);
        return false;
    }
    
    // Apply appropriate classes
    if (iOS) {
        document.documentElement.classList.add('ios-device');
    }
    
    if (isTouch) {
        document.documentElement.classList.add('touch-device');
    }
    
    if (!supports3DTransforms()) {
        document.documentElement.classList.add('no-preserve3d');
    }
    
    // Simple flip card functionality - fixed version
    document.addEventListener('DOMContentLoaded', function() {
        // Wait for DOM to be fully loaded
        setTimeout(function() {
            const teamMembers = document.querySelectorAll('.team-member');
            console.log('Team members found:', teamMembers.length);
            
            teamMembers.forEach(function(member, index) {
                console.log('Setting up team member:', index);
                
                // Get the front and back buttons
                const frontButton = member.querySelector('.member-front .flip-card-btn');
                const backButton = member.querySelector('.member-back .flip-card-btn');
                
                if (frontButton) {
                    console.log('Found front button for member:', index);
                    
                    // Remove any existing event listeners
                    const newFrontButton = frontButton.cloneNode(true);
                    frontButton.parentNode.replaceChild(newFrontButton, frontButton);
                    
                    // Add click event listener with explicit function
                    newFrontButton.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Front button clicked for member:', index);
                        member.classList.add('flipped');
                    };
                    
                    // Add specific handling for bio scrolling
                    const memberBio = member.querySelector('.member-bio');
                    if (memberBio) {
                        // Make bio scrollable
                        memberBio.style.webkitOverflowScrolling = 'touch';
                        memberBio.style.overflowY = 'scroll';
                        memberBio.style.touchAction = 'pan-y';
                        
                        // Prevent propagation of touch events
                        memberBio.addEventListener('touchmove', function(e) {
                            e.stopPropagation();
                        }, { passive: true });
                        
                        // Prevent default on parent to allow scrolling
                        memberBio.addEventListener('scroll', function(e) {
                            e.stopPropagation();
                        }, { passive: true });
                    }
                }
                
                if (backButton) {
                    console.log('Found back button for member:', index);
                    
                    // Remove any existing event listeners
                    const newBackButton = backButton.cloneNode(true);
                    backButton.parentNode.replaceChild(newBackButton, backButton);
                    
                    // Add click event listener with explicit function
                    newBackButton.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Back button clicked for member:', index);
                        member.classList.remove('flipped');
                    };
                    
                    // Allow scrolling on the back side
                    const memberBack = member.querySelector('.member-back');
                    if (memberBack) {
                        // Force scrollable styles
                        memberBack.style.webkitOverflowScrolling = 'touch';
                        memberBack.style.overflowY = 'scroll';
                        memberBack.style.touchAction = 'pan-y';
                        
                        // Prevent touch events from bubbling up
                        memberBack.addEventListener('touchmove', function(e) {
                            e.stopPropagation();
                        }, { passive: true });
                        
                        // Handle scroll events
                        memberBack.addEventListener('scroll', function(e) {
                            e.stopPropagation();
                        }, { passive: true });
                        
                        // Ensure z-index is set when flipped
                        memberBack.style.zIndex = '10';
                    }
                }
            });
        }, 500); // Small delay to ensure everything is loaded
    });
    
    // Force 3D acceleration for all devices
    const allFlipCards = document.querySelectorAll('.flip-card, .video-item, .team-member');
    allFlipCards.forEach(card => {
        card.style.transform = 'translateZ(0)';
        card.style.webkitTransform = 'translateZ(0)';
        card.style.backfaceVisibility = 'hidden';
        card.style.webkitBackfaceVisibility = 'hidden';
    });
});

/* Special hover effects */

        /* Screen reader only class for accessibility */
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        /* Fieldset styling for rating section */
        fieldset {
            border: none;
            padding: 0;
            margin: 0;
        }

        legend {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--text-color);
            font-size: 1rem;
        }
    </style>
</head>
<body>
    <!-- Welcome Popup -->
    <div class="welcome-popup" id="welcomePopup">
        <div class="welcome-content">
            <div class="welcome-bg">
                <img src="images/welcome.png" alt="Welcome to Sri Lanka">
            </div>
            
            <div class="welcome-badge">
                <img src="images/logo-w.png" alt="Adventure Travels Logo" class="welcome-logo-img">
            </div>
            
            <button class="close-welcome" id="closeWelcome">
                <i class="fas fa-times"></i>
            </button>
            
            <div class="welcome-info">
                <div class="welcome-location">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>SRI LANKA</span>
                </div>
                
                <h2>Discover Paradise <span class="welcome-highlight">Island</span></h2>
                <p>Immerse yourself in the jewel of the Indian Ocean with its pristine beaches, ancient ruins, lush highlands and vibrant culture. Let us guide you to unforgettable adventures in this tropical paradise.</p>
                
                <div class="welcome-btn-container">
                    <button class="welcome-btn" id="continueBtn">Begin Your Journey</button>
                    <button class="welcome-skip" id="skipBtn">Skip Intro <i class="fas fa-long-arrow-alt-right"></i></button>
                </div>
            </div>
        </div>
    </div>
    
    <header class="header">
        <a href="#" class="logo">
            <img src="images/logo-5.PNG" alt="Adventure Travel Logo">
        </a >

        <div class="sri-lanka-clock">
            <img src="images/sl-flag.jpg" alt="Sri Lankan Flag" class="sl-flag">
            <div id="sl-time"></div>
        </div>

        <div class="menu-toggle">☰</div>

        <nav class="navbar">
            <a href="#home">Home</a>
            <a href="#packages">Packages</a>
            <a href="#vehicle-hire">Vehicle Hire</a>
            <a href="#destinations">Destinations</a>
            <a href="#videos">Videos</a>
            <a href="#review">Reviews</a>
            <a href="contact_us.php">Contact Us</a>
            <a href="about_us/about_us.php">About Us</a>
            <?php if ($is_logged_in): ?>
                <div class="user-dropdown">
                    <div class="profile-btn">
                        <?php 
                        // Get first letters of names for initials
                        $initials = '';
                        $name_parts = explode(' ', $user_name);
                        foreach ($name_parts as $part) {
                            if (!empty($part)) {
                                $initials .= strtoupper(substr($part, 0, 1));
                                if (strlen($initials) >= 2) break;
                            }
                        }
                        
                        // Get first name for display
                        $first_name = !empty($name_parts[0]) ? $name_parts[0] : $user_name;
                        ?>
                        <div class="avatar"><?php echo $initials; ?></div>
                        <div class="user-name"><?php echo htmlspecialchars($first_name); ?></div>
                        <div class="dropdown-icon"><i class="fas fa-chevron-down"></i></div>
                    </div>
                    <div class="profile-dropdown">
                        <div class="profile-header">
                            <div class="profile-avatar">
                                <?php echo $initials; ?>
                            </div>
                            <h4><?php echo htmlspecialchars($user_name); ?></h4>
                            <p>@<?php echo htmlspecialchars($username); ?></p>
                        </div>
                        <div class="menu-section">
                            <a href="profile.php" class="menu-item">
                                <span class="icon">👤</span>
                                My Profile
                            </a>
                            <a href="settings.php" class="menu-item">
                                <span class="icon">⚙️</span>
                                Settings
                            </a>
                            <a href="?logout=1" class="menu-item logout">
                                <span class="icon">🚪</span>
                                Log Out
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="login-btn">Login</a>
            <?php endif; ?>
        </nav>
    </header>

    <!-- Theme toggle button with improved toggle switch design -->
    <button class="theme-toggle" id="theme-toggle" aria-label="Toggle dark mode">
        <div class="toggle-icons">
            <i class="fas fa-sun"></i>
            <i class="fas fa-moon"></i>
        </div>
        <div class="toggle-handle"></div>
    </button>

    <section class="home" id="home">
        <div class="slide active"></div>
        <div class="slide"></div>
        <div class="slide"></div>
        
        <div class="home-content">
            <div class="content-box active" data-slide="1">
                <span>Never Stop</span>
                <h1>Exploring</h1>
                <p>"Dream big and chase your passions. Life is a journey filled with opportunities waiting to be explored."</p>
                <a href="#packages" class="btn">Get Started</a>
                <div class="social-links">
                    <a href="https://wa.me/+94715380080" class="social-link whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/share/1FpJmkvUn8/?mibextid=wwXIfr" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/adventuretravelsrilanka?igsh=dncyeXJyYjRqNDRq&utm_source=qr" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                    <a href="mailto:adventuretravelsrilanka@gmail.com" class="social-link email"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
            
            <div class="content-box" data-slide="2">
                <span>Make Tour</span>
                <h1>Amazing</h1>
                <p>"Make your tour amazing by embracing spontaneity and connecting with locals. Plan enough to stay organized but leave room for surprises!"</p>
                <a href="#packages" class="btn">Get Started</a>
                <div class="social-links">
                    <a href="https://wa.me/+94715380080" class="social-link whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/share/1FpJmkvUn8/?mibextid=wwXIfr" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/adventuretravelsrilanka?igsh=dncyeXJyYjRqNDRq&utm_source=qr" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                    <a href="mailto:adventuretravelsrilanka@gmail.com" class="social-link email"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
            
            <div class="content-box" data-slide="3">
                <span>Explore the</span>
                <h1>New World</h1>
                <p>"Explore the new world with an open heart and curious mind. Every journey is a chance to discover unseen landscapes."</p>               
                <a href="#packages" class="btn">Get Started</a> 
                <div class="social-links">
                    <a href="https://wa.me/+94715380080" class="social-link whatsapp"><i class="fab fa-whatsapp"></i></a>
                    <a href="https://www.facebook.com/share/1FpJmkvUn8/?mibextid=wwXIfr" class="social-link facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/adventuretravelsrilanka?igsh=dncyeXJyYjRqNDRq&utm_source=qr" class="social-link instagram"><i class="fab fa-instagram"></i></a>
                    <a href="mailto:adventuretravelsrilanka@gmail.com" class="social-link email"><i class="fas fa-envelope"></i></a>
                </div>
            </div>
        </div>
        
        <!-- Slider Navigation Dots -->
        <div class="slider-dots">
            <span class="slider-dot active" data-slide="1"></span>
            <span class="slider-dot" data-slide="2"></span>
            <span class="slider-dot" data-slide="3"></span>
        </div>
        
    </section>

    <!-- Stats Counter Section -->
    <section class="stats-counter">
        <h2 class="section-title"><em style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-size: 1.1em; font-weight: bold;">Our</em> <span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: bold;">Achievements</span></h2>
        <div class="stats-container">
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-counter">
                    <span class="stat-counter-value">1K</span><span class="modern-plus"></span>
                </div>
                <div class="stat-title">Happy Clients</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-route"></i></div>
                <div class="stat-counter">
                    <span class="stat-counter-value">150</span><span class="modern-plus"></span>
                </div>
                <div class="stat-title">Completed Tours</div>
            </div>
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-map-marked-alt"></i></div>
                <div class="stat-counter">
                    <span class="stat-counter-value">20</span><span class="modern-plus"></span>
                </div>
                <div class="stat-title">Destinations</div>
            </div>           
            <div class="stat-item">
                <div class="stat-icon"><i class="fas fa-car-side"></i></div>
                <div class="stat-counter">
                    <span class="stat-counter-value">5</span><span class="modern-plus"></span>
                </div>
                <div class="stat-title">Vehicles</div>
            </div>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages" id="packages">
        <h2 class="section-title"><em style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-size: 1.1em; font-weight: bold;">Our</em> <span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: bold;">Packages</span></h2>
        
        <!-- Desktop version - flex layout -->
        <div class="packages-container">
            <!-- Tour Packages -->
            <div class="package-card flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        <div class="ribbon-wrapper">
                            <div class="ribbon">Popular</div>
                            <div class="ribbon-corner-left"></div>
                            <div class="ribbon-corner-right"></div>
                        </div>
                        <div class="front-image">
                    <img src="images/tourpackage2.png" alt="Tour Package">
                </div>
                        <div class="front-content">
                            <h3>Tour Packages</h3>
                            <span class="flip-hint">Flip for more</span>
                        </div>
                    </div>
                                        <div class="flip-card-back">
                        <div class="flip-back-btn"><i class="fas fa-times"></i></div>
                        <div class="back-content">
                            <h3>Tour Packages</h3>
                            <p>Make your dream holiday come true with Adventure Travel Sri Lanka tour packages.</p>
                            <div class="card-features">
                                <div class="feature">
                                    <i class="feature-icon">🏞️</i>
                                    <span>Multiple Destinations</span>
                                </div>
                                <div class="feature">
                                    <i class="feature-icon">🏨</i>
                                    <span>Premium Accommodations</span>
                                </div>
                                <div class="feature">
                                    <i class="feature-icon">🚐</i>
                                    <span>Private Transportation</span>
                                </div>
                            </div>
                            <a href="tour_packages/tour_packages.php" class="card-btn">View Packages</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- One Day Tour Packages -->
            <div class="package-card flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        <div class="front-image">
                    <img src="images/oneday2.png" alt="One Day Tour Package">
                </div>
                        <div class="front-content">
                            <h3>One Day Tour Packages</h3>
                            <span class="flip-hint">Flip for more</span>
                        </div>
                    </div>
                    <div class="flip-card-back">
                        <div class="flip-back-btn"><i class="fas fa-times"></i></div>
                        <div class="back-content">
                            <h3>One Day Tour Packages</h3>
                            <p>Discover Sri Lanka in a day with our variety of one-day tour packages.</p>
                            <div class="card-features">
                                <div class="feature">
                                    <i class="feature-icon">🕒</i>
                                    <span>Time-Efficient Itineraries</span>
                                </div>
                                <div class="feature">
                                    <i class="feature-icon">🧭</i>
                                    <span>Expert Local Guides</span>
                                </div>
                                <div class="feature">
                                    <i class="feature-icon">🍽️</i>
                                    <span>Meals Included</span>
                                </div>
                            </div>
                            <a href="one_day_tour_packages/one_day_tour.php" class="card-btn">View Packages</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Special Tour Packages -->
            <div class="package-card flip-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        <div class="front-image">
                    <img src="images/specialtour2.png" alt="Special Tour Package">
                </div>
                        <div class="front-content">
                            <h3>Special Tour Packages</h3>
                            <span class="flip-hint">Flip for more</span>
                        </div>
                    </div>
                    <div class="flip-card-back">
                        <div class="flip-back-btn"><i class="fas fa-times"></i></div>
                        <div class="back-content">
                            <h3>Special Tour Packages</h3>
                            <p>Golden beaches, ancient temples, and lush tea plantations await you.</p>
                            <div class="card-features">
                                <div class="feature">
                                    <i class="feature-icon">🌟</i>
                                    <span>Unique Experiences</span>
                                </div>
                                <div class="feature">
                                    <i class="feature-icon">🎭</i>
                                    <span>Cultural Immersion</span>
                                </div>
                                <div class="feature">
                                    <i class="feature-icon">📷</i>
                                    <span>Photographic Opportunities</span>
                                </div>
                            </div>
                            <a href="special_tour_packages/special_tour.php" class="card-btn">View Packages</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mobile version - Swiper slider -->
        <div class="packages-swiper">
            <div class="swiper packagesSwiper">
                <div class="swiper-wrapper">
                    <!-- Tour Packages -->
                    <div class="swiper-slide">
                        <div class="package-card flip-card">
                            <div class="flip-card-inner">
                                <div class="flip-card-front">
                                    <div class="ribbon-wrapper">
                                        <div class="ribbon">Popular</div>
                                        <div class="ribbon-corner-left"></div>
                                        <div class="ribbon-corner-right"></div>
                                    </div>
                                    <div class="front-image">
                                        <img src="images/tourpackage2.png" alt="Tour Package">
                                    </div>
                                    <div class="front-content">
                                        <h3>Tour Packages</h3>
                                        <span class="flip-hint">Flip for more</span>
                                    </div>
                                </div>
                                <div class="flip-card-back">
                                    <div class="flip-back-btn"><i class="fas fa-times"></i></div>
                                    <div class="back-content">
                                        <h3>Tour Packages</h3>
                                        <p>Make your dream holiday come true with Adventure Travel Sri Lanka tour packages.</p>
                                        <div class="card-features">
                                            <div class="feature">
                                                <i class="feature-icon">🏞️</i>
                                                <span>Multiple Destinations</span>
                                            </div>
                                            <div class="feature">
                                                <i class="feature-icon">🏨</i>
                                                <span>Premium Accommodations</span>
                                            </div>
                                            <div class="feature">
                                                <i class="feature-icon">🚐</i>
                                                <span>Private Transportation</span>
                                            </div>
                                        </div>
                                        <a href="tour_packages/tour_packages.php" class="card-btn">View Packages</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- One Day Tour Packages -->
                    <div class="swiper-slide">
                        <div class="package-card flip-card">
                            <div class="flip-card-inner">
                                <div class="flip-card-front">
                                    <div class="front-image">
                                        <img src="images/oneday2.png" alt="One Day Tour Package">
                                    </div>
                                    <div class="front-content">
                                        <h3>One Day Tour Packages</h3>
                                        <span class="flip-hint">Flip for more</span>
                                    </div>
                                </div>
                                <div class="flip-card-back">
                                    <div class="flip-back-btn"><i class="fas fa-times"></i></div>
                                    <div class="back-content">
                                        <h3>One Day Tour Packages</h3>
                                        <p>Discover Sri Lanka in a day with our variety of one-day tour packages.</p>
                                        <div class="card-features">
                                            <div class="feature">
                                                <i class="feature-icon">🕒</i>
                                                <span>Time-Efficient Itineraries</span>
                                            </div>
                                            <div class="feature">
                                                <i class="feature-icon">🧭</i>
                                                <span>Expert Local Guides</span>
                                            </div>
                                            <div class="feature">
                                                <i class="feature-icon">🍽️</i>
                                                <span>Meals Included</span>
                                            </div>
                                        </div>
                                        <a href="one_day_tour_packages/one_day_tour.php" class="card-btn">View Packages</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Special Tour Packages -->
                    <div class="swiper-slide">
                        <div class="package-card flip-card">
                            <div class="flip-card-inner">
                                <div class="flip-card-front">
                                    <div class="front-image">
                                        <img src="images/specialtour2.png" alt="Special Tour Package">
                                    </div>
                                    <div class="front-content">
                                        <h3>Special Tour Packages</h3>
                                        <span class="flip-hint">Flip for more</span>
                                    </div>
                                </div>
                                <div class="flip-card-back">
                                    <div class="flip-back-btn"><i class="fas fa-times"></i></div>
                                    <div class="back-content">
                                        <h3>Special Tour Packages</h3>
                                        <p>Golden beaches, ancient temples, and lush tea plantations await you.</p>
                                        <div class="card-features">
                                            <div class="feature">
                                                <i class="feature-icon">🌟</i>
                                                <span>Unique Experiences</span>
                                            </div>
                                            <div class="feature">
                                                <i class="feature-icon">🎭</i>
                                                <span>Cultural Immersion</span>
                                            </div>
                                            <div class="feature">
                                                <i class="feature-icon">📷</i>
                                                <span>Photographic Opportunities</span>
                                            </div>
                                        </div>
                                        <a href="special_tour_packages/special_tour.php" class="card-btn">View Packages</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Vehicle Hire Section -->
    <section class="vehicle-hire" id="vehicle-hire">
        <h2 class="section-title"><em style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-size: 1.1em; font-weight: bold;">Vehicle</em> <span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: bold;">Hire</span></h2>
        
        <!-- Desktop version - flex layout -->
        <div class="vehicles-container">
            <?php if (!empty($vehicles)): ?>
                <?php foreach ($vehicles as $vehicle): ?>
                    <div class="vehicle-card flip-card">
                        <div class="flip-card-inner">
                            <div class="flip-card-front">
                                <div class="front-image">
                            <img src="images/<?php echo htmlspecialchars($vehicle['image']); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>">
                        </div>
                                <div class="front-content">
                                    <h3><?php echo htmlspecialchars($vehicle['name']); ?></h3>
                                    <span class="flip-hint">Flip for details</span>
                                </div>
                            </div>
                            <div class="flip-card-back">
                                <div class="flip-back-btn"><i class="fas fa-times"></i></div>
                                <div class="back-content">
                            <h3><?php echo htmlspecialchars($vehicle['name']); ?></h3>
                            <div class="card-features">
                                <div class="feature">
                                    <i class="feature-icon">🚘</i>
                                    <span><?php echo htmlspecialchars($vehicle['type']); ?></span>
                                </div>
                                <div class="feature">
                                    <i class="feature-icon">👨‍👩‍👧‍👦</i>
                                    <span>Maximum Capacity: <?php echo $vehicle['capacity']; ?> persons</span>
                                </div>
                                <div class="feature">
                                    <i class="feature-icon">💰</i>
                                    <span>$<?php echo number_format($vehicle['price_per_day'], 2); ?> per day (150km included) </span>
                                </div>
                            </div>
                            <a href="vehicles/vehicle_details.php?id=<?php echo $vehicle['vehicle_id']; ?>" class="card-btn">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">Sorry, no vehicles are available at the moment. Please check back later or contact us for alternatives.</div>
            <?php endif; ?>
        </div>
        
        <!-- Mobile version - Swiper slider -->
        <div class="vehicles-swiper">
            <div class="swiper vehiclesSwiper">
                <div class="swiper-wrapper">
                    <?php if (!empty($vehicles)): ?>
                        <?php foreach ($vehicles as $vehicle): ?>
                            <div class="swiper-slide">
                                <div class="vehicle-card flip-card">
                                    <div class="flip-card-inner">
                                        <div class="flip-card-front">
                                            <div class="front-image">
                                                <img src="images/<?php echo htmlspecialchars($vehicle['image']); ?>" alt="<?php echo htmlspecialchars($vehicle['name']); ?>">
                                            </div>
                                            <div class="front-content">
                                                <h3><?php echo htmlspecialchars($vehicle['name']); ?></h3>
                                                <span class="flip-hint">Flip for details</span>
                                            </div>
                                        </div>
                                        <div class="flip-card-back">
                                            <div class="flip-back-btn"><i class="fas fa-times"></i></div>
                                            <div class="back-content">
                                                <h3><?php echo htmlspecialchars($vehicle['name']); ?></h3>
                                                <div class="card-features">
                                                    <div class="feature">
                                                        <i class="feature-icon">🚘</i>
                                                        <span><?php echo htmlspecialchars($vehicle['type']); ?></span>
                                                    </div>
                                                    <div class="feature">
                                                        <i class="feature-icon">👨‍👩‍👧‍👦</i>
                                                        <span>Maximum Capacity: <?php echo $vehicle['capacity']; ?> persons</span>
                                                    </div>
                                                    <div class="feature">
                                                        <i class="feature-icon">💰</i>
                                                        <span>$<?php echo number_format($vehicle['price_per_day'], 2); ?> per day (150km included) </span>
                                                    </div>
                                                </div>
                                                <a href="vehicles/vehicle_details.php?id=<?php echo $vehicle['vehicle_id']; ?>" class="card-btn">View Details</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide">
                            <div class="alert alert-info text-center">Sorry, no vehicles are available at the moment. Please check back later or contact us for alternatives.</div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
        </div>
    </section>

    <!-- Destinations Section -->
    <section class="destinations" id="destinations">
        <h2 class="section-title"><em style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-size: 1.1em; font-weight: bold;">Popular</em> <span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: bold;">Destinations</span></h2>
        
        <div class="destinations-container">
            <!-- Anuradhapura -->
            <div class="destination-card">
                <img src="destinations/destination-1.jpg" alt="Anuradhapura">
                <div class="destination-content">
                    <h3>Anuradhapura</h3>
                    <p>Ancient city with sacred Buddhist sites and ruins dating back over 2,000 years.</p>
                    <a href="destinations/destination_detail.php?id=28" class="destination-btn">Explore</a>
                </div>
            </div>
            
            <!-- Colombo -->
            <div class="destination-card">
                <img src="destinations/destination-2.jpg" alt="Colombo">
                <div class="destination-content">
                    <h3>Colombo</h3>
                    <p>The vibrant capital city with colonial buildings, museums, and bustling markets.</p>
                    <a href="destinations/destination_detail.php?id=22" class="destination-btn">Explore</a>
                </div>
            </div>
            
            <!-- Galle -->
            <div class="destination-card">
                <img src="destinations/destination-3.jpg" alt="Galle Fort">
                <div class="destination-content">
                    <h3>Galle</h3>
                    <p>Historic fort with Dutch colonial architecture, boutiques, and ocean views.</p>
                    <a href="destinations/destination_detail.php?id=30" class="destination-btn">Explore</a>
                </div>
            </div>
            
            <!-- Hatton -->
            <div class="destination-card">
                <img src="destinations/destination-4.png" alt="Hatton">
                <div class="destination-content">
                    <h3>Hatton</h3>
                    <p>Scenic hill country with lush tea plantations and spectacular mountain views.</p>
                    <a href="destinations/destination_detail.php?id=48" class="destination-btn">Explore</a>
                </div>
            </div>
            
            <!-- Horton Plains -->
            <div class="destination-card">
                <img src="destinations/destination-5.jpg" alt="Horton Plains">
                <div class="destination-content">
                    <h3>Horton Plains</h3>
                    <p>National park with unique cloud forests, wildlife, and the famous World's End viewpoint.</p>
                    <a href="destinations/destination_detail.php?id=49" class="destination-btn">Explore</a>
                </div>
            </div>
            
            <!-- Kandy -->
            <div class="destination-card">
                <img src="destinations/destination-6.jpg" alt="Kandy">
                <div class="destination-content">
                    <h3>Kandy</h3>
                    <p>Cultural capital and home to the Temple of the Sacred Tooth Relic.</p>
                    <a href="destinations/destination_detail.php?id=23" class="destination-btn">Explore</a>
                </div>
            </div>
            
            <!-- Hikkaduwa -->
            <div class="destination-card">
                <img src="destinations/destination-7.jpg" alt="Hikkaduwa">
                <div class="destination-content">
                    <h3>Hikkaduwa</h3>
                    <p>Beach resort town known for coral reefs, surfing, and vibrant nightlife.</p>
                    <a href="destinations/destination_detail.php?id=38" class="destination-btn">Explore</a>
                </div>
            </div>

            <!-- Kithulgala -->
            <div class="destination-card">
                <img src="destinations/destination-8.jpg" alt="Kithulgala">
                <div class="destination-content">
                    <h3>Kithulgala</h3>
                    <p>Adventure hub ideal for white water rafting, jungle treks, and bird watching.</p>
                    <a href="destinations/destination_detail.php?id=47" class="destination-btn">Explore</a>
                </div>
            </div>
        </div>
        
        <div class="explore-more-container">
            <a href="destinations/destinations.php" class="explore-more-btn">Explore More Destinations</a>
        </div>
    </section>

    <!-- Reviews Section -->
    <?php
    // Fetch videos from database
    $featured_video_query = "SELECT * FROM videos WHERE featured = 1 ORDER BY display_order ASC, created_at DESC LIMIT 1";
    $featured_video_result = mysqli_query($conn, $featured_video_query);
    $featured_video = mysqli_fetch_assoc($featured_video_result);
    
    // Fetch non-featured videos
    $videos_query = "SELECT * FROM videos WHERE featured = 0 ORDER BY display_order ASC, created_at DESC LIMIT 4";
    $videos_result = mysqli_query($conn, $videos_query);
    $videos = [];
    if ($videos_result) {
        while ($video = mysqli_fetch_assoc($videos_result)) {
            $videos[] = $video;
        }
    }
?>
<!-- Video Section -->
<section class="video-section" id="videos">
    <h2 class="section-title"><em style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-size: 1.1em; font-weight: bold;">Our</em> <span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: bold;">Videos</span></h2>
    <div class="video-container">
        <?php if ($featured_video): ?>
        <div class="featured-video">
            <div class="video-wrapper">
                <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($featured_video['video_url']); ?>" title="<?php echo htmlspecialchars($featured_video['title']); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
            <div class="video-info">
                <h3><?php echo htmlspecialchars($featured_video['title']); ?></h3>
                <p><?php echo htmlspecialchars($featured_video['description']); ?></p>
            </div>
        </div>
        <?php else: ?>
        <div class="featured-video">
            <div class="video-wrapper">
                <div style="height: 100%; display: flex; align-items: center; justify-content: center; background-color: #f5f5f5; color: #666;">
                    <div style="text-align: center;">
                        <i class="fas fa-film" style="font-size: 48px; margin-bottom: 15px;"></i>
                        <p>No featured video available</p>
                    </div>
                </div>
            </div>
            <div class="video-info">
                <h3>Explore Our Adventures</h3>
                <p>Add a featured video in the admin panel to showcase your best adventure travel experiences.</p>
            </div>
        </div>
        <?php endif; ?>
        <div class="video-grid">
            <?php if (count($videos) > 0): ?>
                <?php foreach ($videos as $video): ?>
                <div class="video-item">
                    <div class="video-item-inner">
                        <div class="video-front">
                            <div class="video-thumbnail">
                        <?php if (!empty($video['thumbnail'])): ?>
                            <img src="images/<?php echo htmlspecialchars($video['thumbnail']); ?>" alt="<?php echo htmlspecialchars($video['title']); ?>">
                        <?php else: ?>
                            <div style="height: 100%; background-color: #222; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-video" style="font-size: 32px; color: #fff;"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                            <div class="video-front-content">
                    <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                                <span class="video-flip-hint">Flip for video</span>
                            </div>
                        </div>
                        <div class="video-back" data-video-id="<?php echo htmlspecialchars($video['video_url']); ?>">
                            <h4><?php echo htmlspecialchars($video['title']); ?></h4>
                            <div class="play-button"><i class="fas fa-play"></i></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="video-item">
                    <div class="video-item-inner">
                        <div class="video-front">
                            <div class="video-thumbnail">
                                <div style="height: 100%; background-color: #222; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-film" style="font-size: 32px; color: #fff;"></i>
                        </div>
                    </div>
                            <div class="video-front-content">
                    <h4>Wildlife Explorations</h4>
                                <span class="video-flip-hint">Flip for video</span>
                </div>
                        </div>
                        <div class="video-back" data-video-id="">
                            <h4>Wildlife Explorations</h4>
                        <div class="play-button"><i class="fas fa-play"></i></div>
                    </div>
                    </div>
                </div>
                <div class="video-item">
                    <div class="video-item-inner">
                        <div class="video-front">
                            <div class="video-thumbnail">
                                <div style="height: 100%; background-color: #222; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-film" style="font-size: 32px; color: #fff;"></i>
                        </div>
                            </div>
                            <div class="video-front-content">
                                <h4>Beach Paradise</h4>
                                <span class="video-flip-hint">Flip for video</span>
                            </div>
                        </div>
                        <div class="video-back" data-video-id="">
                            <h4>Beach Paradise</h4>
                        <div class="play-button"><i class="fas fa-play"></i></div>
                    </div>
                    </div>
                </div>
                <div class="video-item">
                    <div class="video-item-inner">
                        <div class="video-front">
                            <div class="video-thumbnail">
                                <div style="height: 100%; background-color: #222; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-film" style="font-size: 32px; color: #fff;"></i>
                        </div>
                            </div>
                            <div class="video-front-content">
                                <h4>Mountain Trekking</h4>
                                <span class="video-flip-hint">Flip for video</span>
                            </div>
                        </div>
                        <div class="video-back" data-video-id="">
                            <h4>Mountain Trekking</h4>
                        <div class="play-button"><i class="fas fa-play"></i></div>
                    </div>
                    </div>
                </div>
                <div class="video-item">
                    <div class="video-item-inner">
                        <div class="video-front">
                            <div class="video-thumbnail">
                                <div style="height: 100%; background-color: #222; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-film" style="font-size: 32px; color: #fff;"></i>
                                </div>
                            </div>
                            <div class="video-front-content">
                    <h4>Cultural Heritage</h4>
                                <span class="video-flip-hint">Flip for video</span>
                            </div>
                        </div>
                        <div class="video-back" data-video-id="">
                            <h4>Cultural Heritage</h4>
                            <div class="play-button"><i class="fas fa-play"></i></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Video Modal -->
    <div class="video-modal" id="video-modal">
        <div class="modal-content">
            <span class="close-video">&times;</span>
            <div class="modal-video-container">
                <iframe id="modal-video-frame" src="" title="Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</section>

<section class="reviews" id="review">
        <h2 class="section-title"><em style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-size: 1.1em; font-weight: bold;">Customer</em> <span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: bold;">Reviews</span></h2>
        
        <!-- Tripadvisor Reviews Widget -->
        <div class="tripadvisor-container">
            <h3 class="reviews-subtitle">Tripadvisor Reviews</h3>
            <script src="https://static.elfsight.com/platform/platform.js" async></script>
            <div class="elfsight-app-afdb59ed-6945-45ea-908d-df2311efbab9" data-elfsight-app-lazy></div>
        </div>
        
        <div class="reviews-container">
            <h3 class="reviews-subtitle">Our Customer Reviews</h3>
            <div class="reviews-slider">
                <?php if (count($approved_reviews) > 0): ?>
                    <?php foreach ($approved_reviews as $index => $review): ?>
                        <div class="review-card <?php echo $index === 0 ? 'active' : ''; ?>">
                            <div class="user-info">
                                <div class="user-img">
                                    <?php if (!empty($review['photo'])): ?>
                                        <img src="images/<?php echo htmlspecialchars($review['photo']); ?>" alt="User Review Photo">
                                    <?php else: ?>
                                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($review['name']); ?>&background=random" alt="User">
                                    <?php endif; ?>
                                </div>
                                <div class="user-details">
                                    <h3><?php echo htmlspecialchars($review['name']); ?></h3>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <?php if ($i <= $review['rating']): ?>
                                                <i class="star">★</i>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                            <p class="review-text">"<?php echo htmlspecialchars($review['review_text']); ?>"</p>
                            <div class="tour-type"><?php echo htmlspecialchars($review['tour_type']); ?></div>
                            <div class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default review if no approved reviews exist -->
                    <div class="review-card active">
                        <div class="user-info">
                            <div class="user-img">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="User">
                            </div>
                            <div class="user-details">
                                <h3>David Thompson</h3>
                                <div class="rating">
                                    <i class="star">★</i>
                                    <i class="star">★</i>
                                    <i class="star">★</i>
                                    <i class="star">★</i>
                                    <i class="star">★</i>
                                </div>
                            </div>
                        </div>
                        <p class="review-text">"Our tour to Kandy and the cultural triangle was exceptional! The guide was knowledgeable and the accommodations were perfect. Highly recommend Adventure Travel for anyone looking to explore Sri Lanka."</p>
                        <div class="tour-type">Cultural Tour Package</div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="slider-controls">
                <div class="prev-btn" onclick="prevReview()">❮</div>
                <div class="dots-container">
                    <?php 
                    $total_reviews = count($approved_reviews) > 0 ? count($approved_reviews) : 1;
                    for ($i = 0; $i < $total_reviews; $i++): 
                    ?>
                        <span class="dot <?php echo $i === 0 ? 'active' : ''; ?>" onclick="showReview(<?php echo $i; ?>)"></span>
                    <?php endfor; ?>
                </div>
                <div class="next-btn" onclick="nextReview()">❯</div>
            </div>
        </div>
        
                    <div class="review-cta">
                <p>Share your experience with us and help others plan their adventure</p>
                <a href="#" class="review-btn" id="open-review-modal">Write a Review</a>
            </div>
            
            <?php if (isset($_GET['success']) && $_GET['success'] == 'review_submitted'): ?>
                <div class="review-message success" style="margin-top: 20px; padding: 10px 20px; background-color: rgba(40, 167, 69, 0.1); border-left: 4px solid #28a745; border-radius: 4px;">
                    <p style="margin: 0; color: #28a745; font-weight: bold;">Thank you for your review! It has been submitted for approval.</p>
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="review-message error" style="margin-top: 20px; padding: 10px 20px; background-color: rgba(220, 53, 69, 0.1); border-left: 4px solid #dc3545; border-radius: 4px;">
                    <p style="margin: 0; color: #dc3545; font-weight: bold;">
                        <?php 
                            $error = $_GET['error'];
                            switch($error) {
                                case 'review_fields_required':
                                    echo 'Please fill in all required fields.';
                                    break;
                                case 'invalid_email':
                                    echo 'Please enter a valid email address.';
                                    break;
                                case 'invalid_rating':
                                    echo 'Please select a rating between 1 and 5 stars.';
                                    break;
                                case 'invalid_file_type':
                                    echo 'Please upload only JPEG, PNG, or GIF images.';
                                    break;
                                case 'review_submission_failed':
                                    echo 'There was an error submitting your review. Please try again later.';
                                    break;
                                default:
                                    echo 'An error occurred. Please try again.';
                            }
                        ?>
                    </p>
                </div>
            <?php endif; ?>
        
        <!-- Review Modal -->
        <div id="review-modal" class="review-modal">
            <div class="review-modal-content">
                <span class="close-review-modal">&times;</span>
                <h3>Write Your Review</h3>
                <p style="margin-bottom: 20px; color: #666; font-size: 0.9rem;">Fields marked with <span style="color: #dc3545;">*</span> are required</p>
                <form id="review-form" method="post" action="submit_review.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="review-name">Your Name <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="review-name" name="name" autocomplete="name" required>
                    </div>
                    <div class="form-group">
                        <label for="review-email">Email <span style="color: #dc3545;">*</span></label>
                        <input type="email" id="review-email" name="email" autocomplete="email" required>
                    </div>
                    <div class="form-group">
                        <label for="review-tour-type">Tour Type <span style="color: #dc3545;">*</span></label>
                        <select id="review-tour-type" name="tour_type" required>
                            <option value="">Select Tour Type</option>
                            <option value="Tour Package">Tour Package</option>
                            <option value="One Day Tour Package">One Day Tour Package</option>
                            <option value="Special Tour Package">Special Tour Package</option>
                            <option value="Vehicle Hire">Vehicle Hire</option>
                            <option value="Custom Experience">Custom Experience</option>
                            <option value="other">Other (Specify)</option>
                        </select>
                    </div>
                    
                    <div class="form-group" id="custom-tour-type-container" style="display: none;">
                        <label for="custom-tour-type">Specify Tour Type <span style="color: #dc3545;">*</span></label>
                        <input type="text" id="custom-tour-type" name="custom_tour_type" autocomplete="organization-title" placeholder="Enter your tour type">
                    </div>
                    <div class="form-group">
                        <fieldset>
                            <legend id="review-rating-label">Rating <span style="color: #dc3545;">*</span></legend>
                            <div id="rating-description" class="sr-only">Select a rating from 1 to 5 stars by clicking on the stars or using keyboard navigation</div>
                            <div class="rating-select">
                                <div class="rating-value">0</div>
                                <div class="stars-container" id="stars-rating-group" role="radiogroup" aria-labelledby="review-rating-label" aria-describedby="rating-description" tabindex="0">
                                <i class="rating-star far fa-star" data-rating="1" title="Poor" role="radio" aria-label="1 star - Poor" tabindex="0"></i>
                                <i class="rating-star far fa-star" data-rating="2" title="Fair" role="radio" aria-label="2 stars - Fair" tabindex="0"></i>
                                <i class="rating-star far fa-star" data-rating="3" title="Good" role="radio" aria-label="3 stars - Good" tabindex="0"></i>
                                <i class="rating-star far fa-star" data-rating="4" title="Very Good" role="radio" aria-label="4 stars - Very Good" tabindex="0"></i>
                                <i class="rating-star far fa-star" data-rating="5" title="Excellent" role="radio" aria-label="5 stars - Excellent" tabindex="0"></i>
                            </div>
                            <div class="rating-slider">
                                <div class="rating-progress"></div>
                            </div>
                            <div class="rating-scale">
                                <div class="scale-point">Poor</div>
                                <div class="scale-point">Fair</div>
                                <div class="scale-point">Good</div>
                                <div class="scale-point">Very Good</div>
                                <div class="scale-point">Excellent</div>
                            </div>
                            <input type="hidden" id="review-rating" name="rating" value="0" required>
                            <div id="rating-error" class="form-error" style="display: none; color: #dc3545; margin-top: 15px; font-size: 0.9rem; text-align: center;">Please select a rating by clicking the stars above</div>
                        </fieldset>
                    </div>
                    <div class="form-group">
                        <label for="review-text">Your Review <span style="color: #dc3545;">*</span></label>
                        <textarea id="review-text" name="review" autocomplete="off" rows="5" required></textarea>
                    </div>
                    <div class="form-group review-upload">
                        <label for="review-photo">Upload Your Photo</label>
                        <input type="file" id="review-photo" name="photo" accept="image/*">
                        <div class="upload-preview"></div>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="cancel-review">Cancel</button>
                        <button type="submit" class="submit-review">Submit Review</button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team" id="team">
        <h2 class="section-title"><em style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-size: 1.1em; font-weight: bold;">Our</em> <span style="font-family: 'Dancing Script', 'Segoe Script', 'Lucida Handwriting', 'Brush Script MT', cursive; font-style: italic; font-weight: bold;">Team</span></h2>
        
        <div class="team-container">
            <?php if (!empty($team_members)): ?>
                <?php foreach ($team_members as $member): ?>
                    <div class="team-member">
                        <div class="member-inner">
                            <div class="member-front">
                                <div class="member-image">
                                    <img src="images/<?php echo htmlspecialchars($member['image']); ?>" alt="<?php echo htmlspecialchars($member['name']); ?>">
                                </div>
                                <div class="member-info-front">
                                    <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                                    <p class="position-front"><?php echo htmlspecialchars($member['position']); ?></p>
                                    <button class="flip-card-btn" onclick="this.closest('.team-member').classList.add('flipped'); return false;">More Info <i class="fas fa-arrow-right"></i></button>
                                </div>
                            </div>
                            <div class="member-back">
                                <h3><?php echo htmlspecialchars($member['name']); ?></h3>
                                <p class="member-bio"><?php echo htmlspecialchars($member['bio'] ?? 'Experienced travel professional with a passion for creating unforgettable adventures. Expert in Sri Lanka tourism and committed to exceptional customer service.'); ?></p>
                                <div class="member-social">
                                    <?php if (!empty($member['facebook'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['facebook']); ?>" target="_blank" class="facebook" aria-label="Facebook Profile"><i class="fab fa-facebook-f"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['twitter'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['twitter']); ?>" target="_blank" class="twitter" aria-label="Twitter Profile"><i class="fab fa-whatsapp"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['instagram'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['instagram']); ?>" target="_blank" class="instagram" aria-label="Instagram Profile"><i class="fab fa-instagram"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['linkedin'])): ?>
                                        <a href="<?php echo htmlspecialchars($member['linkedin']); ?>" target="_blank" class="linkedin" aria-label="LinkedIn Profile"><i class="fab fa-linkedin-in"></i></a>
                                    <?php endif; ?>
                                </div>
                                <button class="flip-card-btn back-btn" onclick="this.closest('.team-member').classList.remove('flipped'); return false;">Back <i class="fas fa-arrow-left"></i></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                 <p>No team members found</p>    
            <?php endif; ?>
        </div>
    </section>

    <script>
        // Global variables for review functionality
        let reviewInterval;
        let currentReview = 0;
        let reviews = [];
        let reviewDots = [];
        let lastActivityTime = Date.now();
        let isTabVisible = true;

        // Function to auto-refresh reviews section every 5 minutes
        function setupReviewRefresh() {
            // Check for dynamic reviews every minute
            setInterval(() => {
                fetch('fetch_reviews.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the reviews HTML
                            document.querySelector('.reviews-slider').innerHTML = data.html;
                            document.querySelector('.dots-container').innerHTML = data.dots_html;
                            
                            // Re-initialize review functionality
                            reviews = document.querySelectorAll('.review-card');
                            reviewDots = document.querySelectorAll('.dots-container .dot');
                            
                            if (reviews.length > 0) {
                                // Reset current review
                                currentReview = 0;
                                
                                // Initialize the first review and set the slider height
                                showReview(0);
                                
                                // Reset auto-change interval
                                resetReviewInterval();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching reviews:', error);
                        // On error, ensure interval is still running properly
                        resetReviewInterval();
                    });
            }, 60000); // Check every minute
        }
        
        // Function to reset the review interval
        function resetReviewInterval() {
            // Clear any existing interval to prevent duplicates
            if (reviewInterval) {
                clearInterval(reviewInterval);
            }
            
            // Only start auto-rotation if tab is visible
            if (isTabVisible) {
                reviewInterval = setInterval(nextReview, 8000);
            }
        }
        
        // Function to handle tab visibility changes
        function handleVisibilityChange() {
            if (document.hidden) {
                // Tab is hidden, pause the interval
                isTabVisible = false;
                if (reviewInterval) {
                    clearInterval(reviewInterval);
                }
            } else {
                // Tab is visible again, restart the interval 
                isTabVisible = true;
                resetReviewInterval();
                
                // If it's been inactive for a while, reset the current review
                if (Date.now() - lastActivityTime > 30000) { // 30 seconds
                    if (reviews.length > 0) {
                        showReview(currentReview); // Refresh the current review
                    }
                }
                
                lastActivityTime = Date.now();
            }
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Get initial review elements
            reviews = document.querySelectorAll('.review-card');
            reviewDots = document.querySelectorAll('.dots-container .dot');
            
            // Add visibility change detection
            document.addEventListener('visibilitychange', handleVisibilityChange);
            
            // Add user interaction detection to reset the activity timer
            document.addEventListener('click', () => { lastActivityTime = Date.now(); });
            document.addEventListener('scroll', () => { lastActivityTime = Date.now(); });
            document.addEventListener('keydown', () => { lastActivityTime = Date.now(); });
            
            // Automatically check and reset the interval periodically
            setInterval(() => {
                if (isTabVisible && !reviewInterval) {
                    resetReviewInterval();
                }
            }, 30000); // Check every 30 seconds
            
            // Initialize review refresh
            setupReviewRefresh();
            
            // Menu toggle functionality
            const menuToggle = document.querySelector('.menu-toggle');
            const navbar = document.querySelector('.navbar');
            
            menuToggle.addEventListener('click', function() {
                navbar.classList.toggle('active');
            });
            
            // Close navigation when a nav link is clicked
            document.querySelectorAll('.navbar a').forEach(link => {
                link.addEventListener('click', () => {
                    navbar.classList.remove('active');
                });
            });
            
            // User dropdown menu functionality
            const userDropdown = document.querySelector('.user-dropdown');
            if (userDropdown) {
                // Handle dropdown toggle for both mobile and desktop
                const profileBtn = userDropdown.querySelector('.profile-btn');
                
                profileBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    userDropdown.classList.toggle('active');
                });
                
                // Close dropdown when clicking elsewhere
                document.addEventListener('click', function(e) {
                    if (!userDropdown.contains(e.target) && userDropdown.classList.contains('active')) {
                        userDropdown.classList.remove('active');
                    }
                });
                
                // Prevent dropdown toggle from closing navbar in mobile view
                userDropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }
            
            // Theme toggle functionality
            const themeToggle = document.getElementById('theme-toggle');
            
            // Check for saved theme preference or use device preference
            const savedTheme = localStorage.getItem('theme');
            const prefersDarkScheme = window.matchMedia('(prefers-color-scheme: dark)');
            
            if (savedTheme === 'dark' || (!savedTheme && prefersDarkScheme.matches)) {
                document.body.classList.add('dark-mode');
                
                // Ensure reviews display correctly in dark mode on page load
                setTimeout(() => {
                    if (reviews.length > 0) {
                        showReview(currentReview);
                    }
                }, 200);
            }
            
            // Listen for system color scheme changes
            prefersDarkScheme.addEventListener('change', (e) => {
                // Only change if user hasn't manually set a preference
                if (!localStorage.getItem('theme')) {
                    if (e.matches) {
                        document.body.classList.add('dark-mode');
                    } else {
                        document.body.classList.remove('dark-mode');
                    }
                    
                    // Update review display after system theme change
                    if (reviews.length > 0) {
                        setTimeout(() => {
                            showReview(currentReview);
                        }, 100);
                    }
                }
            });
            
            themeToggle.addEventListener('click', function() {
                document.body.classList.toggle('dark-mode');
                
                // Save preference to localStorage
                if (document.body.classList.contains('dark-mode')) {
                    localStorage.setItem('theme', 'dark');
                } else {
                    localStorage.setItem('theme', 'light');
                }

                // Refresh review section on theme change to fix any display issues
                if (reviews.length > 0) {
                    setTimeout(() => {
                        showReview(currentReview);
                    }, 50);
                }
            });
            
            // Hide navbar on scroll down, show on scroll up
            let lastScrollTop = 0;
            const header = document.querySelector('.header');
            const scrollThreshold = 100; // Minimum scroll before header hides
            
            window.addEventListener('scroll', function() {
                let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Don't hide menu when at the very top of the page
                if (scrollTop <= 10) {
                    header.classList.remove('hide');
                    return;
                }
                
                // Only trigger hide/show after passing threshold to avoid flickering
                if (Math.abs(lastScrollTop - scrollTop) <= scrollThreshold) return;
                
                // Hide when scrolling down, show when scrolling up
                if (scrollTop > lastScrollTop) {
                    // Scrolling down
                    header.classList.add('hide');
                } else {
                    // Scrolling up
                    header.classList.remove('hide');
                }
                
                lastScrollTop = scrollTop;
            });
        });
        
        // Image slider with content - Initialize after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            const contentBoxes = document.querySelectorAll('.content-box');
            const dots = document.querySelectorAll('.slider-dot');
            let currentSlide = 1;
            
            // Check if essential elements exist before proceeding
            if (slides.length === 0 || contentBoxes.length === 0) {
                console.warn('Essential slider elements not found:', {
                    slides: slides.length,
                    contentBoxes: contentBoxes.length
                });
                return;
            }
            
            // Check if dots are missing (non-critical warning)
            if (dots.length === 0) {
                console.warn('Slider dots not found - slider will work without navigation dots');
            }
        
        function showSlide(slideNumber) {
            // Ensure slideNumber is within bounds
            if (slideNumber < 1 || slideNumber > slides.length) {
                console.error("Invalid slideNumber:", slideNumber, "Available slides:", slides.length);
                return; // Exit the function if the slideNumber is invalid
            }
            
            // Hide all slides and content
            slides.forEach(slide => slide.classList.remove('active'));
            contentBoxes.forEach(content => content.classList.remove('active'));
            if (dots.length > 0) {
                dots.forEach(dot => dot.classList.remove('active'));
            }
            
            // Show current slide
            slides[slideNumber - 1].classList.add('active');
            contentBoxes[slideNumber - 1].classList.add('active');
            if (dots.length > 0) {
                dots[slideNumber - 1].classList.add('active');
            }
            currentSlide = slideNumber;
        }
        
        // Add click event to dots (only if dots exist)
        if (dots.length > 0) {
            dots.forEach(dot => {
                dot.addEventListener('click', () => {
                    const slideNumber = parseInt(dot.getAttribute('data-slide'));
                    showSlide(slideNumber);
                });
            });
        }
        
            // Auto slide change
            setInterval(() => {
                currentSlide = currentSlide < slides.length ? currentSlide + 1 : 1;
                showSlide(currentSlide);
            }, 5000);
        }); // End of DOMContentLoaded for slider
        
        // Stats Counter Animation
        function animateStatCounters() {
            const statItems = document.querySelectorAll('.stat-item');
            const statCounters = document.querySelectorAll('.stat-counter-value');
            const counterValues = [1000, 150, 20, 5]; // Final values for each counter
            let countersStarted = false;
            
            // Function to check if element is in viewport
            function isInViewport(element) {
                const rect = element.getBoundingClientRect();
                return (
                    rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
                    rect.bottom >= 0
                );
            }
            
            // Function to animate counters
            function startCounters() {
                if (countersStarted) return;
                countersStarted = true;
                
                statItems.forEach(item => item.classList.add('reveal'));
                
                statCounters.forEach((counter, index) => {
                    const finalValue = counterValues[index];
                    const duration = 3500; // 3.5 seconds - slower animation
                    const startTime = performance.now();
                    
                    function updateCounter(currentTime) {
                        const elapsedTime = currentTime - startTime;
                        const progress = Math.min(elapsedTime / duration, 1);
                        
                        // Easing function for smoother, more gradual animation
                        const easedProgress = progress === 1 ? 1 : progress < 0.5
                            ? 2 * progress * progress  // Slower at the beginning
                            : 1 - Math.pow(-2 * progress + 2, 2) / 2; // Slower at the end
                        
                        // Calculate current value with easing
                        const currentValue = Math.floor(easedProgress * finalValue);
                        // Format to show 1K for the first counter (Happy Clients) when it reaches 1000
                        counter.textContent = (index === 0 && currentValue === 1000) ? '1K' : currentValue;
                        
                        if (progress < 1) {
                            requestAnimationFrame(updateCounter);
                        }
                    }
                    
                    requestAnimationFrame(updateCounter);
                });
            }
            
            // Check on scroll if stats section is visible
            function checkStatsVisibility() {
                const statsSection = document.querySelector('.stats-counter');
                if (isInViewport(statsSection)) {
                    startCounters();
                    // Remove scroll listener once animation started
                    window.removeEventListener('scroll', checkStatsVisibility);
                }
            }
            
            // Initialize counter animation
            checkStatsVisibility();
            window.addEventListener('scroll', checkStatsVisibility);
        }
        
        // Initialize stats counter animation when DOM is loaded
        document.addEventListener('DOMContentLoaded', animateStatCounters);

        // Reviews slider functionality
        function showReview(index) {
            // Ensure index is within bounds
            if (index < 0 || index >= reviews.length || index >= reviewDots.length) {
                console.error("Invalid review index:", index, "Available reviews:", reviews.length);
                return; // Exit the function if the index is invalid
            }
            
            reviews.forEach(review => review.classList.remove('active'));
            reviewDots.forEach(dot => dot.classList.remove('active'));
            
            reviews[index].classList.add('active');
            reviewDots[index].classList.add('active');
            currentReview = index;
            lastActivityTime = Date.now(); // Update activity time when changing reviews
            
            // Adjust the slider height to match the active review card with proper buffer
            setTimeout(() => {
                const activeReview = reviews[index];
                const reviewsSlider = document.querySelector('.reviews-slider');
                const reviewHeight = activeReview.offsetHeight;
                
                // Calculate buffer based on screen size
                let buffer = 20;
                if (window.innerWidth <= 576) buffer = 30;
                if (window.innerWidth <= 375) buffer = 40;
                
                // Ensure minimum height for small content
                const minHeight = window.innerWidth <= 375 ? 450 : 
                                 window.innerWidth <= 576 ? 420 : 
                                 window.innerWidth <= 768 ? 380 : 350;
                                 
                const newHeight = Math.max(reviewHeight + buffer, minHeight);
                reviewsSlider.style.minHeight = newHeight + 'px';
                
                // Reset scroll position on text containers
                const reviewTexts = activeReview.querySelectorAll('.review-text');
                reviewTexts.forEach(text => {
                    text.scrollTop = 0;
                });
                
                // Ensure review text visibility by forcing a repaint
                const reviewText = activeReview.querySelector('.review-text');
                if (reviewText) {
                    // Force repaint to ensure visibility
                    reviewText.style.display = 'none';
                    reviewText.offsetHeight; // This line forces a repaint
                    reviewText.style.display = 'block';
                }
            }, 100); // Added delay to ensure proper rendering
        }
        
        function nextReview() {
            if (!reviews || reviews.length === 0) return; // Check if reviews exist
            currentReview = (currentReview + 1) % reviews.length;
            showReview(currentReview);
            // Add visual feedback for button click
            const nextBtn = document.querySelector('.next-btn');
            if (nextBtn) {
                nextBtn.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    nextBtn.style.transform = 'scale(1)';
                }, 150);
            }
        }
        
        function prevReview() {
            if (!reviews || reviews.length === 0) return; // Check if reviews exist
            currentReview = (currentReview - 1 + reviews.length) % reviews.length;
            showReview(currentReview);
            // Add visual feedback for button click
            const prevBtn = document.querySelector('.prev-btn');
            if (prevBtn) {
                prevBtn.style.transform = 'scale(0.9)';
                setTimeout(() => {
                    prevBtn.style.transform = 'scale(1)';
                }, 150);
            }
        }
        
        // Initialize reviews if they exist
        if (typeof reviews !== 'undefined' && reviews.length > 0) {
            showReview(0);
            resetReviewInterval(); // Initialize the interval
        }
        
        // Add resize handler for responsive adjustments
        window.addEventListener('resize', function() {
            if (typeof reviews !== 'undefined' && reviews.length > 0) {
                showReview(currentReview);
            }
        });
        
        // Pause auto rotation when hovering over reviews
        const reviewsContainer = document.querySelector('.reviews-container');
        if (reviewsContainer) {
            reviewsContainer.addEventListener('mouseenter', () => {
                if (reviewInterval) {
                    clearInterval(reviewInterval);
                }
            });
            
            reviewsContainer.addEventListener('mouseleave', () => {
                resetReviewInterval();
            });
        }
        
        // Add touch swipe support for reviews on mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        const reviewsSlider = document.querySelector('.reviews-slider');
        if (reviewsSlider) {
            reviewsSlider.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
                lastActivityTime = Date.now(); // Update activity time
            });
            
            reviewsSlider.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
                lastActivityTime = Date.now(); // Update activity time
            });
        }
        
        function handleSwipe() {
            // Detect left or right swipe
            if (touchEndX < touchStartX - 50) {
                // Swipe left - show next review
                nextReview();
            }
            if (touchEndX > touchStartX + 50) {
                // Swipe right - show previous review
                prevReview();
            }
        }
        
        // Video Section Functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Video modal elements
            const videoModal = document.getElementById('video-modal');
            const modalVideoFrame = document.getElementById('modal-video-frame');
            const closeVideoBtn = document.querySelector('.close-video');
            
            // Video back play buttons
            const videoBackButtons = document.querySelectorAll('.video-back');
            
            // Open video modal when clicking on the back side play button
            videoBackButtons.forEach(back => {
                back.addEventListener('click', function() {
                    const videoId = this.getAttribute('data-video-id');
                    if (videoId && videoId.trim() !== '') {
                        openVideoModal(videoId);
                    }
                });
            });
            
            // Function to open video modal
            function openVideoModal(videoId) {
                modalVideoFrame.src = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
                videoModal.style.display = 'block';
                document.body.style.overflow = 'hidden'; // Prevent scrolling
            }
            
            // Close video modal
            closeVideoBtn.addEventListener('click', closeVideoModal);
            
            // Close modal when clicking outside content
            videoModal.addEventListener('click', function(e) {
                if (e.target === videoModal) {
                    closeVideoModal();
                }
            });
            
            // Close video modal function
            function closeVideoModal() {
                videoModal.style.display = 'none';
                modalVideoFrame.src = ''; // Stop video playback
                document.body.style.overflow = ''; // Restore scrolling
            }
            
            // Close modal with ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && videoModal.style.display === 'block') {
                    closeVideoModal();
                }
            });
        });

        // Review Modal Functionality
        const reviewModal = document.getElementById('review-modal');
        const openReviewModalBtn = document.getElementById('open-review-modal');
        const closeReviewModalBtn = document.querySelector('.close-review-modal');
        const cancelReviewBtn = document.querySelector('.cancel-review');
        const reviewForm = document.getElementById('review-form');
        const ratingStars = document.querySelectorAll('.rating-star');
        const ratingInput = document.getElementById('review-rating');
        const reviewPhotoInput = document.getElementById('review-photo');
        const uploadPreview = document.querySelector('.upload-preview');
        
        // Open review modal
        openReviewModalBtn.addEventListener('click', function(e) {
            e.preventDefault();
            reviewModal.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
        
        // Handle custom tour type visibility
        const tourTypeSelect = document.getElementById('review-tour-type');
        const customTourTypeContainer = document.getElementById('custom-tour-type-container');
        const customTourTypeInput = document.getElementById('custom-tour-type');
        
        tourTypeSelect.addEventListener('change', function() {
            if (this.value === 'other') {
                customTourTypeContainer.style.display = 'block';
                customTourTypeInput.setAttribute('required', 'required');
            } else {
                customTourTypeContainer.style.display = 'none';
                customTourTypeInput.removeAttribute('required');
            }
        });
        
        // Close review modal
        function closeReviewModal() {
            reviewModal.classList.remove('active');
            document.body.style.overflow = '';
            setTimeout(() => {
                reviewForm.reset();
                resetRatingStars();
                uploadPreview.style.display = 'none';
                uploadPreview.innerHTML = '';
            }, 300);
        }
        
        closeReviewModalBtn.addEventListener('click', closeReviewModal);
        cancelReviewBtn.addEventListener('click', closeReviewModal);
        
        // Close modal when clicking outside of it
        reviewModal.addEventListener('click', function(e) {
            if (e.target === reviewModal) {
                closeReviewModal();
            }
        });
        
        // Modern star rating interaction with slider
        const ratingLabels = ['Poor', 'Fair', 'Good', 'Very Good', 'Excellent'];
        const ratingValue = document.querySelector('.rating-value');
        const ratingProgress = document.querySelector('.rating-progress');
        const ratingSelectDiv = document.querySelector('.rating-select');
        let ratingSelected = false;
        
        // Update progress bar and value tooltip position
        function updateProgressBar(rating) {
            const progressPercentage = (rating / 5) * 100;
            ratingProgress.style.width = `${progressPercentage}%`;
            
            // Position tooltip
            if (rating > 0) {
                ratingValue.textContent = rating;
                ratingValue.style.left = `${progressPercentage}%`;
                ratingValue.style.transform = 'translateX(-50%)';
                ratingValue.style.opacity = '1';
            } else {
                ratingValue.style.opacity = '0';
            }
        }
        
        ratingStars.forEach((star, index) => {
            // Mouse hover effect to preview rating
            star.addEventListener('mouseover', function() {
                const hoverRating = parseInt(this.dataset.rating);
                
                // Update tooltip and progress bar
                updateProgressBar(hoverRating);
                
                // Reset visual state first
                ratingStars.forEach(s => {
                    s.classList.remove('fas', 'hover');
                    s.classList.add('far');
                });
                
                // Fill stars up to hovered rating
                for (let i = 0; i < ratingStars.length; i++) {
                    if (i < hoverRating) {
                        ratingStars[i].classList.remove('far');
                        ratingStars[i].classList.add('fas', 'hover');
                    }
                }
            });
            
            // Mouse leave effect to restore selected rating
            star.addEventListener('mouseleave', function() {
                const selectedRating = parseInt(ratingInput.value);
                
                // Restore selected rating if any
                updateStarsDisplay(selectedRating);
                updateProgressBar(selectedRating);
            });
            
            // Click to select rating with nice transition
            star.addEventListener('click', function() {
                selectRating(this.dataset.rating);
            });
            
            // Keyboard navigation support
            star.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectRating(this.dataset.rating);
                } else if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextIndex = (index + 1) % ratingStars.length;
                    ratingStars[nextIndex].focus();
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevIndex = (index - 1 + ratingStars.length) % ratingStars.length;
                    ratingStars[prevIndex].focus();
                }
            });
        });
        
        // Function to handle rating selection
        function selectRating(rating) {
            const ratingValue = parseInt(rating);
            ratingInput.value = ratingValue;
            ratingSelected = true;
            
            // Visual feedback for selection
            const selectedStar = document.querySelector(`[data-rating="${rating}"]`);
            selectedStar.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                selectedStar.style.transform = 'translateY(-5px)';
            }, 200);
            
            // Hide any error message
            document.getElementById('rating-error').style.display = 'none';
            
            // Update visuals
            updateStarsDisplay(ratingValue);
            updateProgressBar(ratingValue);
            
            // Show selection confirmation
            ratingSelectDiv.classList.add('rating-selected');
            
            // Update ARIA attributes for accessibility
            ratingStars.forEach((star, index) => {
                star.setAttribute('aria-checked', index < ratingValue ? 'true' : 'false');
            });
        }
        
        // Add rating-selected class styles
        const style = document.createElement('style');
        style.textContent = `
            .rating-selected {
                box-shadow: 5px 5px 10px #d9d9d9, -5px -5px 10px #ffffff, 0 0 0 2px rgba(71, 118, 230, 0.2);
            }
            .rating-selected:after {
                content: "✓ Rating confirmed" !important;
                color: #4776E6 !important;
                font-weight: bold;
            }
            .rating-selected .rating-progress {
                animation: none !important;
                opacity: 1 !important;
            }
        `;
        document.head.appendChild(style);
        
        function resetRatingStars() {
            ratingStars.forEach(star => {
                star.classList.remove('fas', 'selected', 'hover');
                star.classList.add('far');
                star.setAttribute('aria-checked', 'false');
            });
            ratingInput.value = 0;
            ratingSelected = false;
            updateProgressBar(0);
            ratingSelectDiv.classList.remove('rating-selected');
        }
        
        function updateStarsDisplay(rating) {
            // Reset all stars first
            ratingStars.forEach(star => {
                star.classList.remove('fas', 'selected', 'hover');
                star.classList.add('far');
                star.setAttribute('aria-checked', 'false');
            });
            
            // Fill stars up to selected rating
            for (let i = 0; i < ratingStars.length; i++) {
                if (i < rating) {
                    ratingStars[i].classList.remove('far');
                    ratingStars[i].classList.add('fas', 'selected');
                    ratingStars[i].setAttribute('aria-checked', 'true');
                }
            }
        }
        
        // Handle image upload preview
        reviewPhotoInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    uploadPreview.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                    uploadPreview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            } else {
                uploadPreview.style.display = 'none';
                uploadPreview.innerHTML = '';
            }
        });
        
        // Form submission
        reviewForm.addEventListener('submit', function(e) {
            // Check if rating is selected
            if (ratingInput.value === '0') {
                e.preventDefault();
                // Show the rating error message instead of an alert
                document.getElementById('rating-error').style.display = 'block';
                // Highlight the rating section with a shake animation
                const ratingSelect = document.querySelector('.rating-select');
                ratingSelect.style.animation = 'none';
                setTimeout(() => {
                    ratingSelect.style.animation = 'shake 0.5s';
                }, 10);
                // Scroll to the rating section
                ratingSelect.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            } else {
                // Hide error message if rating is selected
                document.getElementById('rating-error').style.display = 'none';
            }
            
            // If using AJAX submission instead of form action, uncomment this
            /*
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('submit_review.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Thank you for your review!');
                    closeReviewModal();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
            */
        });

        // Add resize handler for responsive adjustments with debounce
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                if (reviews.length > 0) {
                    showReview(currentReview);
                }
            }, 100); // Debounce for better performance
        });
    </script>
    
</body>
</html>
    
    <!-- Footer Section -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-about">
                <h3>Adventure Travel</h3>
                <p>Explore Sri Lanka's breathtaking destinations with our premium tour packages and travel services. We offer unforgettable experiences with professional guides and comfortable transportation.</p>
                <div class="social-icons">
                    <a href="#" class="social-icon"><i class="social-icon-img">📱</i></a>
                    <a href="#" class="social-icon"><i class="social-icon-img">📘</i></a>
                    <a href="#" class="social-icon"><i class="social-icon-img">📸</i></a>
                    <a href="#" class="social-icon"><i class="social-icon-img">▶️</i></a>
                </div>
            </div>
            
            <div class="footer-links">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#packages">Packages</a></li>
                    <li><a href="#vehicle-hire">Vehicle Hire</a></li>
                    <li><a href="#destinations">Destinations</a></li>
                    <li><a href="#review">Reviews</a></li>
                    <li><a href="#team">Our Team</a></li>
                    <li><a href="contact_us.php">Contact Us</a></li>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="login.php?form=register">Register</a></li>
                </ul>
            </div>
            
            <div class="footer-contact">
                <h3>Contact Us</h3>
                <div class="contact-info">
                    <div class="contact-item">
                        <i class="contact-icon">📍</i>
                        <p>Narammala, Kurunegala, Sri Lanka</p>
                    </div>
                    <div class="contact-item">
                        <i class="contact-icon">📱</i>
                        <p>+94 71 538 0080</p>
                    </div>
                    <div class="contact-item">
                        <i class="contact-icon">✉️</i>
                        <p>adventuretravelsrilanka@gmail.com</p>
                    </div>
                </div>
            </div>
            
    
        </div>
        
        <div class="footer-bottom">
            <div class="copyright">
                <p>&copy; 2025 Adventure Travel. All Rights Reserved.</p>
            </div>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">FAQ</a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Include Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <script>
    // Initialize Swiper for packages
    const packageSlides = document.querySelectorAll('.packagesSwiper .swiper-slide');
    const packageSlidesCount = packageSlides.length;
    const getPackagesSlidesPerView = () => {
        const w = window.innerWidth;
        if (w >= 640) return 1.5;
        if (w >= 480) return 1;
        return 1;
    };
    const packagesMinRequiredForLoop = Math.ceil(getPackagesSlidesPerView()) + 1; // need > slidesPerView
    const packagesLoopEnabled = packageSlidesCount >= packagesMinRequiredForLoop;
    const packagesSwiper = new Swiper('.packagesSwiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        centeredSlides: true,
        loop: packagesLoopEnabled,
        pagination: {
            el: '.packagesSwiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.packagesSwiper .swiper-button-next',
            prevEl: '.packagesSwiper .swiper-button-prev',
        },
        breakpoints: {
            // When window width is >= 480px
            480: {
                slidesPerView: 1,
                spaceBetween: 20
            },
            // When window width is >= 640px
            640: {
                slidesPerView: 1.5,
                spaceBetween: 30
            }
        }
    });
    
    // Initialize Swiper for vehicles with conditional loop
    const vehicleSlides = document.querySelectorAll('.vehiclesSwiper .swiper-slide');
    const vehicleSlidesCount = vehicleSlides.length;
    const getVehiclesSlidesPerView = () => {
        const w = window.innerWidth;
        if (w >= 640) return 1.5;
        if (w >= 480) return 1;
        return 1;
    };
    const vehiclesMinRequiredForLoop = Math.ceil(getVehiclesSlidesPerView()) + 1; // need > slidesPerView
    const vehiclesLoopEnabled = vehicleSlidesCount >= vehiclesMinRequiredForLoop;
    const vehiclesSwiper = new Swiper('.vehiclesSwiper', {
        slidesPerView: 1,
        spaceBetween: 30,
        centeredSlides: true,
        loop: vehiclesLoopEnabled,
        pagination: {
            el: '.vehiclesSwiper .swiper-pagination',
            clickable: true,
        },
        navigation: {
            nextEl: '.vehiclesSwiper .swiper-button-next',
            prevEl: '.vehiclesSwiper .swiper-button-prev',
        },
        breakpoints: {
            // When window width is >= 480px
            480: {
                slidesPerView: 1,
                spaceBetween: 20
            },
            // When window width is >= 640px
            640: {
                slidesPerView: 1.5,
                spaceBetween: 30
            }
        }
    });
</script>
    
    <!-- Flip Card Functionality -->
    <script>
        // Function to handle card flipping on button click
        function setupCardFlipping() {
            // Get all flip hint buttons
            const flipHints = document.querySelectorAll('.flip-hint');
            
            // Add click event listener to each button
            flipHints.forEach(hint => {
                hint.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Find the parent flip card
                    const flipCard = hint.closest('.flip-card');
                    if (flipCard) {
                        flipCard.classList.toggle('flipped');
                    }
                });
            });
            
            // Add click event listener to the back button to flip back
            const flipCards = document.querySelectorAll('.flip-card');
            flipCards.forEach(card => {
                const backBtn = card.querySelector('.flip-back-btn');
                if (backBtn) {
                    backBtn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        card.classList.remove('flipped');
                    });
                }
                
                // Prevent card buttons from triggering flip back
                const cardButtons = card.querySelectorAll('.card-btn');
                cardButtons.forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.stopPropagation();
                    });
                });
            });
        }
        
        // Function to handle the reveal animation
        function revealCards() {
            const packageCards = document.querySelectorAll('.packages .flip-card');
            const vehicleCards = document.querySelectorAll('.vehicle-hire .flip-card');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Get the section this card belongs to
                        const isVehicleCard = entry.target.closest('.vehicle-hire') !== null;
                        const cards = isVehicleCard ? vehicleCards : packageCards;
                        
                        // Stagger the animations slightly but faster than before
                        const delay = Array.from(cards).indexOf(entry.target) * 100;
                        setTimeout(() => {
                            entry.target.classList.add('reveal');
                        }, delay);
                        
                        // Once revealed, stop observing
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.15, // Increased threshold for earlier detection
                rootMargin: '0px 0px -30px 0px' // Reduced negative margin for earlier animation
            });
            
            // Observe each card
            const allCards = document.querySelectorAll('.flip-card');
            allCards.forEach(card => {
                observer.observe(card);
            });
        }
        
        // Run on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Slight delay to ensure DOM is fully loaded
            setTimeout(() => {
                revealCards();
                setupCardFlipping();
            }, 100);
            
            // Add special handling for vehicle section to ensure animations are consistent
            const vehicleSection = document.querySelector('.vehicle-hire');
            if (vehicleSection) {
                const vehicleObserver = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) {
                        // Pre-load vehicle images for smoother animations
                        const vehicleImages = document.querySelectorAll('.vehicle-hire .front-image img');
                        vehicleImages.forEach(img => {
                            if (img.src) {
                                const preloadImg = new Image();
                                preloadImg.src = img.src;
                            }
                        });
                        
                        vehicleObserver.unobserve(vehicleSection);
                    }
                }, { threshold: 0.1 });
                
                vehicleObserver.observe(vehicleSection);
            }
        });
        
        // Fallback for browsers that don't support IntersectionObserver
        if (!('IntersectionObserver' in window)) {
            document.querySelectorAll('.flip-card').forEach(card => {
                card.classList.add('reveal');
            });
        }
    </script>
    
    <!-- Chat Interface -->
    <?php if ($is_logged_in): ?>
    <!-- Chat Button -->
    <div class="chat-btn-container">
        <button id="chat-btn" class="chat-btn">
            <i class="fas fa-comments"></i>
            <span class="chat-notification" id="chat-notification" style="display: none;"></span>
        </button>
    </div>
    
    <!-- Chat Box -->
    <div class="chat-box" id="chat-box">
        <div class="chat-header">
            <h3>Chat with Support</h3>
            <button id="close-chat" class="close-chat">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-messages" id="chat-messages">
            <div class="chat-welcome">
                <p>Welcome to Adventure Travel Chat Support! How can we help you today?</p>
            </div>
            <!-- Messages will be loaded here -->
        </div>
        <div class="chat-input">
            <textarea id="chat-message" placeholder="Type your message here..." style="font-size: 16px;" inputmode="text"></textarea>
            <button id="send-message" disabled>
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
        <!-- Reply container - hidden by default -->
        <div id="reply-container" style="display: none;" class="reply-preview">
            <div class="reply-content">
                <p id="reply-text"></p>
            </div>
            <button id="cancel-reply" class="cancel-action">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <!-- Edit container - hidden by default -->
        <div id="edit-container" style="display: none;" class="edit-preview">
            <div class="edit-content">
                <p id="edit-text"></p>
            </div>
            <button id="cancel-edit" class="cancel-action">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            const userId = <?php echo $is_logged_in ? $_SESSION['user_id'] : 'null'; ?>;
            let chatBox = $('#chat-box');
            let chatBtn = $('#chat-btn');
            let closeChat = $('#close-chat');
            let chatMessages = $('#chat-messages');
            let chatMessage = $('#chat-message');
            let sendMessage = $('#send-message');
            let chatNotification = $('#chat-notification');
            
            // Reply and edit functionality variables
            let replyContainer = $('#reply-container');
            let replyText = $('#reply-text');
            let cancelReply = $('#cancel-reply');
            let editContainer = $('#edit-container');
            let editText = $('#edit-text');
            let cancelEdit = $('#cancel-edit');
            let replyingTo = null;
            let editingMessageId = null;
            
            // Show debug info (for development)
            console.log("User ID:", userId);
            console.log("Base URL:", window.location.protocol + '//' + window.location.host);
            
            // Toggle chat box
            chatBtn.on('click', function() {
                // First display the chat box
                chatBox.css('display', 'flex');
                
                // Then load messages
                loadMessages();
                
                // Make sure to mark messages as read after chat box is visible
                console.log("Chat button clicked, marking messages as read");
                
                // Use setTimeout to ensure the chat box is fully visible before marking messages as read
                setTimeout(function() {
                    markMessagesAsRead();
                    
                    // Also use the direct endpoint for more reliability
                    if (userId) {
                        $.ajax({
                            url: 'mark_read_direct.php',
                            type: 'GET',
                            data: {
                                user_id: userId
                            },
                            dataType: 'json',
                            success: function(directResponse) {
                                console.log("Direct mark as read from button click:", directResponse);
                            }
                        });
                    }
                }, 300);
            });
            
            // Close chat box
            closeChat.on('click', function() {
                chatBox.hide();
            });
            
            // Enable/disable send button based on message content
            chatMessage.on('input', function() {
                sendMessage.prop('disabled', $(this).val().trim() === '');
            });
            
            // Send message on enter key
            chatMessage.on('keypress', function(e) {
                if (e.which === 13 && !e.shiftKey) {
                    e.preventDefault();
                    if (!sendMessage.prop('disabled')) {
                        sendMessageToServer();
                    }
                }
            });
            
            // Send message on button click
            sendMessage.on('click', function() {
                sendMessageToServer();
            });
            
            // Cancel reply
            cancelReply.on('click', function() {
                replyingTo = null;
                replyContainer.hide();
            });
            
            // Cancel edit
            cancelEdit.on('click', function() {
                editingMessageId = null;
                editContainer.hide();
                chatMessage.val('');
            });
            
            // Handle message action clicks (reply, edit, delete)
            $(document).on('click', '.action-btn', function() {
                const action = $(this).data('action');
                const messageId = $(this).closest('.message').data('id');
                const messageContent = $(this).closest('.message').find('.message-content').text();
                
                if (action === 'reply') {
                    // Set up reply mode
                    replyingTo = messageId;
                    replyText.text(messageContent.substring(0, 50) + (messageContent.length > 50 ? '...' : ''));
                    replyContainer.show();
                    editContainer.hide();
                    chatMessage.focus();
                    
                } else if (action === 'edit') {
                    // Set up edit mode - only if it's the user's own message
                    editingMessageId = messageId;
                    editText.text('Editing message');
                    editContainer.show();
                    replyContainer.hide();
                    chatMessage.val(messageContent).focus();
                    
                } else if (action === 'delete') {
                    // Custom styled confirmation dialog
                    showDeleteConfirmation(messageId);
                }
            });
            
            // Function to show a custom delete confirmation dialog
            function showDeleteConfirmation(messageId) {
                // Create confirmation overlay if it doesn't exist
                if ($('#delete-confirmation-overlay').length === 0) {
                    const confirmationHTML = `
                        <div id="delete-confirmation-overlay" style="
                            position: fixed;
                            top: 0;
                            left: 0;
                            right: 0;
                            bottom: 0;
                            background: rgba(0, 0, 0, 0.7);
                            backdrop-filter: blur(4px);
                            display: flex;
                            align-items: flex-end;
                            justify-content: center;
                            z-index: 9999;
                            opacity: 0;
                            visibility: hidden;
                            transition: opacity 0.3s ease, visibility 0.3s ease;
                        ">
                            <div id="delete-confirmation-dialog" style="
                                background: linear-gradient(145deg, #ff5252, #ff1744);
                                color: white;
                                border-radius: 20px 20px 0 0;
                                padding: 25px;
                                width: 100%;
                                max-width: 500px;
                                box-shadow: 0 -5px 30px rgba(255, 23, 68, 0.4);
                                transform: translateY(100%);
                                transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                                margin-bottom: 0;
                            ">
                                <div style="
                                    display: flex;
                                    align-items: center;
                                    margin-bottom: 15px;
                                ">
                                    <div style="
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        width: 50px;
                                        height: 50px;
                                        background-color: rgba(255, 255, 255, 0.2);
                                        border-radius: 50%;
                                        margin-right: 15px;
                                    ">
                                        <i class="fas fa-trash-alt" style="font-size: 20px;"></i>
                                    </div>
                                    <div>
                                        <h3 style="margin: 0; font-size: 20px; font-weight: 600;">Delete Message</h3>
                                        <p style="margin: 5px 0 0; opacity: 0.8; font-size: 14px;">This will permanently remove the message</p>
                                    </div>
                                </div>

                                <div style="
                                    background-color: rgba(255, 255, 255, 0.1);
                                    border-left: 4px solid rgba(255, 255, 255, 0.3);
                                    padding: 15px;
                                    border-radius: 0 10px 10px 0;
                                    margin: 20px 0;
                                ">
                                    <p style="margin: 0; font-size: 15px;">Are you sure you want to delete this message? This action cannot be undone.</p>
                                </div>

                                <div style="
                                    display: flex;
                                    justify-content: flex-end;
                                    gap: 15px;
                                    margin-top: 20px;
                                ">
                                    <button id="cancel-delete" style="
                                        padding: 12px 20px;
                                        border-radius: 50px;
                                        border: 1px solid rgba(255, 255, 255, 0.3);
                                        background: transparent;
                                        color: white;
                                        cursor: pointer;
                                        font-size: 15px;
                                        font-weight: 500;
                                        transition: all 0.2s ease;
                                    ">Cancel</button>
                                    <button id="confirm-delete" style="
                                        padding: 12px 25px;
                                        border-radius: 50px;
                                        border: none;
                                        background-color: white;
                                        color: #ff1744;
                                        cursor: pointer;
                                        font-size: 15px;
                                        font-weight: 600;
                                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                                        transition: all 0.2s ease;
                                    ">Delete</button>
                                </div>
                            </div>
                        </div>
                    `;
                    $('body').append(confirmationHTML);
                    
                    // Event handlers for the confirmation dialog
                    $('#cancel-delete').on('click', function() {
                        hideDeleteConfirm();
                    });
                    
                    $('#cancel-delete').on('mouseover', function() {
                        $(this).css({
                            'background': 'rgba(255, 255, 255, 0.1)',
                            'transform': 'translateY(-2px)'
                        });
                    }).on('mouseout', function() {
                        $(this).css({
                            'background': 'transparent',
                            'transform': 'translateY(0)'
                        });
                    });
                    
                    $('#confirm-delete').on('mouseover', function() {
                        $(this).css({
                            'transform': 'translateY(-2px)',
                            'box-shadow': '0 6px 15px rgba(0, 0, 0, 0.3)'
                        });
                    }).on('mouseout', function() {
                        $(this).css({
                            'transform': 'translateY(0)',
                            'box-shadow': '0 4px 10px rgba(0, 0, 0, 0.2)'
                        });
                    });
                    
                    // Close on click outside
                    $('#delete-confirmation-overlay').on('click', function(e) {
                        if (e.target === this) {
                            hideDeleteConfirm();
                        }
                    });
                    
                    // Close on ESC key
                    $(document).on('keydown', function(e) {
                        if (e.key === 'Escape' && $('#delete-confirmation-overlay').css('visibility') === 'visible') {
                            hideDeleteConfirm();
                        }
                    });
                }
                
                // Update confirmation button to use current messageId
                $('#confirm-delete').off('click').on('click', function() {
                    hideDeleteConfirm();
                    deleteMessage(messageId);
                });
                
                // Show the confirmation dialog
                const overlay = $('#delete-confirmation-overlay');
                const dialog = $('#delete-confirmation-dialog');
                
                overlay.css({
                    'visibility': 'visible',
                    'opacity': '1'
                });
                
                setTimeout(() => {
                    dialog.css('transform', 'translateY(0)');
                }, 50);
            }
            
            // Function to hide delete confirmation
            function hideDeleteConfirm() {
                const overlay = $('#delete-confirmation-overlay');
                const dialog = $('#delete-confirmation-dialog');
                
                dialog.css('transform', 'translateY(100%)');
                
                setTimeout(() => {
                    overlay.css({
                        'opacity': '0',
                        'visibility': 'hidden'
                    });
                }, 300);
            }
            
            // Function to send message
            function sendMessageToServer() {
                let message = chatMessage.val().trim();
                if (message === '') return;
                
                // Clear input
                chatMessage.val('');
                sendMessage.prop('disabled', true);
                
                // Add temporary message to chat (optimistic UI update)
                const tempId = 'temp-' + Date.now();
                let tempMessageHtml = '';
                
                if (replyingTo) {
                    // This is a reply message
                    const repliedToContent = $('#message-' + replyingTo).find('.message-content').text();
                    tempMessageHtml = `
                        <div id="${tempId}" class="message user" style="animation: fadeIn 0.3s;">
                            <div class="replied-message">↩️ ${escapeHtml(repliedToContent.substring(0, 50) + (repliedToContent.length > 50 ? '...' : ''))}</div>
                            <div class="message-content">${escapeHtml(message)}</div>
                            <div class="message-time">Sending...</div>
                        </div>
                    `;
                } else if (editingMessageId) {
                    // This is an edit - replace the existing message content
                    $(`#message-${editingMessageId} .message-content`).text(message);
                    $(`#message-${editingMessageId} .message-time`).html(`Updating... <span class="message-edited">(edited)</span>`);
                    editContainer.hide();
                    const messageIdToEdit = editingMessageId;
                    editingMessageId = null;
                    return updateMessage(messageIdToEdit, message);
                    } else {
                    // Regular message
                    tempMessageHtml = `
                        <div id="${tempId}" class="message user" style="animation: fadeIn 0.3s;">
                            <div class="message-content">${escapeHtml(message)}</div>
                            <div class="message-time">Sending...</div>
                        </div>
                    `;
                }
                
                chatMessages.append(tempMessageHtml);
                scrollToBottom();
                
                // Hide reply container after sending
                if (replyingTo) {
                    replyContainer.hide();
                }
                
                // Try with relative URL first (for localhost)
                const relativeUrl = 'admin/message_ajax.php';
                // Get the base URL for fallback
                const baseUrl = window.location.protocol + '//' + window.location.host;
                const chatAjaxUrl = baseUrl + '/Adventure_travels/admin/message_ajax.php';
                
                console.log("Sending message to:", relativeUrl);
                console.log("Message data:", {
                    action: 'send_message',
                    user_id: userId,
                    message: message,
                    is_admin: 0,
                    reply_to: replyingTo
                });
                
                // Send message to server using relative URL first
                $.ajax({
                    url: relativeUrl,
                    type: 'POST',
                    data: {
                        action: 'send_message',
                        user_id: userId,
                        message: message,
                        is_admin: 0,
                        reply_to: replyingTo // New field for reply functionality
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Send message response:", response);
                        
                        // Remove temporary message
                        $(`#${tempId}`).remove();
                        
                        if (response.success) {
                            // Add confirmed message to chat
                            addMessage(response.data, true);
                            scrollToBottom();
                            // Reset reply state
                            replyingTo = null;
                        } else {
                            const errorHtml = `
                                <div class="message admin" style="animation: fadeIn 0.3s;">
                                    <div class="message-content">Message could not be sent: ${response.message}</div>
                                    <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                </div>
                            `;
                            chatMessages.append(errorHtml);
                            console.error("Failed to send message:", response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Chat error with relative URL:", status, error);
                        
                        // Try with absolute URL
                        console.log("Trying with absolute URL:", chatAjaxUrl);
                        
                        $.ajax({
                            url: chatAjaxUrl,
                            type: 'POST',
                            data: {
                                action: 'send_message',
                                user_id: userId,
                                message: message,
                                is_admin: 0,
                                reply_to: replyingTo
                            },
                            dataType: 'json',
                            success: function(response) {
                                console.log("Send message response (absolute URL):", response);
                                
                                // Remove temporary message
                                $(`#${tempId}`).remove();
                                
                                if (response.success) {
                                    // Add confirmed message to chat
                                    addMessage(response.data, true);
                                    scrollToBottom();
                                    // Reset reply state
                                    replyingTo = null;
                                } else {
                                    const errorHtml = `
                                        <div class="message admin" style="animation: fadeIn 0.3s;">
                                            <div class="message-content">Message could not be sent: ${response.message}</div>
                                            <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                        </div>
                                    `;
                                    chatMessages.append(errorHtml);
                                    console.error("Failed to send message:", response.message);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Chat error with absolute URL:", textStatus, errorThrown);
                                console.log("Response headers:", jqXHR.getAllResponseHeaders());
                                
                                // Remove temporary message
                                $(`#${tempId}`).remove();
                                
                                const errorHtml = `
                                    <div class="message admin" style="animation: fadeIn 0.3s;">
                                        <div class="message-content">Cannot connect to server. Please check your internet connection.</div>
                                        <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                    </div>
                                `;
                                chatMessages.append(errorHtml);
                                
                                // Re-enable input for retry
                                chatMessage.val(message);
                                sendMessage.prop('disabled', false);
                            }
                        });
                    }
                });
            }
            // Function to update (edit) a message
            function updateMessage(messageId, newContent) {
                const relativeUrl = 'admin/message_ajax.php';
                const baseUrl = window.location.protocol + '//' + window.location.host;
                const chatAjaxUrl = baseUrl + '/Adventure_travels/admin/message_ajax.php';
                
                $.ajax({
                    url: relativeUrl,
                    type: 'POST',
                    data: {
                        action: 'edit_message',
                        message_id: messageId,
                        message: newContent
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Edit message response:", response);
                        
                        if (response.success) {
                            // Update the message with edited content and indicator
                            $(`#message-${messageId} .message-content`).text(newContent);
                            
                            // Add "edited" indicator if not already there
                            if ($(`#message-${messageId} .message-edited`).length === 0) {
                                $(`#message-${messageId} .message-time`).append('<span class="message-edited">(edited)</span>');
                            }
                        } else {
                            // Show specific error message
                            if (response.message.includes('10 minutes')) {
                                alert('You can only edit messages within 10 minutes of sending them.');
                            } else {
                                alert('Failed to update message: ' + response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Edit message error:", status, error);
                        
                        // Try with absolute URL
                        $.ajax({
                            url: chatAjaxUrl,
                            type: 'POST',
                            data: {
                                action: 'edit_message',
                                message_id: messageId,
                                message: newContent
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Update the message with edited content and indicator
                                    $(`#message-${messageId} .message-content`).text(newContent);
                                    
                                    // Add "edited" indicator if not already there
                                    if ($(`#message-${messageId} .message-edited`).length === 0) {
                                        $(`#message-${messageId} .message-time`).append('<span class="message-edited">(edited)</span>');
                                    }
                                } else {
                                    // Show specific error message
                                    if (response.message.includes('10 minutes')) {
                                        alert('You can only edit messages within 10 minutes of sending them.');
                                    } else {
                                        alert('Failed to update message: ' + response.message);
                                    }
                                }
                            },
                            error: function() {
                                alert('Failed to update message. Please try again.');
                            }
                        });
                    }
                });
            }
            
            // Function to delete a message
            function deleteMessage(messageId) {
                const relativeUrl = 'admin/message_ajax.php';
                const baseUrl = window.location.protocol + '//' + window.location.host;
                const chatAjaxUrl = baseUrl + '/Adventure_travels/admin/message_ajax.php';
                
                $.ajax({
                    url: relativeUrl,
                    type: 'POST',
                    data: {
                        action: 'delete_message',
                        message_id: messageId
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Delete message response:", response);
                        
                        if (response.success) {
                            // Remove the message from the UI
                            $(`#message-${messageId}`).fadeOut(300, function() {
                                $(this).remove();
                            });
                        } else {
                            // Show specific error message
                            if (response.message.includes('10 minutes')) {
                                alert('You can only delete messages within 10 minutes of sending them.');
                            } else {
                                alert('Failed to delete message: ' + response.message);
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Delete message error:", status, error);
                        
                        // Try with absolute URL
                        $.ajax({
                            url: chatAjaxUrl,
                            type: 'POST',
                            data: {
                                action: 'delete_message',
                                message_id: messageId
                            },
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    // Remove the message from the UI
                                    $(`#message-${messageId}`).fadeOut(300, function() {
                                        $(this).remove();
                                    });
                                } else {
                                    // Show specific error message
                                    if (response.message.includes('10 minutes')) {
                                        alert('You can only delete messages within 10 minutes of sending them.');
                                    } else {
                                        alert('Failed to delete message: ' + response.message);
                                    }
                                }
                            },
                            error: function() {
                                alert('Failed to delete message. Please try again.');
                            }
                        });
                    }
                });
            }
            
            // Function to load messages
            function loadMessages() {
                // Try with relative URL first (for localhost)
                const relativeUrl = 'admin/message_ajax.php';
                // Get the base URL for fallback
                const baseUrl = window.location.protocol + '//' + window.location.host;
                const chatAjaxUrl = baseUrl + '/Adventure_travels/admin/message_ajax.php';
                
                console.log("Loading messages from:", relativeUrl);
                
                // Show loading indicator
                const loadingId = 'loading-' + Date.now();
                const loadingHtml = `
                    <div id="${loadingId}" class="message admin" style="animation: fadeIn 0.3s;">
                        <div class="message-content">Loading messages...</div>
                        <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                    </div>
                `;
                chatMessages.append(loadingHtml);
                
                $.ajax({
                    url: relativeUrl,
                    type: 'POST',
                    data: {
                        action: 'get_messages',
                        user_id: userId
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Load messages response:", response);
                        
                        // Remove loading indicator
                        $(`#${loadingId}`).remove();
                        
                        if (response.success) {
                            // Clear messages except welcome
                            chatMessages.find('.message').remove();
                            
                            // Add all messages
                            if (response.data && response.data.length > 0) {
                                response.data.forEach(function(message) {
                                    addMessage(message);
                                });
                                
                                // Only mark messages as read if chat box is visible
                                if (chatBox.is(':visible')) {
                                    console.log("Messages loaded and chat box is visible, marking as read");
                                    markMessagesAsRead();
                                } else {
                                    console.log("Messages loaded but chat box is not visible, not marking as read");
                                }
                            } else {
                                const noMessagesHtml = `
                                    <div class="message admin">
                                        <div class="message-content">No messages yet. Start a conversation!</div>
                                        <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                    </div>
                                `;
                                chatMessages.append(noMessagesHtml);
                            }
                            
                            scrollToBottom();
                        } else {
                            const errorHtml = `
                                <div class="message admin">
                                    <div class="message-content">Error loading messages: ${response.message}</div>
                                    <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                </div>
                            `;
                            chatMessages.append(errorHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Load messages error with relative URL:", status, error);
                        
                        // Try with absolute URL
                        console.log("Trying with absolute URL:", chatAjaxUrl);
                        
                        $.ajax({
                            url: chatAjaxUrl,
                            type: 'POST',
                            data: {
                                action: 'get_messages',
                                user_id: userId
                            },
                            dataType: 'json',
                            success: function(response) {
                                console.log("Load messages response (absolute URL):", response);
                                
                                // Remove loading indicator
                                $(`#${loadingId}`).remove();
                                
                                if (response.success) {
                                    // Clear messages except welcome
                                    chatMessages.find('.message').remove();
                                    
                                    // Add all messages
                                    if (response.data && response.data.length > 0) {
                                        response.data.forEach(function(message) {
                                            addMessage(message);
                                        });
                                        
                                        // Only mark messages as read if chat box is visible
                                        if (chatBox.is(':visible')) {
                                            console.log("Messages loaded and chat box is visible, marking as read");
                                            markMessagesAsRead();
                                        } else {
                                            console.log("Messages loaded but chat box is not visible, not marking as read");
                                        }
                                    } else {
                                        const noMessagesHtml = `
                                            <div class="message admin">
                                                <div class="message-content">No messages yet. Start a conversation!</div>
                                                <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                            </div>
                                        `;
                                        chatMessages.append(noMessagesHtml);
                                    }
                                    
                                    scrollToBottom();
                                } else {
                                    const errorHtml = `
                                        <div class="message admin">
                                            <div class="message-content">Error loading messages: ${response.message}</div>
                                            <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                        </div>
                                    `;
                                    chatMessages.append(errorHtml);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Load messages error with absolute URL:", textStatus, errorThrown);
                                console.log("Response headers:", jqXHR.getAllResponseHeaders());
                                
                                // Remove loading indicator
                                $(`#${loadingId}`).remove();
                                
                                // Show error message
                                const errorHtml = `
                                    <div class="message admin">
                                        <div class="message-content">Cannot connect to server. Please check your internet connection.</div>
                                        <div class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                                    </div>
                                `;
                                chatMessages.append(errorHtml);
                            }
                        });
                    }
                });
            }

            // Function to mark messages as read
            function markMessagesAsRead() {
                // Don't proceed if user is not logged in
                if (!userId) {
                    console.log("Not marking messages as read - user not logged in");
                    return;
                }
                
                const relativeUrl = 'admin/message_ajax.php';
                const baseUrl = window.location.protocol + '//' + window.location.host;
                const chatAjaxUrl = baseUrl + '/Adventure_travels/admin/message_ajax.php';
                
                console.log("Marking messages as read for user:", userId);
                
                // Only mark messages as read if the chat box is visible
                if (!chatBox.is(':visible')) {
                    console.log("Chat box not visible, not marking messages as read");
                    return;
                }
                
                console.log("Chat box is visible, marking messages as read");
                
                // Try both URLs in sequence for better reliability
                console.log("Sending mark_as_read request with user_id:", userId);
                
                // First try with relative URL
                $.ajax({
                    url: relativeUrl,
                    type: 'POST',
                    data: {
                        action: 'mark_as_read',
                        user_id: userId
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Messages marked as read (relative URL):", response);
                        // Hide notification after marking messages as read
                        chatNotification.hide();
                        
                        // Force refresh unread count to ensure UI is updated
                        setTimeout(checkUnreadMessages, 500);
                        
                        // Also try direct SQL update via a special endpoint
                        $.ajax({
                            url: 'mark_read_direct.php',
                            type: 'GET',
                            data: {
                                user_id: userId
                            },
                            dataType: 'json',
                            success: function(directResponse) {
                                console.log("Direct mark as read completed:", directResponse);
                                setTimeout(checkUnreadMessages, 1000);
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error("Error marking messages as read:", status, error);
                        
                        // Try with absolute URL as fallback
                        fallbackMarkAsRead();
                    }
                });
                
                // Fallback function
                function fallbackMarkAsRead() {
                    $.ajax({
                        url: chatAjaxUrl,
                        type: 'POST',
                        data: {
                            action: 'mark_as_read',
                            user_id: userId
                        },
                        dataType: 'json',
                        success: function(response) {
                            console.log("Messages marked as read (absolute URL):", response);
                            // Hide notification after marking messages as read
                            chatNotification.hide();
                            
                            // Force refresh unread count to ensure UI is updated
                            setTimeout(checkUnreadMessages, 500);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.error("Error marking messages as read with absolute URL:", textStatus, errorThrown);
                        }
                    });
                }
            }

            // Function to check for unread messages periodically
            function checkUnreadMessages() {
                // Try with relative URL first (for localhost)
                const relativeUrl = 'admin/message_ajax.php';
                // Get the base URL for fallback
                const baseUrl = window.location.protocol + '//' + window.location.host;
                const chatAjaxUrl = baseUrl + '/Adventure_travels/admin/message_ajax.php';
                
                // Don't check for unread messages if chat is open
                // This prevents notification flickering when user has chat open
                if (chatBox.is(':visible')) {
                    console.log("Chat box is visible, hiding notification and skipping unread check");
                    chatNotification.hide();
                    return;
                }
                
                console.log("Checking unread messages");
                
                $.ajax({
                    url: relativeUrl,
                    type: 'POST',
                    data: {
                        action: 'get_unread_count',
                        user_id: userId
                    },
                    dataType: 'json',
                    success: function(response) {
                        console.log("Unread count response:", response);
                        
                        if (response.success && response.data > 0) {
                            // Show notification with count
                            chatNotification.text(response.data).show();
                            
                            // Play notification sound
                            if (typeof notificationSound !== 'undefined' && notificationSound) {
                                notificationSound.play().catch(function(error) {
                                    console.log("Sound play prevented:", error);
                                });
                            }
                        } else {
                            // No unread messages, hide notification
                            chatNotification.hide();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error checking unread count with relative URL:", status, error);
                        
                        // Try with absolute URL as fallback
                        $.ajax({
                            url: chatAjaxUrl,
                            type: 'POST',
                            data: {
                                action: 'get_unread_count',
                                user_id: userId
                            },
                            dataType: 'json',
                            success: function(response) {
                                console.log("Unread count response (absolute URL):", response);
                                
                                if (response.success && response.data > 0) {
                                    // Show notification with count
                                    chatNotification.text(response.data).show();
                                    
                                    // Play notification sound
                                    if (typeof notificationSound !== 'undefined' && notificationSound) {
                                        notificationSound.play().catch(function(error) {
                                            console.log("Sound play prevented:", error);
                                        });
                                    }
                                } else {
                                    // No unread messages, hide notification
                                    chatNotification.hide();
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error("Error checking unread count with absolute URL:", textStatus, errorThrown);
                                // Silent fail on this one - no need to bother user
                            }
                        });
                    }
                });
            }
            
            // Create notification sound
            let notificationSound;
            try {
                notificationSound = new Audio('data:audio/mp3;base64,SUQzBAAAAAAAI1RTU0UAAAAPAAADTGF2ZjU4Ljc2LjEwMAAAAAAAAAAAAAAA//tAwAAAAAAAAAAAAAAAAAAAAAAASW5mbwAAAA8AAAASAAAeMwAUFBQUFCIiIiIiIjAwMDAwMD4+Pj4+PkxMTExMTFpaWlpaWmdnZ2dnZ3V1dXV1dYODg4ODg5GRkZGRkZ+fn5+fn62tra2trbq6urq6usLCwsLCwtDQ0NDQ0NjY2NjY2Obm5ubm5vT09PT09P////////8AAAAATGF2YzU4LjEzAAAAAAAAAAAAAAAAJAV2Z2AAAAsAAAB4AJ5qJAkAAAAAAAAAAAAAAAAAAAAA//tANSAAAAAGcAAAAwAAA0gAAADOYAAAAwBAA0gAAADIAAAAKVRyYWNrIDEAAAAACgAAA0gAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA//vAwAAAAdsATQAAAAQAAA5gAAABAAABpAAAACAAADSAAAAETEFNRTMuMTAwVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVVU=');
            } catch (e) {
                console.log("Audio notification not supported:", e);
            }
            
            // Ensure chat notification is hidden initially
            $(document).ready(function() {
                chatNotification.hide();
            });
            
            // Check for new messages every 10 seconds
            setInterval(checkUnreadMessages, 10000);
            
            // Initial check for unread messages
            checkUnreadMessages();
            
            // Don't mark messages as read on page load, only when chat box is opened
            
            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            // Function to add a message to the chat
            function addMessage(message, isNew = false) {
                const isUser = message.is_admin == 0;
                const messageClass = isUser ? 'user' : 'admin';
                const date = new Date(message.created_at);
                const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                const messageId = message.message_id;
                
                // Check if edited
                const editedMarkup = message.edited ? '<span class="message-edited">(edited)</span>' : '';
                
                // Check if this is a reply to another message
                let replyMarkup = '';
                if (message.reply_to && message.replied_to_content) {
                    replyMarkup = `
                        <div class="replied-message">↩️ ${escapeHtml(message.replied_to_content.substring(0, 50) + (message.replied_to_content.length > 50 ? '...' : ''))}</div>
                    `;
                }
                
                // Check if message is less than 10 minutes old to show edit/delete buttons
                const messageTime = new Date(message.created_at).getTime();
                const currentTime = new Date().getTime();
                const timeDiffMinutes = (currentTime - messageTime) / 1000 / 60;
                const canModify = timeDiffMinutes <= 10;
                
                // Only show action buttons for user's messages and only show edit/delete if within time limit
                const actionButtons = isUser ? `
                    <div class="message-actions">
                        <button class="action-btn reply-btn" data-action="reply" title="Reply">
                            <i class="fas fa-reply"></i>
                        </button>
                        ${canModify ? `
                        <button class="action-btn edit-btn" data-action="edit" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete-btn" data-action="delete" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        ` : ''}
                    </div>
                ` : `
                    <div class="message-actions">
                        <button class="action-btn reply-btn" data-action="reply" title="Reply">
                            <i class="fas fa-reply"></i>
                        </button>
                    </div>
                `;
                
                const messageHtml = `
                    <div id="message-${messageId}" data-id="${messageId}" class="message ${messageClass}" ${isNew ? 'style="animation: fadeIn 0.3s;"' : ''}>
                        ${actionButtons}
                        ${replyMarkup}
                        <div class="message-content">${escapeHtml(message.message)}</div>
                        <div class="message-time">${formattedTime} ${editedMarkup}</div>
                    </div>
                `;
                
                chatMessages.append(messageHtml);
            }
            
            // Function to scroll to bottom of chat
            function scrollToBottom() {
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }
        });
    </script>
    <?php endif; ?>
    
    <!-- Sri Lankan Real-time Clock JavaScript -->
    <script>
        function updateSriLankaTime() {
            const now = new Date();
            
            const options = {
                timeZone: 'Asia/Colombo',
                hour12: true,
                hour: 'numeric',
                minute: 'numeric'
            };
            
            const secondsOptions = {
                timeZone: 'Asia/Colombo',
                second: 'numeric'
            };
            
            const timeStr = now.toLocaleTimeString('en-US', options);
            const seconds = now.toLocaleTimeString('en-US', secondsOptions).padStart(2, '0');
            
            // Format with seconds in a smaller size
            document.getElementById('sl-time').innerHTML = `${timeStr}<span style="font-size:0.8em; opacity:0.8; margin-left:2px;">:${seconds}</span>`;
            
            // Pulse animation on seconds change
            const clockElement = document.querySelector('.sri-lanka-clock');
            clockElement.style.transform = 'scale(1.02)';
            setTimeout(() => {
                clockElement.style.transform = '';
            }, 200);
        }
        
        // Update time immediately and then every second
        updateSriLankaTime();
        setInterval(updateSriLankaTime, 1000);
    </script>
    
    <!-- Welcome Popup JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show welcome popup when page loads
            const welcomePopup = document.getElementById('welcomePopup');
            const closeWelcome = document.getElementById('closeWelcome');
            const continueBtn = document.getElementById('continueBtn');
            const skipBtn = document.getElementById('skipBtn');
            
            // Display the popup
            welcomePopup.style.display = 'block';
            
            // Function to close the popup with fade effect
            function closePopup() {
                welcomePopup.style.opacity = 0;
                setTimeout(() => {
                    welcomePopup.style.display = 'none';
                }, 600);
            }
            
            // Close popup when close button is clicked
            closeWelcome.addEventListener('click', closePopup);
            
            // Close popup when continue button is clicked
            continueBtn.addEventListener('click', closePopup);
            
            // Close popup when skip button is clicked
            skipBtn.addEventListener('click', closePopup);
            
            // Close popup when clicking outside the content
            welcomePopup.addEventListener('click', function(event) {
                if (event.target === welcomePopup) {
                    closePopup();
                }
            });
        });
    </script>
    
    <!-- Background Music -->
    <audio id="backgroundMusic" loop>
        <source src="sounds/background4.mp3" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>
    
    <!-- Background Music JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bgMusic = document.getElementById('backgroundMusic');
            
            if (bgMusic) {
                // Set very low volume (0.1 = 10% volume)
                bgMusic.volume = 0.1;
                
                // Try to play music when user interacts with the page
                document.addEventListener('click', function() {
                    if (bgMusic.paused) {
                        bgMusic.play().catch(function(error) {
                            console.log("Background music play prevented:", error);
                        });
                    }
                }, { once: true });
                
                // Add music controls to the page
                const musicControlDiv = document.createElement('div');
                musicControlDiv.style.cssText = 'position: fixed; bottom: 70px; right: 20px; z-index: 9999; background-color: rgba(0,0,0,0.5); width: 26px; height: 26px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center;';
                
                const musicIcon = document.createElement('i');
                musicIcon.className = 'fas fa-music';
                musicIcon.style.cssText = 'color: white; font-size: 12px;';
                
                musicControlDiv.appendChild(musicIcon);
                document.body.appendChild(musicControlDiv);
                
                // Toggle music play/pause
                musicControlDiv.addEventListener('click', function() {
                    if (bgMusic.paused) {
                        bgMusic.play();
                        musicIcon.className = 'fas fa-music';
                    } else {
                        bgMusic.pause();
                        musicIcon.className = 'fas fa-volume-mute';
                    }
                });
            }
        });
    </script>
    
    <!-- Chatbase AI Assistant -->
    <script>
    (function(){if(!window.chatbase||window.chatbase("getState")!=="initialized"){window.chatbase=(...arguments)=>{if(!window.chatbase.q){window.chatbase.q=[]}window.chatbase.q.push(arguments)};window.chatbase=new Proxy(window.chatbase,{get(target,prop){if(prop==="q"){return target.q}return(...args)=>target(prop,...args)}})}const onLoad=function(){const script=document.createElement("script");script.src="https://www.chatbase.co/embed.min.js";script.id="mcJPiSLoWu9a4NAiIOZbF";script.domain="www.chatbase.co";document.body.appendChild(script)};if(document.readyState==="complete"){onLoad()}else{window.addEventListener("load",onLoad)}})();
    </script>
    
    <!-- Chat Button Size Matching -->
    <style>
    /* Force the size of the AI assistant button to match our chat button */
    #chatbase-bubble-button,
    .chatbase-bubble,
    .chatbase-launcher-frame button,
    iframe[id^="chatbase"] button {
        width: 50px !important;
        height: 50px !important;
        min-width: 50px !important;
        min-height: 50px !important;
        max-width: 50px !important;
        max-height: 50px !important;
        border-radius: 50% !important;
        /* Not changing position - only size */
    }
    </style>
    
    <!-- Script to ensure AI button matches chat button size only -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to apply size only after Chatbase loads
        function applyChatbaseStyles() {
            // Look for the chatbase button in various ways
            const chatbaseElements = document.querySelectorAll('[id^="chatbase-bubble"], .chatbase-bubble, [class*="chatbase"], iframe[id^="chatbase"]');
            
            chatbaseElements.forEach(el => {
                if (el.tagName === 'IFRAME') {
                    try {
                        const buttons = el.contentDocument.querySelectorAll('button');
                        buttons.forEach(button => {
                            button.style.width = '50px';
                            button.style.height = '50px';
                            button.style.minWidth = '50px';
                            button.style.minHeight = '50px';
                            button.style.borderRadius = '50%';
                            // Not changing position
                        });
                    } catch(e) {
                        // Cross-origin iframe access may fail
                    }
                } else if (el.tagName === 'BUTTON') {
                    el.style.width = '50px';
                    el.style.height = '50px';
                    el.style.minWidth = '50px';
                    el.style.minHeight = '50px';
                    el.style.borderRadius = '50%';
                    // Not changing position
                }
                
                // Check where the AI button is positioned to match our chat button
                if (el.tagName === 'BUTTON' || (el.tagName === 'DIV' && (el.classList.contains('chatbase-bubble') || el.id === 'chatbase-bubble-button'))) {
                    const aiButtonPosition = window.getComputedStyle(el).bottom;
                    if (aiButtonPosition && aiButtonPosition !== 'auto') {
                        // Update our chat button to match the AI button height
                        const chatButton = document.querySelector('.chat-btn-container');
                        if (chatButton) {
                            chatButton.style.bottom = aiButtonPosition;
                        }
                    }
                }
            });
            
            // Try again after a delay
            setTimeout(applyChatbaseStyles, 2000);
        }
        
        // Try to apply immediately and after load
        applyChatbaseStyles();
        window.addEventListener('load', applyChatbaseStyles);
    });
    </script>
    
    <!-- Position Matching Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to detect AI assistant button position and adjust chat button
        function matchChatButtonPosition() {
            // Function to observe the DOM for changes
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length) {
                        // Check if any AI assistant elements are added
                        const aiButtons = document.querySelectorAll(
                            '#chatbase-bubble-button, .chatbase-bubble, [class*="chatbase-launcher"], iframe[id^="chatbase"]'
                        );
                        
                        if (aiButtons.length > 0) {
                            aiButtons.forEach(function(aiButton) {
                                const styles = window.getComputedStyle(aiButton);
                                const bottom = styles.getPropertyValue('bottom');
                                
                                if (bottom && bottom !== 'auto') {
                                    // Update our chat button position
                                    const chatButton = document.querySelector('.chat-btn-container');
                                    if (chatButton) {
                                        chatButton.style.bottom = bottom;
                                        
                                        // Also update the chat box position
                                        const chatBox = document.querySelector('.chat-box');
                                        if (chatBox) {
                                            // Calculate chat box position (usually 60px above the button)
                                            const bottomValue = parseInt(bottom);
                                            if (!isNaN(bottomValue)) {
                                                chatBox.style.bottom = (bottomValue + 60) + 'px';
                                            }
                                        }
                                    }
                                }
                            });
                        }
                    }
                });
            });
            
            // Start observing the document
            observer.observe(document.body, { childList: true, subtree: true });
            
            // Also check periodically
            setInterval(function() {
                const aiButtons = document.querySelectorAll(
                    '#chatbase-bubble-button, .chatbase-bubble, [class*="chatbase-launcher"], iframe[id^="chatbase"]'
                );
                
                aiButtons.forEach(function(aiButton) {
                    const styles = window.getComputedStyle(aiButton);
                    const bottom = styles.getPropertyValue('bottom');
                    
                    if (bottom && bottom !== 'auto') {
                        // Update our chat button position
                        const chatButton = document.querySelector('.chat-btn-container');
                        if (chatButton) {
                            chatButton.style.bottom = bottom;
                        }
                    }
                });
            }, 2000);
        }
        
        // Start the position matching
        matchChatButtonPosition();
    });
    </script>
    
    <!-- Smooth Scrolling Performance Optimization -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add passive scrolling for better performance on mobile
        let supportsPassive = false;
        try {
            window.addEventListener("test", null, Object.defineProperty({}, 'passive', {
                get: function () { supportsPassive = true; return true; }
            }));
        } catch(e) {}
        
        const wheelOpt = supportsPassive ? { passive: true } : false;
        const wheelEvent = 'onwheel' in document.createElement('div') ? 'wheel' : 'mousewheel';
        
        // Use passive listeners for all scroll events to improve performance
        window.addEventListener('scroll', function() {}, wheelOpt);
        window.addEventListener(wheelEvent, function() {}, wheelOpt);
        window.addEventListener('touchstart', function() {}, wheelOpt);
        window.addEventListener('touchmove', function() {}, wheelOpt);
        
        // Smooth anchor link scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    // Smooth scroll to element
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    });
    </script>
</body>
</html>




