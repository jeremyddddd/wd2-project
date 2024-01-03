<?php
    session_start();

    require('connect.php');

    if (isset($_POST['id']) && isset($_POST['name']))
    {
        $id  = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $name  = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        
        $query = "UPDATE employeecategory
                  SET category_name = :name
                  WHERE category_id = :id";

        $statement= $db->prepare($query);

        $statement->bindValue(':name', $name);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        if ($statement->execute())
        {
            header("Location: admincategory.php");
            exit;            
        }
    }
    else if (isset($_GET['id']))
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT * FROM employeecategory WHERE category_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        
        $statement->execute();

        $category = $statement->fetch();

        if (empty($category['category_id']))
        {
            header("Location: admincategory.php");
        }        
    }
    else
    {
        header("Location: admincategory.php");
        exit;            
    }
?>

<?php if(isset($_SESSION['username']) && $_SESSION['role'] == 'admin'):?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Edit employee category</title>
</head>
<body>
<body>
    <div id="wrapper" class="container">
        <div id="header" class="text-center">
            <h1 class="my-4">
                <a href="admincategory.php" class="text-decoration-none">Best Cleaners Solutions - Edit Customer</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="admincategory.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div id="customer_edit" class="my-3">
            <form action="editcategory.php" method="post" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
                    <legend class="w-auto px-2">Category Details</legend>
                    <div class="form-group">
                        <label class="form-label">Category ID:</label>
                        <span class="form-control-plaintext"><?=$category['category_id']?></span>
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">Category name:</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?=$category['category_name']?>" required>
                    </div>
                    <input type="hidden" name="id" value="<?= $category['category_id'] ?>">
                    <div class="form-group text-center">
                        <input type="submit" name="update" value="Update" class="btn btn-primary">
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