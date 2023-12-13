<?php
    if (isset($_GET['logout']) && $_GET['logout'] == 'true') 
    {
        session_destroy();
        header("Location: /wd2/Project/wd2-project/public/Login.php");
        exit;
    }
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand">Admin Panel</a>
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="adminemployees.php">View employees</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admincategory.php">View employee categories</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admincustomers.php">View customers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="adminlogins.php">View logins</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="adminpublicemployees.php">View comments</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="newemployee.php">Insert new employee</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="newcategory.php">Insert new employee category</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="newcustomer.php">Insert new customer</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="newlogin.php">Insert new user</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?logout=true">Logout</a>
            </li>
        </ul>
    </div>
</nav>