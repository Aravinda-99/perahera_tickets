<?php
require 'db_connect.php';
require_once 'session.php';  // Include session handling
$error = '';

// If user is already logged in, redirect to booking form
if (isset($_SESSION['user_id'])) {
    header("Location: booking_form.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, email FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                // Password is correct, start a new session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                header("Location: booking_form.php"); // Redirect to booking form
                exit();
            } else {
                $error = "Incorrect password. Please try again.";
            }
        } else {
            $error = "No account found with that email address.";
        }
        $stmt->close();
    }
}
?>

<head>
    <title>Login</title>
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
            background: #FFFFFF;
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
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }
        input[type="email"]:focus, input[type="password"]:focus {
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
        .error {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            background-color: #FADBD8;
            color: #C0392B;
            border: 1px solid #C0392B;
        }
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        .signup-link a {
            color: #D35400;
            font-weight: 700;
            text-decoration: none;
        }
        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<div class="container">
    <h2>Login to Your Account</h2>
    <?php if(!empty($error)): ?><p class="error"><?php echo $error; ?></p><?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group"><label for="email">Email Address:</label><input type="email" id="email" name="email" required></div>
        <div class="form-group"><label for="password">Password:</label><input type="password" id="password" name="password" required></div>
        <button type="submit">Login</button>
    </form>
    <div class="signup-link">
        <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</div>
