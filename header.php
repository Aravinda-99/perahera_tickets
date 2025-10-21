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
        body { 
            font-family: 'Noto Sans Sinhala', sans-serif; 
            padding: 0; 
            margin: 0; 
            background-color: #f4f4f4; 
        }
        .navbar { 
            background-color: #333333; 
            overflow: hidden; 
        }
        .navbar a { 
            float: left; 
            display: block; 
            color: white; 
            text-align: center; 
            padding: 14px 20px; 
            text-decoration: none; 
        }
        .navbar a:hover { 
            background-color: #ddd; 
            color: black; 
        }
        .navbar .right { 
            float: right; 
        }
    </style>
</head>
<body>
<div class="navbar">
    <a href="index.php">Home</a>
    <div class="right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">My Profile</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Signup</a>
        <?php endif; ?>
    </div>
</div></body>
</html>
