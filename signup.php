<?php
require 'db_connect.php';
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } else {
        // Check if email already exists
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $message = "This email address is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                $message = "Registration successful! You can now log in.";
                header("Refresh: 2; url=login.php");
            } else {
                $message = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $stmt_check->close();
    }
}
?>
<!-- <?php include 'header.php'; ?> -->
<head>
    <title>Sign Up</title>
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
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            max-width: 450px;
            margin: 40px auto;
            background: rgb(255, 255, 255);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-top: 5px solid #FF9933;
        }
        h2 {
            text-align: center;
            color: #D35400;
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
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="text"]:focus, input[type="email"]:focus, input[type="password"]:focus {
            outline: none;
            border-color: #FF9933;
        }
        button {
            width: 100%;
            background: linear-gradient(45deg, #FF9933, #D35400);
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
        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #eef;
            color: #333;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #D35400;
            font-weight: 700;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<div class="container">
    <h2>Create a New Account</h2>
    <?php if(!empty($message)): ?><p class="message"><?php echo $message; ?></p><?php endif; ?>
    <form action="signup.php" method="POST">
        <div class="form-group"><label for="username">Username:</label><input type="text" id="username" name="username" required></div>
        <div class="form-group"><label for="email">Email Address:</label><input type="email" id="email" name="email" required></div>
        <div class="form-group"><label for="password">Password:</label><input type="password" id="password" name="password" required></div>
        <button type="submit">Sign Up</button>
    </form>
    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>
