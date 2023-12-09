<?php
    session_start();

    require('connect.php');

    if(isset($_POST['name']))
    {
        $name  = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "INSERT INTO employeecategory (category_name) VALUES (:category_name)";

        $statement = $db->prepare($query);

        $statement->bindValue(':category_name', $name);

        if($statement->execute())
        {
            header("Location: admincategory.php");
            exit;
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
    <title>New Employee Category</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="admincategory.php">Best Cleaners Solutions - New Category</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="admincategory.php">Home</a>
            </li>
        </ul>
        <div id="login_edit">
            <form action="newcategory.php" method="post">
                <fieldset>
                    <legend>New Category</legend>
                    <p>
                        <label for="name">Category Name:</label>
                        <input type="text" name="name" id="name" required>
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