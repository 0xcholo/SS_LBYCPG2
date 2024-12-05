<?php
include 'SS_Db_Conn.php';

$error_message = ""; // To store error messages
$success_message = ""; // To store success messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve inputs
    $username = $_GET['username'];
    $category =  $_POST['category'];
    $action = $_POST['action']; // Increase or decrease
    $amount_change = (float)$_POST['amount'];

    // Fetch user ID
    $sql = "SELECT id FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];

        // Fetch the budget for the selected category
        $sql = "SELECT budget_amount FROM budgets WHERE user_id = $user_id AND budget_type = '$category'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $budget = $result->fetch_assoc();
            $current_budget = $budget['budget_amount'];

            // Fetch the user's wallet balance
            $sql = "SELECT balance_amount FROM wallet WHERE user_id = $user_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $wallet = $result->fetch_assoc();
                $current_balance = $wallet['balance_amount'];

                // Perform the appropriate action
                if ($action === 'increase') {
                    if ($amount_change > $current_balance) {
                        $error_message = "Insufficient balance to increase the budget.";
                    } else {
                        $new_budget = $current_budget + $amount_change;
                        $new_balance = $current_balance - $amount_change;

                        // Update budget and wallet
                        $conn->query("UPDATE budgets SET budget_amount = $new_budget WHERE user_id = $user_id AND budget_type = '$category'");
                        $conn->query("UPDATE wallet SET balance_amount = $new_balance WHERE user_id = $user_id");

                        $success_message = "Budget increased successfully! New $category budget: $" . number_format($new_budget, 2) . ". Remaining balance: $" . number_format($new_balance, 2) . ".";
                    }
                } elseif ($action === 'decrease') {
                    if ($amount_change > $current_budget) {
                        $error_message = "Cannot decrease more than the current budget.";
                    } else {
                        $new_budget = $current_budget - $amount_change;
                        $new_balance = $current_balance + $amount_change;

                        // Update budget and wallet
                        $conn->query("UPDATE budgets SET budget_amount = $new_budget WHERE user_id = $user_id AND budget_type = '$category'");
                        $conn->query("UPDATE wallet SET balance_amount = $new_balance WHERE user_id = $user_id");

                        $success_message = "Budget decreased successfully! New $category budget: $" . number_format($new_budget, 2) . ". Updated balance: $" . number_format($new_balance, 2) . ".";
                    }
                }
            } else {
                $error_message = "Wallet not found.";
            }
        } else {
            $error_message = "No budget exists for the selected category.";
        }
    } else {
        $error_message = "User not found.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Budget</title>
    <link href="SS_EditBudget.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">SpendSmart</h1>
            <p class="subtitle">Edit Your Budget</p>
        </header>
        <main>
            <!-- Display Success or Error Messages -->
            <?php if (!empty($success_message)) : ?>
                <div class="message success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            <?php if (!empty($error_message)) : ?>
                <div class="message error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST" action="SS_EditBudget.php?username=<?php echo urlencode($_GET['username']); ?>" class="edit-budget-form">
                <div class="form-group">
                    <label for="category">Select Category:</label>
                    <select id="category" name="category" required>
                        <option value="Food">Food</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Personal Essentials">Personal Essentials</option>
                        <option value="Investments">Investments</option>
                        <option value="Miscellaneous">Miscellaneous</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="amount">Enter Amount:</label>
                    <input type="number" step="0.01" id="amount" name="amount" required>
                </div>
                <div class="button-group">
                    <button type="submit" name="action" value="increase" class="btn btn-primary">Increase Budget</button>
                    <button type="submit" name="action" value="decrease" class="btn btn-secondary">Decrease Budget</button>
                </div>
                <button type="button" class="btn btn-home" onclick="window.location.href='SS_Home.php?username=<?php echo urlencode($_GET['username']); ?>';">Return to Home Page</button>
            </form>
        </main>
    </div>
</body>
</html>

