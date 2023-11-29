<?php
    session_start();
    
    require('connect.php');

    $password_error = '';
    $username_error = '';
    $email_error = '';
    
    function generateCaptchaCode($length = 4)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $captchaCode = '';
        for ($i = 0; $i < $length; $i++) 
        {
            $captchaCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $captchaCode;
    }

    if ($_POST &&
        !empty($_POST['username']) &&
        !empty($_POST['password']) && 
        !empty($_POST['confirm_password']) &&
        !empty($_POST['email']))
    {
        $username  = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $captcha_input = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        $checkQuery = "SELECT * FROM login WHERE username = :username";
        $checkStatement = $db->prepare($checkQuery);
        $checkStatement->bindValue(':username', $username);
        $checkStatement->execute();
        $existingUser = $checkStatement->fetch();

        if ($existingUser && $username == $existingUser['username'])
        {
            $username_error = 'Username is already taken. Please choose a different username.';
        }
        else
        {
            if ($password != $confirm_password)
            {
                $password_error = 'Password does not match';
            }
            else
            {
                $checkEmailQuery = "SELECT * FROM login WHERE email = :email";
                $checkEmailStatement = $db->prepare($checkEmailQuery);
                $checkEmailStatement->bindValue(':email', $email);
                $checkEmailStatement->execute();
                $existingEmail = $checkEmailStatement->fetch();

                if ($existingEmail && $email == $existingEmail['email'])
                {
                    $email_error = 'Email is already taken. Please choose a different email.';
                }
                else
                {
                    if (empty($captcha_input) || strtoupper($captcha_input) !== $_SESSION['captcha']) 
                    {
                        $captcha_error = 'CAPTCHA code is incorrect. Please try again.';
                    }
                    else
                    {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                        $query = "INSERT INTO login (username, password, email, role) VALUES (:username, :password, :email, 'customer')";

                        $statement = $db->prepare($query);
                        $statement->bindValue(':username', $username);
                        $statement->bindValue(':password', $hashedPassword);
                        $statement->bindValue(':email', $email);

                        if($statement->execute())
                        {
                            $error_message = ''; 
                            echo '<script type="text/javascript">'.       
                                    'alert("Registration successful. You can now log in.");'.
                                    'window.location.href = "login.php";'.
                                '</script>';
                        }
                        else
                        {
                            echo '<script type="text/javascript">'.       
                                    'alert("There has been an error. Please try again.");'.
                                    'window.location.href = "login.php";'.
                                '</script>';
                        }
                    }
                }
            }
        }
    }

    $captcha_code = generateCaptchaCode();
    $_SESSION['captcha'] = strtoupper($captcha_code);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Register Account</title>
</head>
<body>
    <div class="registration-container">
        <h2>Register</h2>
        <form action="register.php" method="post">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" value="<?= isset($username) ? $username : '' ?>" required>
                <?php if ($username_error != ''): ?>
                    <p class='error'><?= $username_error ?></p>
                <?php endif ?>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" value="<?= isset($password) ? $password : '' ?>" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" value="<?= isset($confirm_password) ? $confirm_password : '' ?>" required>
                <?php if ($password_error != ''): ?>
                    <p class='error'><?= $password_error ?></p>
                <?php endif ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= isset($email) ? $email : '' ?>" required>
                <?php if ($email_error != ''): ?>
                    <p class='error'><?= $email_error ?></p>
                <?php endif ?>
            </div>

            <div class="form-group">
                <label for="captcha">CAPTCHA:</label>
                <img src="captcha_image.php" alt="CAPTCHA Image">
                <input type="text" id="captcha" name="captcha" placeholder="Enter CAPTCHA code" required>
                <?php if (isset($captcha_error)): ?>
                    <p class='error'><?= $captcha_error ?></p>
                <?php endif ?>
            </div>

            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>