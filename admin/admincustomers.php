<?php
    session_start();

    require('connect.php');

    if (isset($_GET['logout']) && $_GET['logout'] == 'true') 
    {
        session_destroy();
        header("Location: /wd2/Project/wd2-project/public/Login.php");
        exit;
    }

    if(!isset($_GET['id']) && !isset($_GET['search']))
    {
        $query = "SELECT * FROM customers ORDER BY customer_id DESC";

        $statement = $db->prepare($query);

        $statement->execute();
    }
    else if (isset($_GET['search'])) 
    {
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        if (!empty($search)) 
        {
            $query = "SELECT * FROM customers WHERE 
                      name LIKE :search OR 
                      address LIKE :search OR
                      phone LIKE :search OR 
                      email LIKE :search";
    
            $statement = $db->prepare($query);
            $statement->bindValue(':search', '%' . $search . '%');
            $statement->execute();
        } 
        else 
        {
            header("Location: admincustomers.php");
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
    <link rel="stylesheet" href="table.css">
    <title>Best Clearns Solutions - Customer Management</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="adminemployees.php">Best Cleaners Solutions - Customers</a>   
            </h1>
        </div>
        <?php include('adminmenubar.php'); ?>
        <div class="search-form">
            <form action="admincustomers.php" method="GET">
                <label for="search">Search:</label>
                <input type="text" name="search" id="search" placeholder="Enter search keyword">
                <button type="submit">Search</button>
            </form>
        </div>
        <div>
            <?php while($row = $statement->fetch()): ?>
                <div>
                    <h2 class = "name_header">
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
<?php else: ?>
    <script>
        alert('Authorized access only');
        window.location.replace("/wd2/Project/wd2-project/public/Login.php");
    </script>
<?php endif ?>