<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart Registration Page</title>
    <link href="SS_Register.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">SpendSmart</h1>
            <p class="subtitle">Create your account</p>
        </header>
        <main>
            <form method="POST" action="SS_Register.php" class="register-form">
                <div class="form-group">
                    <label for="full_name">Full Name:</label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p class="login-link">
                Already have an account? <a href="SS_Login.php">Login here</a>
            </p>
        </main>
    </div>
</body>
</html>

<?php 
    include 'SS_Db_Conn.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $full_name = $_POST['full_name']; 
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];

        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert the user into the database
        $sql = "INSERT INTO users (full_name, username, password, email) VALUES ('$full_name', '$username', '$hashed_password', '$email')";

        if ($conn->query($sql) === TRUE) {
            echo "<p>Registration Successful!</p>";
            echo "<p>You can now <a href='SS_Login.php'>Login</a>.</p>";
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }

        $conn->close();
    }
?>