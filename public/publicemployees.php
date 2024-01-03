<?php

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
            header("Location: publicemployees.php");
            exit;
        }
    } 
    else
    {
        header("Location: publicemployees.php");
    }

?>

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
    <div id="wrapper" class="container">
        <div id="header" class="text-center">
            <h1 class="my-4">
                <a href="publicemployees.php" class="text-decoration-none">Best Cleaners Solutions - Employees</a>
            </h1>
        </div>
        <div class="mb-3">
            <a href="login.php" class="btn btn-link text-decoration-none">Login or Create account</a>
        </div>
        <div id="all_employees">
            <div class="sorting-options mb-3">
                <label for="sort" class="form-label">Sort:</label>
                <select name="sort" id="sort" class="form-select" onchange="location = this.value;">
                    <option>Select</option>
                    <option value="publicemployees.php?sort=lastname">Last Name (A-Z)</option>
                    <option value="publicemployees.php?sort=id">Employee ID (Least to Greatest)</option>
                </select>
                <?php if (isset($sort) && $sort == "lastname"): ?>
                    <h3>
                        Sorted by: Last name (A-Z)
                    </h3>
                <?php elseif (isset($sort) && $sort == "id"): ?>
                    <h3>
                        Sorted by: Employee ID (Least to Greatest)
                    </h3>
                <?php endif ?>
            </div>
            <div class="search-form mb-3">
                <form action="publicemployees.php" method="GET" class="d-flex">
                    <label for="search" class="form-label me-2">Search:</label>
                    <input type="text" name="search" id="search" class="form-control me-2" placeholder="Enter search keyword">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <?php while($row = $statement->fetch()): ?>
                <div>
                    <h2 class="employee_header">
                        <a href=<?="commentemployee.php?id={$row['employee_id']}" ?> class="text-decoration-none"><?= $row['first_name'] . ' ' . $row['last_name']?></a>
                    </h2>
                    <table class="table">
                        <tr>
                            <th class="small-col">Employee ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Phone</th>
                            <th class="wide-col">Email</th>
                        </tr>
                        <tr>
                            <td class="small-col"><?= $row['employee_id'] ?></td>
                            <td><?= $row['first_name'] ?></td>
                            <td><?= $row['last_name'] ?></td>
                            <td><?= $row['phone'] ?></td>
                            <td class="wide-col"><?= $row['email'] ?></td>
                        </tr>
                    </table>       
                </div>
            <?php endwhile ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>        
</body>
</html>