<?php
    session_start();

    require('connect.php');

    if(!isset($_GET['id']))
    {
        $query = "SELECT * FROM employeecategory";

        $statement = $db->prepare($query);

        $statement->execute();
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
    <link rel="stylesheet" href="table.css">
    <title>New Employee Category</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="admincategory.php">Best Cleaners Solutions - Categories</a>   
            </h1>
        </div>
        <?php include('adminmenubar.php'); ?>
        <div>
            <?php while($row = $statement->fetch()): ?>
                <div>
                    <table id="category-table">
                        <tr>
                            <th class="small-col">Category ID</th>
                            <th>Category Name</th>
                        </tr>
                        <tr>
                            <td class="small-col"><?= $row['category_id'] ?></td>
                            <td><a href="editcategory.php?id=<?= $row['category_id'] ?>"><?= $row['category_name'] ?></a></td>
                        </tr>
                    </table>       
                </div>
            <?php endwhile ?>            
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