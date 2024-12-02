<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Confirmation</title>
    <link href="SS_Logout.css" rel="stylesheet" type="text/css">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">SpendSmart</h1>
        </header>
        <main>
            <div class="confirmation-box">
                <h2>Are you sure you want to logout, <?php  if (!empty($_GET['username'])) {
                                                                $username = $_GET['username'];
                                                                echo $username; 
                                                            } ?>?</h2>
                <div class="button-group">
                    <a href="SS_Home.php?username=<?php echo urlencode($username); ?>" class="btn btn-return">
                        Return to Home 
                    </a>
                    <a href="SS_Login.php" class="btn btn-leave">
                        Leave
                    </a>
                </div>
            </div>
        </main>
        <footer>
            <p>SpendSmart &copy; 2024. All Rights Reserved.</p>
        </footer>
    </div>
</body>
</html>
