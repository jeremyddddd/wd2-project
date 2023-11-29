<?php
    session_start();

    require('connect.php');

    $error_message = '';

    if (isset($_POST['username']) && isset($_POST['password']))
    {
        $username  = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password  = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "SELECT * FROM login WHERE username = :username";

        $statement = $db->prepare($query);
        $statement->bindValue(':username', $username);

        $statement->execute();

        $user = $statement->fetch();

        if ($user && password_verify($password, $user['password']) && $user['role'] == 'admin')
        {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];

            echo '<script type="text/javascript">'.       
                    'alert("Login successful.");'.
                    'window.location.href = "/wd2/Project/wd2-project/admin/adminemployees.php";'.
                 '</script>';
        }
        else if ($user && password_verify($password, $user['password']) && $user['role'] == 'customer')
        {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];

            echo '<script type="text/javascript">'.       
                    'alert("Login successful.");'.
                    'window.location.href = "/wd2/Project/wd2-project/customer/customerhome.php";'.
                 '</script>';          
        }
        else
        {
            $error_message = 'Invalid name or password';
        }

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Best Cleaner's Login page</title>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error_message != ''): ?>
            <p class='error'><?= $error_message ?></p>
        <?php endif ?>
        <form action="login.php" method="post">
            <input type="text" name="username" placeholder="Username" value="<?= isset($username) ? $username : '' ?>" required>
            <input type="password" name="password" placeholder="Password" value="<?= isset($password) ? $password : '' ?>" required>
            <button type="submit">Login</button>
        </form>
        <p>or</p>
        <p><a href="register.php">Create a new account</a></p>
    </div>    
</body>
</html>