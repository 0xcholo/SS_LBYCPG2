<?php
    include 'SS_Db_Conn.php';

    // Fetch user ID based on username
    if (!empty($_GET['username'])) {
        $username = $_GET['username'];
        $sql = "SELECT * FROM users WHERE username = '$username'";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $user_id = $row["id"];
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize user input
        $budget_type = $_POST['budget_category'];
        $budget_amount =$_POST['budget_amount'];
       
        // Create the SQL query
        $sql = "
            INSERT INTO budgets (user_id, budget_type, budget_amount)
            VALUES ('$user_id', '$budget_type', '$budget_amount')
        ";

        // Execute the query
        if ($conn->query($sql) === TRUE) {
            echo "<p>Budget successfully added!</p>";
        } else {
            echo "<p>Error: " . $conn->error . "</p>";
        }

        $conn->close();
    }
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
            <form method="POST" action="SS_NewBudget.php?username=<?php echo $username; ?>" class="budget-form">
                <div class="form-group">
                    <label for="budget_category">Budget Category:</label>
                    <select id="budget_category" name="budget_category" required>
                        <option value="Food">Food</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Personal Essentials">Personal Essentials</option>
                        <option value="Savings">Savings</option>
                        <option value="Miscellaneous">Miscellaneous</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="budget_amount">Budget Amount:</label>
                    <input type="number" id="budget_amount" name="budget_amount" step="0.01" required>
                </div>
                <div class="button-group">
                    <button type="submit" class="btn btn-primary">Add Budget</button>
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='SS_Home.php?username=<?php echo urlencode($username); ?>';">Home</button>
                </div>
            </form>
        </main>
    </div>
</body>
</html>
