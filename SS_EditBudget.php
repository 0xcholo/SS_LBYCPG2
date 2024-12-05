<?php
include 'SS_Db_Conn.php';

$error_message = ""; // To store error messages
$success_message = ""; // To store success messages

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and retrieve inputs
    $username = mysqli_real_escape_string($conn, $_GET['username']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount_change = (float)$_POST['amount'];

    // Fetch user ID
    $sql = "SELECT id FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_id = $row['id'];

        // Check if the budget category exists for the user
        $sql = "SELECT amount FROM budgets WHERE user_id = $user_id AND category = '$category'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $budget = $result->fetch_assoc();
            $current_amount = $budget['amount'];
            $new_amount = $current_amount + $amount_change;

            // Validate new amount
            if ($new_amount < 0) {
                $error_message = "The budget amount cannot be less than $0.00.";
            } else {
                // Update the budget
                $sql = "UPDATE budgets SET amount = $new_amount WHERE user_id = $user_id AND category = '$category'";
                if ($conn->query($sql) === TRUE) {
                    $success_message = "Budget updated successfully! New amount for $category is $" . number_format($new_amount, 2) . ".";
                } else {
                    $error_message = "Error updating budget: " . $conn->error;
                }
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
                    <label for="amount">Enter Amount (positive to increase, negative to decrease):</label>
                    <input type="number" step="0.01" id="amount" name="amount" required>
                </div>
                <button type="submit" class="btn btn-primary">Update Budget</button>
                <button type="button" class="btn btn-secondary" onclick="window.location.href='SS_Home.php?username=<?php echo urlencode($_GET['username']); ?>';">Return to Home Page</button>
            </form>
        </main>
    </div>
</body>
</html>
