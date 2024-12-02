<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart Login Page</title>
    <link href="SS_Login.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">SpendSmart</h1>
            <p class="subtitle">Login to your account</p>
        </header>
        <main>
            <form method="POST" action="SS_Login.php" class="login-form">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="register-link">
                Don't have an account? <a href="SS_Register.php">Create one here</a>.
            </p>
        </main>
    </div>
</body>
</html>



<?php 
    include 'SS_Db_Conn.php';

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $password = $_POST['password']; 

        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {
                echo '<meta http-equiv="refresh" content="0;url=SS_Home.php?username=' . urlencode($username) . '">';
            } else {
            echo "Incorrect password. Please try again.";
            }
        } else {
            echo "<br>No account found with that username.<br>";
            echo "<a href='SS_register.php'>
                    <button>Create an Account</button>
                  </a>";
        }

        $conn->close();
}
?>