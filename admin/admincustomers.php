<?php
    require('C:\xampp\htdocs\wd2\Project\connect.php');

    if(!isset($_GET['id']))
    {
        $query = "SELECT * FROM customers ORDER BY customer_id DESC";

        $statement = $db->prepare($query);

        $statement->execute();
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
                <a href="adminemployees.php">Best Cleaners Solutions - Customers</a>   
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="adminemployees.php">View employees</a>
            </li>
            <li>
                <a href="admincustomers.php">View customers</a>
            </li>
            <li>
                <a href="newemployee.php">Insert new employee</a>
            </li>
            <li>
                <a href="newcustomer.php">Insert new customer</a>
            </li>
        </ul>
        <div>
            <?php while($row = $statement->fetch()): ?>
                <div>
                    <h2 class = "employee_header">
                        <a href=<?="editcustomer.php?id={$row['customer_id']}" ?>><?= $row['name'] ?></a>
                    </h2>
                    <table>
                        <tr>
                            <th class="small-col">Customer ID</th>
                            <th>Company name</th>
                            <th class="wide-col">Address</th>
                            <th class="small-col">Phone</th>
                            <th class="wide-col">Email</th>
                            <th class="wide-col">Registered Date</th>
                            <th class="small-col">Blacklisted</th>
                        </tr>
                        <tr>
                            <td class="small-col"><?= $row['customer_id'] ?></td>
                            <td><?= $row['name'] ?></td>
                            <td class="wide-col"><?= $row['address'] ?></td>
                            <td class="small-col"><?= $row['phone'] ?></td>
                            <td class="wide-col"><?= $row['email'] ?></td>
                            <td class="wide-col"><?= $row['registered_date'] ?></td>
                            <td class="small-col"><?= $row['blacklist'] ?></td>
                        </tr>
                    </table>       
                </div>
            <?php endwhile ?>            
        </div>
    </div>
</body>
</html>