<?php
    session_start();

    require('connect.php');

    if (isset($_GET['logout']) && $_GET['logout'] == 'true') 
    {
        session_destroy();
        header("Location: /wd2/Project/wd2-project/public/Login.php");
        exit;
    }

?>

<?php if(isset($_SESSION['username']) && $_SESSION['role'] == 'customer'):?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Customer Home</title>
</head>
<body>
    <form action="customerhome.php" method="post">
        <h1>Welcome</h1>
        <a href="?logout=true">Logout</a>
    </form>
</body>
</html>
<?php else: ?>
    <script>
        alert('Authorized access only');
        window.location.replace("/wd2/Project/wd2-project/public/Login.php");
    </script>
<?php endif ?>