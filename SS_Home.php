<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SpendSmart Home</title>
    <link href="SS_Home.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-left">
                <h1 class="logo">SpendSmart</h1>
                <p class="username">Welcome, <?php  if(!empty($_GET['username'])) {
                                                        $username = $_GET['username'];
                                                        echo $username;
                                                    } ?></p>
            </div>
        </header>
        <main>
            <section class="options">
                <div class="option" onclick="window.location.href='SS_Dashboard.php?username=<?php if(!empty($_GET['username'])) { $username = $_GET['username']; echo urlencode($username); } ?>';">
                    <h2>Dashboard</h2>
                    <p>View your budgets and transactions.</p>
                </div>
                <div class="option" onclick="window.location.href='SS_NewBudget.php?username=<?php if(!empty($_GET['username'])) { $username = $_GET['username']; echo urlencode($username); } ?>';">
                    <h2>New Budget</h2>
                    <p>Create a new budget to track your expenses.</p>
                </div>
                <div class="option" onclick="window.location.href='SS_EditBudget.php?username=<?php if(!empty($_GET['username'])) { $username = $_GET['username']; echo urlencode($username); } ?>';">
                    <h2>Edit Budget</h2>
                    <p>Modify your existing budgets.</p>
                </div>
                <div class="option" onclick="window.location.href='SS_Logout.php?username=<?php if(!empty($_GET['username'])) { $username = $_GET['username']; echo urlencode($username); } ?>';">
                    <h2>Logout</h2>
                    <p>Sign out of your account.</p>
                </div>
            </section>
        </main>
        <footer>
            <p>SpendSmart &copy; 2024. All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>
