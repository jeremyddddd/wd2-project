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
?>

<?php if(isset($_SESSION['username']) && $_SESSION['role'] == 'admin'):?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Edit employee category</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="admincategory.php">Best Cleaners Solutions - Edit Customer</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="admincategory.php">Home</a>
            </li>
        </ul>
        <div id="customer_edit">
            <form action="editcategory.php" method="post">
                <fieldset>
                    <legend>Category Details</legend>
                    <p>
                        <label>Category ID:</label>
                        <?=$category['category_id']?>
                    </p>
                    <p>
                        <label for="name">Category name:</label>
                        <input type="text" name="name" id="name" value="<?=$category['category_name']?>" required>
                    </p>
                    <input type="hidden" name="id" value=<?= $category['category_id'] ?>>
                    <input type="submit" name="update" value="Update">
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