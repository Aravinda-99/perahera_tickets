<?php
require 'db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Perahera Ticket Booking</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Noto Sans Sinhala', sans-serif;
            background-image: url('assets/images/perahera2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            line-height: 1.6;
            margin: 0; /* Remove default margin */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-top: 5px solid rgb(255, 198, 40); /* Saffron/Orange accent */
        }
        h2 {
            text-align: center;
            color: #D35400; /* Deeper Orange */
            font-weight: 700;
            margin-bottom: 25px;
            letter-spacing: 1px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 700;
            color: #555;
        }
        input[type="text"], select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, select:focus {
            outline: none;
            border-color: #FF9933; /* Saffron/Orange accent on focus */
        }
        button {
            width: 100%;
            background: linear-gradient(45deg, #FF9933, #D35400); /* Orange gradient */
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(211, 84, 0, 0.4);
        }
        .info-box {
            padding: 20px;
            background-color: #FFF8E1; /* Light yellow */
            border-left: 5px solid #FF9933; /* Saffron/Orange accent */
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .info-box p {
            margin: 0;
            margin-bottom: 15px;
        }
        .auth-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .auth-actions a {
            text-decoration: none;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .auth-actions a.login-btn {
             background: linear-gradient(45deg, #FF9933, #D35400);
        }
         .auth-actions a.signup-btn {
            background-color: #555;
        }
        .auth-actions a:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
    </style>
</head>
<div class="container">
    <h2>Book Your Perahera Tickets</h2>
    <?php 
    if (isset($_SESSION['user_id'])) {
        header("Location: booking_form.php");
        exit();
    } else {
    ?>
        <div class="info-box">
            <p>To book tickets, please log in or create an account.</p>
            <div class="auth-actions">
                <a href="login.php" class="login-btn">Login</a>
                <a href="signup.php" class="signup-btn">Create Account</a>
            </div>
        </div>
    <?php } ?>
    </div>
</body>
</html>