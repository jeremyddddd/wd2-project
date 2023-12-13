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
            $_SESSION['account_id'] = $user['account_id'];

            echo '<script type="text/javascript">'.       
                    'alert("Login successful.");'.
                    'window.location.href = "/wd2/Project/wd2-project/admin/adminemployees.php";'.
                 '</script>';
        }
        else if ($user && password_verify($password, $user['password']) && $user['role'] == 'customer')
        {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            $_SESSION['account_id'] = $user['account_id'];

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
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Best Cleaner's Login page</title>
</head>
<body>
    <div class="container mt-5">
        <div class="login-container text-center">
            <h2>Login</h2>
            <?php if ($error_message != ''): ?>
                <p class='error text-danger'><?= $error_message ?></p>
            <?php endif ?>
            <form action="login.php" method="post" class="w-50 mx-auto">
                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" value="<?= isset($username) ? $username : '' ?>" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" value="<?= isset($password) ? $password : '' ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="mt-3">or</p>
            <p><a href="register.php">Create a new account</a></p>
            <p><a href="publicemployees.php">View Employees</a></p>
        </div>    
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>           
</body>
</html>