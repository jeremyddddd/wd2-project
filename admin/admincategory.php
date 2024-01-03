<?php
    session_start();

    require('connect.php');

    if(!isset($_GET['id']))
    {
        $query = "SELECT * FROM employeecategory";

        $statement = $db->prepare($query);

        $statement->execute();
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="table.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>New Employee Category</title>
</head>
<body>
<div id="wrapper" class="container-fluid">
        <div id="header" class="row justify-content-center">
            <h1 class="my-4">
                <a href="admincategory.php" class="text-decoration-none">Best Cleaners Solutions - Categories</a>   
            </h1>
        </div>
        <?php include('adminmenubar.php'); ?>
        <div>
            <?php while($row = $statement->fetch()): ?>
                <div class="mb-3">
                    <table id="category-table" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="small-col">Category ID</th>
                                <th>Category Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="small-col"><?= $row['category_id'] ?></td>
                                <td><a href="editcategory.php?id=<?= $row['category_id'] ?>" class="text-decoration-none"><?= $row['category_name'] ?></a></td>
                            </tr>
                        </tbody>
                    </table>       
                </div>
            <?php endwhile ?>            
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