<?php
include 'SS_Db_Conn.php';

if (!empty($_GET['username'])) {
    $username = $_GET['username'];
    $sql = "SELECT users.id FROM users WHERE username = '$username'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $user_id = $row["id"];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_type = $_POST['expense_type'];
    $expense_amount = (float)$_POST['expense_amount'];

    // Validate the expense amount
    if ($expense_amount <= 0) {
        echo "<p style='color: red;'>Error: Please enter a valid expense amount.</p>";
    } else {
        // Subtract from the corresponding budget and save the expense
        $sql_budget_check = "SELECT budget_amount FROM budgets WHERE user_id = '$user_id' AND budget_type = '$expense_type'";
        $result = $conn->query($sql_budget_check);

        if ($result && $result->num_rows > 0) {
            $budget = $result->fetch_assoc();
            if ($expense_amount > $budget['budget_amount']) {
                echo "<p style='color: red;'>Error: Expense exceeds the available budget for $expense_type.</p>";
            } else {
                // Deduct expense from the budget
                $conn->query("UPDATE budgets SET budget_amount = budget_amount - $expense_amount WHERE user_id = '$user_id' AND budget_type = '$expense_type'");

                // Insert expense record
                $sql_expense = "
                    INSERT INTO expenses (user_id, expense_type, expense_amount)
                    VALUES ('$user_id', '$expense_type', '$expense_amount')
                ";
                if ($conn->query($sql_expense) === TRUE) {
                    echo "<p style='color: green;'>Expense successfully added!</p>";
                } else {
                    echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>Error: No budget found for $expense_type.</p>";
        }
    }
}

// Do not close the connection here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Expenses</title>
    <link href="SS_AddExpenses.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">SpendSmart</h1>
            <p class="subtitle">Add New Expense</p>
        </header>
        <main>
            <form method="POST" action="SS_AddExpenses.php?username=<?php echo urlencode($username); ?>" class="expense-form">
                <div class="form-group">
                    <label for="expense_type">Expense Category:</label>
                    <select id="expense_type" name="expense_type" required>
                        <!-- Populate budget types dynamically -->
                        <?php
                        $sql = "SELECT budget_type FROM budgets WHERE user_id = '$user_id'";
                        $result = $conn->query($sql);
                        if ($result) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . htmlspecialchars($row['budget_type']) . "'>" . htmlspecialchars($row['budget_type']) . "</option>";
                            }
                        } else {
                            echo "<option disabled>Error loading budget types</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="expense_amount">Expense Amount:</label>
                    <input type="number" id="expense_amount" name="expense_amount" step="0.01" required>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Add Expense</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='SS_Home.php?username=<?php echo urlencode($username); ?>';">Return to Home Page</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
<?php
// Close the connection at the end of the script
$conn->close();
?>
