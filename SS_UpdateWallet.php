<?php
include 'SS_Db_Conn.php';

$current_balance = 0; // Initialize variable to store balance

if (!empty($_GET['username'])) {
    $username = mysqli_real_escape_string($conn, $_GET['username']);

    // Fetch user ID
    $sql = "SELECT id FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $user_id = $row['id'];

    // Fetch current wallet balance
    $sql = "SELECT balance_amount FROM wallet WHERE user_id = $user_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $existing_balance = $result->fetch_assoc();
        $current_balance = $existing_balance['balance_amount'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];

    if ($current_balance) {
        // Update balance if it exists
        $new_balance = $current_balance + $amount;
        $sql = "UPDATE wallet SET balance_amount = $new_balance WHERE user_id = $user_id";
        if ($conn->query($sql) === TRUE) {
            $current_balance = $new_balance; // Update displayed balance
            echo "<p>Wallet updated successfully! Your current balance is: $" . number_format($current_balance, 2) . "</p>";
        } else {
            echo "<p>Error updating wallet: " . $conn->error . "</p>";
        }
    } else {
        // Insert a new wallet record if no balance exists
        $sql = "INSERT INTO wallet (user_id, balance_amount) VALUES ($user_id, $amount)";
        if ($conn->query($sql) === TRUE) {
            $current_balance = $amount; // Set new balance
            echo "<p>Wallet created successfully! Your current balance is: $" . number_format($current_balance, 2) . "</p>";
        } else {
            echo "<p>Error creating wallet: " . $conn->error . "</p>";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Wallet</title>
    <link href="SS_UpdateWallet.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">SpendSmart</h1>
            <p class="subtitle">Update Wallet Balance</p>
        </header>
        <main>
            <!-- Display Current Balance -->
            <div class="current-balance">
                <p>Your Current Balance: <strong>$<?php echo number_format($current_balance, 2); ?></strong></p>
            </div>

            <form method="POST" action="SS_UpdateWallet.php?username=<?php echo urlencode($_GET['username']); ?>" class="wallet-form">
                <div class="form-group">
                    <label for="amount">Enter Amount:</label>
                    <input type="number" step="0.01" id="amount" name="amount" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Balance</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='SS_Home.php?username=<?php echo urlencode($_GET['username']); ?>';">Return to Home Page</button>
            </form>
        </main>
    </div>
</body>
</html>
