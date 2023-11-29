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
    <title>New Login</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="adminlogins.php">Best Cleaners Solutions - New Login</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="adminlogins.php">Home</a>
            </li>
        </ul>
        <div id="login_edit">
            <form action="newlogin.php" method="post">
                <fieldset>
                    <legend>New User</legend>
                    <p>
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" value="<?= isset($username) ? $username : '' ?>" required>
                        <?php if ($username_error != ''): ?>
                            <p class='error'><?= $username_error ?></p>
                        <?php endif ?>
                    </p>
                    <p>
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" value="<?= isset($password) ? $password : '' ?>" required>
                    </p>
                    <p>
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" value="<?= isset($confirm_password) ? $confirm_password : '' ?>" required>
                        <?php if ($password_error != ''): ?>
                            <p class='error'><?= $password_error ?></p>
                        <?php endif ?>
                    </p>
                    <p>
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value="<?= isset($email) ? $email : '' ?>" required>
                        <?php if ($email_error != ''): ?>
                            <p class='error'><?= $email_error ?></p>
                        <?php endif ?>
                    </p>
                    <input type="submit" name="register" value="Register">
                </fieldset>
            </form>
        </div>
    </div>
</body>
</html>
<?php else: ?>
    <script>
        alert('Authorized access only');
        window.location.replace("/wd2/Project/wd2-project/public/Login.php");
    </script>
<?php endif ?>