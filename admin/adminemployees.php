<?php
    session_start();

    require('connect.php');

    $allowedColumns = [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'email' => 'Email',
        'phone' => 'Phone'
    ];

    $totalResults = 0;
    $queryCount = "";
    $resultsPerPage = 2;
    $query = "";

    $categoryQuery = "SELECT * FROM employeecategory";
    $categoryStatement = $db->prepare($categoryQuery);
    $categoryStatement->execute();
    $categories = $categoryStatement->fetchAll();

    if (isset($_GET['logout']) && $_GET['logout'] == 'true') 
    {
        session_destroy();
        header("Location: /wd2/Project/wd2-project/public/Login.php");
        exit;
    }

    if (isset($_GET['category']) && $_GET['category'] === '') 
    {
        header("Location: adminemployees.php");
        exit;
    }
    else if (isset($_GET['categorySearch']) && $_GET['categorySearch'] != '') 
    {
        $categorySearchId = filter_input(INPUT_GET, 'categorySearch', FILTER_SANITIZE_NUMBER_INT);
        $searchKeyword = filter_input(INPUT_GET, 'keyword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
        $query = "SELECT e.*, ec.category_name 
                  FROM employees e 
                  LEFT JOIN employeecategory ec ON e.category_id = ec.category_id
                  WHERE e.category_id = :category_id AND (
                      e.first_name LIKE :keyword OR 
                      e.last_name LIKE :keyword OR 
                      e.phone LIKE :keyword OR 
                      e.email LIKE :keyword)
                  ORDER BY e.employee_id DESC";
    
        $statement = $db->prepare($query);
        $statement->bindValue(':category_id', $categorySearchId);
        $statement->bindValue(':keyword', '%' . $searchKeyword . '%');
        $statement->execute();
    }
    else if (isset($_GET['category']) && $_GET['category'] != '') 
    {
        $categoryId = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT e.*, ec.category_name 
                  FROM employees e 
                  LEFT JOIN employeecategory ec ON e.category_id = ec.category_id
                  WHERE e.category_id = :category_id
                  ORDER BY e.employee_id DESC";

        $statement = $db->prepare($query);
        $statement->bindValue(':category_id', $categoryId);
        $statement->execute();
    }
    else if (!isset($_GET['id']) && !isset($_GET['search']) && !isset($_GET['sort']))
    {        
        $query = "SELECT e.*, ec.category_name 
                    FROM employees e 
                    LEFT JOIN employeecategory ec ON e.category_id = ec.category_id
                    ORDER BY e.employee_id DESC";

        $statement = $db->prepare($query);

        $statement->execute();     
    }
    else if (isset($_GET['sort'])) 
    {
        $baseQuery = "SELECT e.*, ec.category_name FROM employees e LEFT JOIN employeecategory ec ON e.category_id = ec.category_id";
        
        $sort = filter_input(INPUT_GET, 'sort', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
        switch ($sort) {
            case "id":
                $orderBy = " ORDER BY e.employee_id ASC";
                break;
            case "lastname":
                $orderBy = " ORDER BY e.last_name ASC";
                break;
            case "startdate":
                $orderBy = " ORDER BY e.start_date ASC";
                break;
            default:
                $orderBy = " ORDER BY e.employee_id DESC";
        }
    
        $query = $baseQuery . $orderBy;
        $statement = $db->prepare($query);
        $statement->execute();
    }
    else if (isset($_GET['search'])) 
    {
        $search = filter_input(INPUT_GET, 'search', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $column = filter_input(INPUT_GET, 'column', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    
        if (!array_key_exists($column, $allowedColumns) && $column !== 'all') {
            header("Location: adminemployees.php");
            exit;
        }

        if (empty($search)) 
        {
            header("Location: adminemployees.php");
            exit;
        }    
        else 
        {
            $searchTerm = '%' . $search . '%';
            $queryBase = " FROM employees e LEFT JOIN employeecategory ec ON e.category_id = ec.category_id WHERE ";
            $queryCondition = $column === 'all' ?
                "(e.first_name LIKE :search OR e.last_name LIKE :search OR e.phone LIKE :search OR e.email LIKE :search)" : 
                "e.$column LIKE :search";
    
            $queryCount = "SELECT COUNT(*)" . $queryBase . $queryCondition;
            $statementCount = $db->prepare($queryCount);
            $statementCount->bindValue(':search', $searchTerm);
            $statementCount->execute();
            $totalResults = $statementCount->fetchColumn();
    
            $totalPages = ceil($totalResults / $resultsPerPage);
    
            $page = isset($_GET['page']) ? max(1, filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT)) : 1;
            $offset = ($page - 1) * $resultsPerPage;
            
            $query = "SELECT e.*, ec.category_name" . $queryBase . $queryCondition;
            $query .= " ORDER BY e.employee_id DESC"; 
            $query .= " LIMIT :limit OFFSET :offset";
    
            $statement = $db->prepare($query);
            $statement->bindValue(':search', $searchTerm);
            $statement->bindValue(':limit', $resultsPerPage, PDO::PARAM_INT);
            $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
            $statement->execute();
        }
    } 
    else
    {
        header("Location: adminemployees.php");
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
    <title>Best Clearns Solutions - Employee Management</title>
</head>
<body>
    <div id="wrapper" class="container-fluid">
        <div id="header" class="row justify-content-center">
            <h1 class="my-4">
                <a href="adminemployees.php" class="text-decoration-none">Best Cleaners Solutions - Employees</a>   
            </h1>
        </div>
        <?php include('adminmenubar.php'); ?>
        <div id="all_employees" class="mt-3">
            <div class="sorting-options mb-3">
                <form class="form-inline">
                    <label for="sort" class="mr-2">Sort:</label>
                    <select name="sort" id="sort" class="form-control" onchange="location = this.value;">
                        <option>Select</option>
                        <option value="adminemployees.php?sort=id">Employee ID (Least to Greatest)</option>
                        <option value="adminemployees.php?sort=lastname">Last Name (A-Z)</option>
                        <option value="adminemployees.php?sort=startdate">Start Date (Oldest to Newest)</option>
                    </select>
                </form>
                <?php if (isset($sort) && $sort == "id"): ?>
                    <h3>
                        Sorted by: Employee ID (Least to Greatest)
                    </h3>
                <?php elseif (isset($sort) && $sort == "lastname"): ?>
                    <h3>
                        Sorted by: Last name (A-Z)
                    </h3>
                <?php elseif (isset($sort) && $sort == "startdate"): ?>
                    <h3>
                        Sorted by: Start Date (Oldest to Newest)
                    </h3>
                <?php endif ?>
            </div>
            <div class="category-menu mb-3">
                <form class="form-inline">
                    <label for="category" class="mr-2">Category:</label>
                    <select name="category" id="category" class="form-control" onchange="window.location.href = 'adminemployees.php?category=' + this.value;">
                        <option value='' <?= (!isset($_GET['category']) || $_GET['category'] === '') ? 'selected' : '' ?>>All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['category_id'] ?>" <?= (isset($_GET['category']) && $_GET['category'] == $category['category_id']) ? 'selected' : '' ?>>
                                <?= $category['category_name'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </form>
            </div>
            <div class="search-form mb-3">
                <form action="adminemployees.php" method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="search">Search:</label>
                        <input type="text" name="search" id="search" class="form-control ml-2" placeholder="Enter search keyword">
                    </div>
                    <div class="form-group mr-2">
                        <label for="column">Search by Column:</label>
                        <select name="column" id="column" class="form-control ml-2">
                            <option value="all">All Columns</option>
                            <?php foreach ($allowedColumns as $columnName => $displayName): ?>
                                <option value="<?= $columnName ?>"><?= $displayName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <div class="category-search-form mb-3">
                <form action="adminemployees.php" method="GET" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="keyword">Keyword:</label>
                        <input type="text" name="keyword" id="keyword" class="form-control ml-2" placeholder="Enter keyword">
                    </div>
                    <div class="form-group">
                        <label for="categorySearch">Category:</label>
                        <select name="categorySearch" id="categorySearch" class="form-control ml-2">
                            <option value="">Select Category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary ml-2">Search in Category</button>
                </form>
            </div>
            <?php while($row = $statement->fetch()): ?>
                <div class="employee-entry mb-3">
                    <h2 class = "name_header">
                        <a href="<?= "editemployee.php?id=" . $row['employee_id'] ?>" class="text-decoration-none"><?= $row['first_name'] . ' ' . $row['last_name']?></a>
                    </h2>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="small-col">Employee ID</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Phone</th>
                                    <th class="wide-col">Email</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class="small-col">Category</th>
                                    <th class="small-col">Blacklisted</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="small-col"><?= $row['employee_id'] ?></td>
                                    <td><?= $row['first_name'] ?></td>
                                    <td><?= $row['last_name'] ?></td>
                                    <td><?= $row['phone'] ?></td>
                                    <td class="wide-col"><?= $row['email'] ?></td>
                                    <td><?= $row['start_date'] ?></td>
                                    <td><?= $row['end_date'] ?></td>
                                    <td class="small-col"><?= $row['category_name'] ?></td>
                                    <td class="small-col"><?= $row['blacklist'] ?></td>
                                </tr>
                            </tbody>
                        </table>       
                    </div>
                </div>
            <?php endwhile ?>
            <div class="pagination mb-3">
                <?php if ($totalResults > $resultsPerPage): ?>
                    <nav aria-label="...">
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
                <?php endif ?>
            </div>
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