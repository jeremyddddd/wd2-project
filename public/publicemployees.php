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
    <title>Best Clearns Solutions - Employee Management</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="publicemployees.php">Best Cleaners Solutions - Employees</a>   
            </h1>
        </div>
        <ul id="menu">
        </ul>
        <div id="all_employees">
            <div class="sorting-options">
                <label for="sort">Sort:</label>
                <select name="sort" id="sort" onchange="location = this.value;">
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
            <div class="search-form">
                <form action="publicemployees.php" method="GET">
                    <label for="search">Search:</label>
                    <input type="text" name="search" id="search" placeholder="Enter search keyword">
                    <button type="submit">Search</button>
                </form>
            </div>
            <?php while($row = $statement->fetch()): ?>
                <div>
                    <h2 class = "employee_header">
                        <a href=<?="commentemployee.php?id={$row['employee_id']}" ?>><?= $row['first_name'] . ' ' . $row['last_name']?></a>
                    </h2>
                    <table>
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
</body>
</html>