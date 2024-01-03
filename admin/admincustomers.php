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
    else 
    {
        header("Location: admincustomers.php");
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
    <title>Best Clearns Solutions - Customer Management</title>
</head>
<body>
    <div id="wrapper" class="container-fluid">
        <div id="header" class="row justify-content-center">
            <h1 class="my-4">
                <a href="admincustomers.php" class="text-decoration-none">Best Cleaners Solutions - Customers</a>   
            </h1>
        </div>
        <?php include('adminmenubar.php'); ?>
        <div class="search-form my-3">
            <form action="admincustomers.php" method="GET" class="form-inline">
                <label for="search" class="mr-2">Search:</label>
                <input type="text" name="search" id="search" class="form-control mr-2" placeholder="Enter search keyword">
                <label for="column" class="mr-2">Search by Column:</label>
                <select name="column" id="column" class="form-control mr-2">
                    <option value="all">All Columns</option>
                    <?php foreach ($allowedColumns as $columnName => $displayName): ?>
                        <option value="<?= $columnName ?>"><?= $displayName ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div>
            <?php while($row = $statement->fetch()): ?>
                <div class="mb-3">
                    <h2 class="name_header">
                        <a href="editcustomer.php?id=<?= $row['customer_id'] ?>" class="text-decoration-none"><?= $row['name'] ?></a>
                    </h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Customer ID</th>
                                    <th>Company name</th>
                                    <th>Address</th>
                                    <th class="phone-col">Phone</th>
                                    <th class="email-col">Email</th>
                                    <th>Registered Date</th>
                                    <th class="blacklist-col">Blacklisted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?= $row['customer_id'] ?></td>
                                    <td><?= $row['name'] ?></td>
                                    <td><?= $row['address'] ?></td>
                                    <td class="phone-col"><?= $row['phone'] ?></td>
                                    <td class="email-col"><?= $row['email'] ?></td>
                                    <td ><?= $row['registered_date'] ?></td>
                                    <td class="blacklist-col"><?= $row['blacklist'] ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endwhile ?>
            <?php if ($totalResults > $resultsPerPage): ?>
                <div class="pagination">
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?search=<?= $search ?>&column=<?= $column ?>&page=<?= ($page - 1) ?>">Previous</a>
                                </li>
                            <?php endif ?>
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                    <a class="page-link" href="?search=<?= $search ?>&column=<?= $column ?>&page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor ?>
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?search=<?= $search ?>&column=<?= $column ?>&page=<?= ($page + 1) ?>">Next</a>
                                </li>
                            <?php endif ?>
                        </ul>
                    </nav>
                </div>
            <?php endif ?>
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