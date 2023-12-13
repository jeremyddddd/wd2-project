<?php
    session_start();

    require('connect.php');

    $password_error = '';
    $username_error = '';
    $email_error = '';

    

    if ($_POST &&
        isset($_POST['account_id']) &&
        isset($_POST['username']) &&
        isset($_POST['email']) ||
        isset($_POST['password']) &&
        isset($_POST['confirm_password']))
    {
        $id  = filter_input(INPUT_POST, 'account_id', FILTER_SANITIZE_NUMBER_INT);
        $username  = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirm_password = filter_input(INPUT_POST, 'confirm_password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

        $checkUsernameQuery = "SELECT * FROM login WHERE username = :username AND account_id != :id";
        $checkUsernameStatement = $db->prepare($checkUsernameQuery);
        $checkUsernameStatement->bindValue(':username', $username);
        $checkUsernameStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $checkUsernameStatement->execute();
        $existingUsername = $checkUsernameStatement->fetch();
    
        $checkEmailQuery = "SELECT * FROM login WHERE email = :email AND account_id != :id";
        $checkEmailStatement = $db->prepare($checkEmailQuery);
        $checkEmailStatement->bindValue(':email', $email);
        $checkEmailStatement->bindValue(':id', $id, PDO::PARAM_INT);
        $checkEmailStatement->execute();
        $existingEmail = $checkEmailStatement->fetch();

        if (isset($_POST['delete'])) 
        {
            $query = "DELETE FROM login WHERE account_id = :id LIMIT 1";
    
            $statement = $db->prepare($query);
    
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
    
            $statement->execute();
    
            header("Location: adminlogins.php");
            exit;
        } 
        else if ($username && $existingUsername && $username == $existingUsername['username']) 
        {
            $username_error = 'Username is already taken. Please choose a different username.';
        } 
        else if ($email && $existingEmail && $email == $existingEmail['email']) 
        {
            $email_error = 'Email is already taken. Please choose a different email.';
        } 
        else if ($_POST && isset($id) && isset($username) && isset($email) && empty($password) && empty($confirm_password)) 
        {
            $query = "UPDATE login SET account_id = :id, username = :username, email = :email WHERE account_id = :id";
    
            $statement = $db->prepare($query);
    
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':email', $email);
    
            if ($statement->execute()) 
            {
                echo '<script type="text/javascript">' .
                        'alert("Account information has been updated.");' .
                        'window.location.href = "adminlogins.php";' .
                     '</script>';
            }
        } 
        else if ($_POST && isset($id) && isset($username) && isset($email) && isset($password) && isset($confirm_password)) 
        {
            if ($password == $confirm_password) 
            {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
                $query = "UPDATE login SET account_id = :id, username = :username, email = :email, password = :password WHERE account_id = :id";
    
                $statement = $db->prepare($query);
    
                $statement->bindValue(':id', $id, PDO::PARAM_INT);
                $statement->bindValue(':username', $username);
                $statement->bindValue(':email', $email);
                $statement->bindValue(':password', $hashedPassword);
    
                if ($statement->execute()) 
                {
                    echo '<script type="text/javascript">' .
                            'alert("Account information has been updated.");' .
                            'window.location.href = "adminlogins.php";' .
                         '</script>';
                }
            } 
            else 
            {
                $password_error = 'Password does not match';
            }
        }
    }
    else if (isset($_GET['id']))
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT * FROM login WHERE account_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        
        $statement->execute();

        $user = $statement->fetch();

        if (empty($user['account_id']))
        {
            header("Location: adminlogins.php");
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
    <title>Edit Login</title>
</head>
<body>
    <div id="wrapper" class="container">
        <div id="header" class="text-center">
            <h1 class="my-4">
                <a href="adminlogins.php" class="text-decoration-none">Best Cleaners Solutions - Edit Login</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="adminlogins.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div id="login_edit">
            <form action="editlogin.php" method="post" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
                    <legend>Login Details</legend>
                    <div class="form-group">
                        <label class="form-label">Account ID:</label>
                        <span class="form-control-plaintext"><?= isset($id) ? $id : $user['account_id'] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" name="username" id="username" class="form-control" value="<?= isset($username) ? $username : $user['username'] ?>" required>
                        <?php if ($username_error != ''): ?>
                            <p class='error text-danger'><?= $username_error ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label">Change Password:</label>
                        <input type="password" name="password" id="password" class="form-control" value="">
                    </div>
                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password:</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" value="">
                        <?php if ($password_error != ''): ?>
                            <p class='error text-danger'><?= $password_error ?></p>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= isset($email) ? $email : $user['email'] ?>" required>
                        <?php if ($email_error != ''): ?>
                            <p class='error text-danger'><?= $email_error ?></p>
                        <?php endif ?>
                    </div>
                    <input type="hidden" name="account_id" value="<?= isset($id) ? $id : $user['account_id'] ?>">
                    <div class="form-group text-center">
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you wish to delete this user?')">Delete User</button>
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