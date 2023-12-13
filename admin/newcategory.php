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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>New Employee Category</title>
</head>
<body>
    <div id="wrapper" class="container">
        <div id="header" class="text-center">
            <h1 class="my-4">
                <a href="admincategory.php" class="text-decoration-none">Best Cleaners Solutions - New Category</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="admincategory.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div id="login_edit">
            <form action="newcategory.php" method="post" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
                    <legend>New Category</legend>
                    <div class="form-group">
                        <label for="name" class="form-label">Category Name:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="form-group text-center mt-3">
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