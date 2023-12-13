<?php
    session_start();

    require('connect.php');

    if (!$_GET && !isset($_GET['search']))
    {        
        $query = "SELECT * FROM employees ORDER BY employee_id DESC";

        $statement = $db->prepare($query);

        $statement->execute();     
    }
    else if (isset($_GET['sort']))
    {
        $sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if ($sort == "lastname")
        {
            $query = "SELECT * FROM employees ORDER BY last_name ASC";

            $statement = $db->prepare($query);

            $statement->execute();
        }
        else if ($sort == "id")
        {
            $query = "SELECT * FROM employees ORDER BY employee_id ASC";
    
            $statement = $db->prepare($query);
    
            $statement->execute();
        }
    }
    else if (isset($_GET['search'])) 
    {
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!empty($search)) 
        {
            $query = "SELECT * FROM employees WHERE 
                      first_name LIKE :search OR 
                      last_name LIKE :search OR
                      phone LIKE :search OR 
                      email LIKE :search";
    
            $statement = $db->prepare($query);
            $statement->bindValue(':search', '%' . $search . '%');
            $statement->execute();
        } 
        else 
        {
            header("Location: adminpublicemployees.php");
            exit;
        }
    } 
    else
    {
        header("Location: adminpublicemployees.php");
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
    <title>Best Clearns Solutions - Employee Management</title>
</head>
<body>
<div id="wrapper" class="container-fluid">
        <div id="header" class="row justify-content-center">
            <h1 class="my-4">
                <a href="adminpublicemployees.php" class="text-decoration-none">Best Cleaners Solutions - Employees</a>   
            </h1>
        </div>
        <ul id="menu" class="nav">
            <?php include('adminmenubar.php'); ?>
        </ul>
        <div id="all_employees" class="mt-3">
            <div class="sorting-options mb-3">
                <form class="form-inline">
                    <label for="sort" class="mr-2">Sort:</label>
                    <select name="sort" id="sort" class="form-control" onchange="location = this.value;">
                        <option>Select</option>
                        <option value="adminpublicemployees.php?sort=lastname">Last Name (A-Z)</option>
                        <option value="adminpublicemployees.php?sort=id">Employee ID (Least to Greatest)</option>
                    </select>
                </form>
            </div>
            <div class="search-form mb-3">
                <form action="adminpublicemployees.php" method="GET" class="form-inline">
                    <label for="search" class="mr-2">Search:</label>
                    <input type="text" name="search" id="search" class="form-control mr-2" placeholder="Enter search keyword">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <?php while($row = $statement->fetch()): ?>
                <div class="employee-entry mb-3">
                    <h2 class="employee_header">
                        <a href="comments.php?id=<?= $row['employee_id'] ?>" class="text-decoration-none"><?= $row['first_name'] . ' ' . $row['last_name']?></a>
                    </h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th class="small-col">Employee ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Phone</th>
                                    <th class="wide-col">Email</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="small-col"><?= $row['employee_id'] ?></td>
                                    <td><?= $row['first_name'] ?></td>
                                    <td><?= $row['last_name'] ?></td>
                                    <td><?= $row['phone'] ?></td>
                                    <td class="wide-col"><?= $row['email'] ?></td>
                                </tr>
                            </tbody>
                        </table>       
                    </div>
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