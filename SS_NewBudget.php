<?php
include 'SS_Db_Conn.php';

// Fetch user ID and wallet balance based on username
if (!empty($_GET['username'])) {
    $username = $_GET['username'];
    $sql = "SELECT users.id, wallet.balance_amount 
            FROM users 
            LEFT JOIN wallet ON users.id = wallet.user_id 
            WHERE username = '$username'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $user_id = $row["id"];
    $wallet_balance = isset($row["balance_amount"]) ? (float)$row["balance_amount"] : 0.00;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $budget_type = $_POST['budget_category'];
    $budget_amount = (float)$_POST['budget_amount'];

    // Check if wallet balance is sufficient
    if ($budget_amount > $wallet_balance) {
        $error_message = "Error: Insufficient balance to create this budget.";
    } else {
        // Check if budget type already exists
        $sql_check = "SELECT budget_amount FROM budgets WHERE user_id = '$user_id' AND budget_type = '$budget_type'";
        $result_check = $conn->query($sql_check);

        if ($result_check && $result_check->num_rows > 0) {
            // Budget type exists; update the existing record
            $conn->query("UPDATE budgets SET budget_amount = budget_amount + $budget_amount WHERE user_id = '$user_id' AND budget_type = '$budget_type'");
            $success_message = "Budget successfully updated!";
        } else {
            // Budget type does not exist; create a new record
            $conn->query("INSERT INTO budgets (user_id, budget_type, budget_amount) VALUES ('$user_id', '$budget_type', '$budget_amount')");
            $success_message = "New budget successfully added!";
        }

        // Deduct from wallet balance
        $conn->query("UPDATE wallet SET balance_amount = balance_amount - $budget_amount WHERE user_id = '$user_id'");

        // Redirect to refresh the page with updated balance
        header("Location: SS_NewBudget.php?username=" . urlencode($username));
        exit;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Budget</title>
    <link href="SS_NewBudget.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">SpendSmart</h1>
            <p class="subtitle">Create a New Budget</p>
        </header>
        <main>
            <form method="POST" action="SS_NewBudget.php?username=<?php echo urlencode($username); ?>" class="budget-form">
                <div class="form-group">
                    <label for="budget_category">Budget Category:</label>
                    <select id="budget_category" name="budget_category" required>
                        <option value="Food">Food</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Personal Essentials">Personal Essentials</option>
                        <option value="Savings">Investments</option>
                        <option value="Miscellaneous">Miscellaneous</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="budget_amount">Budget Amount:</label>
                    <input type="number" id="budget_amount" name="budget_amount" step="0.01" required>
                </div>
                <div class="wallet-info">
                    <p>Available Balance: <strong><?php echo number_format($wallet_balance, 2); ?></strong></p>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Add Budget</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='SS_Home.php?username=<?php echo urlencode($username); ?>';">Return to Home Page</button>
                </div>
            </form>
            <?php if (isset($error_message)): ?>
                <p style="color: red; margin-top: 20px;"><?php echo $error_message; ?></p>
            <?php elseif (isset($success_message)): ?>
                <p style="color: #2cd182; margin-top: 20px;"><?php echo $success_message; ?></p>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
