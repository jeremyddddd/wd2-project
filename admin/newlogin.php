<?php
    session_start();

    require('connect.php');

    $username_error = '';
    $email_error = '';
    $password_error = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') 
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);


        $checkUsernameQuery = "SELECT * FROM login WHERE username = :username";
        $checkUsernameStatement = $db->prepare($checkUsernameQuery);
        $checkUsernameStatement->bindValue(':username', $username);
        $checkUsernameStatement->execute();
        $existingUsername = $checkUsernameStatement->fetch();

        $checkEmailQuery = "SELECT * FROM login WHERE email = :email";
        $checkEmailStatement = $db->prepare($checkEmailQuery);
        $checkEmailStatement->bindValue(':email', $email);
        $checkEmailStatement->execute();
        $existingEmail = $checkEmailStatement->fetch();

        if ($existingUsername) 
        {
            $username_error = 'Username is already taken. Please choose a different username.';
        } 
        elseif ($existingEmail) 
        {
            $email_error = 'Email is already taken. Please choose a different email.';
        } 
        elseif ($password != $confirm_password) 
        {
            $password_error = 'Password does not match';
        } 
        else 
        {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $insertQuery = "INSERT INTO login (username, password, email, role) VALUES (:username, :password, :email, 'customer')";
            $insertStatement = $db->prepare($insertQuery);
            $insertStatement->bindValue(':username', $username);
            $insertStatement->bindValue(':password', $hashedPassword);
            $insertStatement->bindValue(':email', $email);

            if ($insertStatement->execute()) 
            {
                echo '<script type="text/javascript">' .
                        'alert("New user has been added.");' .
                        'window.location.href = "adminlogins.php";' .
                    '</script>';
                exit;
            }
        }
    }
?>

<?php if(isset($_SESSION['username']) && $_SESSION['role'] == 'admin'):?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>New Login</title>
</head>
<body>
<div id="wrapper" class="container">
        <div id="header" class="text-center">
            <h1 class="my-4">
                <a href="adminlogins.php" class="text-decoration-none">Best Cleaners Solutions - New Login</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="adminlogins.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div id="login_edit">
            <form action="newlogin.php" method="post" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
                    <legend>New User</legend>
                    <div class="form-group">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?= isset($username) ? $username : '' ?>" required>
                        <?php if ($username_error != ''): ?>
                            <p class='error text-danger'><?= $username_error ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        <?php if ($password_error != ''): ?>
                            <p class='error text-danger'><?= $password_error ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                        <?php if ($email_error != ''): ?>
                            <p class='error text-danger'><?= $email_error ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-group text-center">
                        <input type="submit" name="register" value="Register" class="btn btn-primary">
                    </div>
                </fieldset>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>        
</body>
</html>
<?php else: ?>
    <script>
        alert('Authorized access only');
        window.location.replace("/wd2/Project/wd2-project/public/Login.php");
    </script>
<?php endif ?>