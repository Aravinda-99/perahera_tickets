<?php
// Include session handling
require_once 'session.php';
?>
<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Desktop Navbar Styles */
        .navbar { 
            font-family: 'Noto Sans Sinhala', sans-serif;
            background-color:rgba(255, 255, 255, 0.96);
            overflow: hidden; 
            width: 100%;
            margin: 0;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border-radius: 0;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            box-sizing: border-box;
            top: 0;
            z-index: 1000;
        }
        
        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar .logo-icon {
            width: 35px;
            height: 35px;
            background: linear-gradient(135deg, #00bfff, #4169e1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            color: white;
            font-weight: bold;
        }

        .navbar .logo-text {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .navbar .nav-links {
            display: flex;
            gap: 17px;
        }

        .navbar .nav-links a { 
            display: block; 
            color: #333; 
            text-align: center; 
            padding: 8px 8px; 
            text-decoration: none; 
            transition: all 0.3s ease; 
            border-radius: 6px;
            font-weight: 510;
            font-size: 14px;
        }

        .navbar .nav-links a:hover,
        .navbar .nav-links a.active { 
            background-color: #345c6c;
            color: white; 
        }

        .navbar .right { 
            display: flex;
            align-items: center;
        }

        body {
            margin: 0;
            padding: 0;
        }

        /* Tablet Responsive Styles */
        @media (max-width: 768px) {
            .navbar {
                padding: 10px;
            }

            .navbar .logo-icon1 img {
                width: 260px !important;
                height: 40px !important;
            }

            .navbar .nav-links {
                gap: 8px;
            }

            .navbar .nav-links a {
                padding: 6px 8px;
                font-size: 13px;
            }
        }

        /* Mobile Responsive Styles */
        @media (max-width: 480px) {
            .navbar {
                padding: 8px;
                flex-direction: column;
                gap: 10px;
            }

            .navbar .logo-icon1 img {
                width: 200px !important;
                height: 35px !important;
            }

            .navbar .nav-links {
                width: 100%;
                justify-content: center;
                flex-wrap: wrap;
                gap: 6px;
            }

            .navbar .nav-links a {
                padding: 8px 12px;
                font-size: 12px;
                min-width: 80px;
                display: flex;
                align-items: center;
                justify-content: center;
                background-color: rgba(255, 255, 255, 0.1);
                border: 1px solid rgba(52, 92, 108, 0.2);
            }

            .navbar .nav-links a.active {
                background-color: #345c6c;
                color: white;
                border-color: #345c6c;
            }

            .navbar .nav-links a:not(.active):hover {
                background-color: rgba(52, 92, 108, 0.1);
                border-color: #345c6c;
            }
        }

        /* Touch Device Optimizations */
        @media (hover: none) {
            .navbar .nav-links a:hover {
                transform: none;
            }

            .navbar .nav-links a:active {
                transform: scale(0.98);
            }
        }
    </style>
</head>
<body>
<div class="navbar">
        <div class="logo">
            <div class="logo-icon1"><img src="assets/logo/logonav.png" alt="Logo" style="width: 340px; height: 50px;"></div>
        </div>
        <div class="nav-links">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="booking_form.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'booking_form.php') ? 'class="active"' : ''; ?>>Book Tickets</a>
                <a href="profile.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'class="active"' : ''; ?>>My Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'class="active"' : ''; ?>>Home</a>
                <a href="login.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'class="active"' : ''; ?>>Login</a>
                <a href="signup.php" <?php echo (basename($_SERVER['PHP_SELF']) == 'signup.php') ? 'class="active"' : ''; ?>>Signup</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
