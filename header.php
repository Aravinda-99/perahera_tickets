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
        /* --- Navbar and Body Styles (පැරණි ඒවාමයි) --- */
        .navbar { 
            font-family: 'Noto Sans Sinhala', sans-serif;
            background-color:rgb(56, 42, 24);
            overflow: hidden; 
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            position: relative;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .navbar .logo {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .navbar .logo-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #00bfff, #4169e1);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            font-weight: bold;
        }
        .navbar .logo-text {
            font-size: 18px;
            font-weight: 600;
            color: white;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .navbar .nav-links {
            display: flex;
            gap: 5px;
        }
        .navbar .nav-links a { 
            display: block; 
            color: white; 
            text-align: center; 
            padding: 10px 18px; 
            text-decoration: none; 
            transition: all 0.3s ease; 
            border-radius: 15px;
            font-weight: 500;
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
    </style>
</head>
<body>
<div class="navbar">
        <div class="logo">
            <div class="logo-icon">LL</div>
            <div class="logo-text">PERAHERA TICKETS</div>
        </div>
        <div class="nav-links">
            <a href="index.php" class="active">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php">My Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Signup</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
