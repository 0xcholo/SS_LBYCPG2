<?php
include 'SS_Db_Conn.php';

if (!empty($_GET['username'])) {
    $username = $_GET['username'];

    // Fetch user ID and wallet balance
    $sql_user = "SELECT users.id, wallet.balance_amount 
                 FROM users 
                 LEFT JOIN wallet ON users.id = wallet.user_id 
                 WHERE username = '$username'";
    $result_user = $conn->query($sql_user);
    $row_user = $result_user->fetch_assoc();
    $user_id = $row_user["id"];
    $wallet_balance = isset($row_user["balance_amount"]) ? (float)$row_user["balance_amount"] : 0.00;

    // Fetch budgets for the user
    $sql_budgets = "SELECT id, budget_type, budget_amount FROM budgets WHERE user_id = '$user_id'";
    $result_budgets = $conn->query($sql_budgets);

    // Fetch expenses for each budget
    $expenses = [];
    while ($budget = $result_budgets->fetch_assoc()) {
        $budget_id = $budget['id'];
        $sql_expenses = "SELECT SUM(expense_amount) as total_expense FROM expenses WHERE user_id = '$budget_id'";
        $result_expenses = $conn->query($sql_expenses);
        $expense = $result_expenses->fetch_assoc();
        $expenses[$budget_id] = $expense['total_expense'] ? $expense['total_expense'] : 0;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="SS_Dashboard.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js -->
</head>
<body>
    <div class="container">
        <header>
            <h1>SpendSmart Dashboard</h1>
            <p>Welcome, <?php echo htmlspecialchars($username); ?>!</p>
        </header>

        <div class="budget-container">
            <?php while ($budget = $result_budgets->fetch_assoc()) { ?>
                <div class="budget-button" onclick="showChart(<?php echo $budget['id']; ?>)">
                    <p><?php echo $budget['budget_type']; ?> - $<?php echo number_format($budget['budget_amount'], 2); ?></p>
                </div>
            <?php } ?>
        </div>

        <!-- Placeholder for the Chart -->
        <div id="chart-container" style="display:none;">
            <canvas id="myChart"></canvas>
        </div>

        <!-- Displaying the wallet balance -->
        <div class="wallet-info">
            <p>Wallet Balance: <strong>$<?php echo number_format($wallet_balance, 2); ?></strong></p>
        </div>

        <script>
            function showChart(budgetId) {
                // Example of dynamically updating the chart with data for the selected budget
                document.getElementById('chart-container').style.display = 'block';
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['Budget Amount', 'Expenses'],
                        datasets: [{
                            label: 'Budget vs Expenses',
                            data: [<?php echo $budget['budget_amount']; ?>, <?php echo $expenses[$budget['id']]; ?>],
                            backgroundColor: ['#2cd182', '#ffab3b'],
                            borderColor: ['#2cd182', '#ffab3b'],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        </script>
    </div>
</body>
</html>
