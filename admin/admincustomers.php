<?php
    session_start();

    require('connect.php');

    $allowedColumns = [
        'name' => 'Company Name',
        'address' => 'Address',
        'email' => 'Email',
        'phone' => 'Phone'
    ];

    $totalResults = 0;
    $queryCount = "";
    $resultsPerPage = 2;

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
        
        $column = filter_input(INPUT_GET, 'column', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        echo $column;

        if (!empty($search)) 
        {
            if ($column === 'all') 
            {
                $queryCount = "SELECT COUNT(*) FROM customers WHERE 
                    name LIKE :search OR 
                    address LIKE :search OR 
                    phone LIKE :search OR 
                    email LIKE :search";
                
                $query = "SELECT * FROM customers WHERE 
                    name LIKE :search OR 
                    address LIKE :search OR 
                    phone LIKE :search OR 
                    email LIKE :search";
            } 
            else 
            {
                $queryCount = "SELECT COUNT(*) FROM customers WHERE $column LIKE :search";
                $query = "SELECT * FROM customers WHERE $column LIKE :search";
            }
    
            $statementCount = $db->prepare($queryCount);
            $statementCount->bindValue(':search', '%' . $search . '%');
            $statementCount->execute();
            $totalResults = $statementCount->fetchColumn();
    
            $totalPages = ceil($totalResults / $resultsPerPage);
    
            $page = isset($_GET['page']) ? max(1, $_GET['page']) : 1;
            $offset = ($page - 1) * $resultsPerPage;
    
            $query .= " LIMIT :limit OFFSET :offset";
    
            $statement = $db->prepare($query);
            $statement->bindValue(':search', '%' . $search . '%');
            $statement->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
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
                <a href="admincustomers.php">Best Cleaners Solutions - Customers</a>   
            </h1>
        </div>
        <?php include('adminmenubar.php'); ?>
        <div class="search-form">
            <form action="admincustomers.php" method="GET">
                <label for="search">Search:</label>
                <input type="text" name="search" id="search" placeholder="Enter search keyword">
                <label for="column">Search by Column:</label>
                <select name="column" id="column">
                    <option value="all">All Columns</option>
                    <?php foreach ($allowedColumns as $columnName => $displayName): ?>
                        <option value="<?= $columnName ?>"><?= $displayName ?></option>
                    <?php endforeach; ?>
                </select>
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
            <?php if ($totalResults > $resultsPerPage): ?>
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?search=<?= $search ?>&column=<?= $column ?>&page=<?= ($page - 1) ?>">Previous</a>
                    <?php endif ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?search=<?= $search ?>&column=<?= $column ?>&page=<?= $i ?>"><?= $i ?></a>
                    <?php endfor ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?search=<?= $search ?>&column=<?= $column ?>&page=<?= ($page + 1) ?>">Next</a>
                    <?php endif ?>
                </div>
            <?php endif ?>            
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