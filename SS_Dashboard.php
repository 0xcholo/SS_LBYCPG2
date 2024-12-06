<?php
include 'SS_Db_Conn.php';

// Fetch user data
$username = $_GET['username'] ?? '';
$sql_user = "SELECT id FROM users WHERE username = '$username'";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc();
$user_id = $user['id'] ?? null;

// Fetch wallet balance
$sql_wallet = "SELECT balance_amount FROM wallet WHERE user_id = '$user_id'";
$result_wallet = $conn->query($sql_wallet);
$wallet = $result_wallet->fetch_assoc();
$wallet_balance = $wallet['balance_amount'] ?? 0.00;

// Fetch budgets
$sql_budgets = "SELECT * FROM budgets WHERE user_id = '$user_id'";
$result_budgets = $conn->query($sql_budgets);
$budgets = [];
while ($row = $result_budgets->fetch_assoc()) {
    $budgets[] = $row;
}

// Fetch expenses grouped by budget_id
$sql_expenses = "SELECT budget_id, expense_amount, created_at FROM expenses WHERE user_id = '$user_id' ORDER BY created_at ASC";
$result_expenses = $conn->query($sql_expenses);
$expenses_by_budget = [];
while ($row = $result_expenses->fetch_assoc()) {
    $expenses_by_budget[$row['budget_id']][] = $row; // Group expenses by budget_id
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SpendSmart</title>
    <link href="SS_Dashboard.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">SpendSmart</h1>
            <p class="subtitle">Dashboard</p>
        </header>
        <main>
            <div class="wallet-info">
                <h2>Wallet Balance: <?php echo number_format($wallet_balance, 2); ?></h2>
            </div>
            <div class="budgets-section">
                <?php foreach ($budgets as $budget): ?>
                    <div class="budget-card">
                        <button class="budget-toggle" onclick="toggleBudgetDetails('<?php echo $budget['id']; ?>')">
                            <?php echo htmlspecialchars($budget['budget_type']); ?>
                        </button>
                        <div id="details-<?php echo $budget['id']; ?>" class="budget-details">
                            <p>Budget Amount: <?php echo number_format($budget['budget_amount'], 2); ?></p>
                            <h4>Expenses:</h4>
                            <ul>
                                <?php if (!empty($expenses_by_budget[$budget['id']])): ?>
                                    <?php foreach ($expenses_by_budget[$budget['id']] as $expense): ?>
                                        <li>
                                            Amount: <?php echo number_format($expense['expense_amount'], 2); ?> - 
                                            Date: <?php echo date('Y-m-d H:i:s', strtotime($expense['created_at'])); ?>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li>No expenses for this budget.</li>
                                <?php endif; ?>
                            </ul>
                            <canvas id="chart-<?php echo $budget['id']; ?>" class="budget-chart"></canvas>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="button-group">
                <button class="btn-home" onclick="window.location.href='SS_Home.php?username=<?php echo urlencode($username); ?>';">Back to Home Page</button>
            </div>
        </main>
    </div>
    <script>
        function toggleBudgetDetails(budgetId) {
            const details = document.getElementById('details-' + budgetId);
            details.style.display = (details.style.display === 'block') ? 'none' : 'block';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const expenseData = <?php echo json_encode($expenses_by_budget); ?>;

            for (const [budgetId, expenses] of Object.entries(expenseData)) {
                const ctx = document.getElementById('chart-' + budgetId).getContext('2d');

                const labels = expenses.map(e => new Date(e.created_at).toLocaleString());
                const data = expenses.map(e => e.expense_amount);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Expenses',
                            data: data,
                            backgroundColor: 'rgba(44, 209, 130, 0.6)',
                            borderColor: 'rgba(44, 209, 130, 1)',
                            borderWidth: 1,
                        }]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: {
                                title: { display: true, text: 'Date & Time', color: '#ffffff' },
                                ticks: { color: '#ffffff' }
                            },
                            y: {
                                title: { display: true, text: 'Expense Amount', color: '#ffffff' },
                                ticks: { color: '#ffffff' },
                                beginAtZero: true
                            }
                        },
                        plugins: {
                            legend: { labels: { color: '#ffffff' } }
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>
