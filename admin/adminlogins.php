<?php
    session_start();

    require('connect.php');

    if (isset($_GET['logout']) && $_GET['logout'] == 'true') 
    {
        session_destroy();
        header("Location: /wd2/Project/wd2-project/public/Login.php");
        exit;
    }

    if (!isset($_GET['id']))
    {        
        $query = "SELECT * FROM login ORDER BY account_id DESC";

        $statement = $db->prepare($query);

        $statement->execute();     
    }
    else
    {
        header("Location: adminlogins.php");
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
    <title>Best Clearns Solutions - Login Management</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="adminlogins.php">Best Cleaners Solutions - Logins</a>   
            </h1>
        </div>
        <?php include('adminmenubar.php'); ?>
        <div>
            <?php while($row = $statement->fetch()): ?>
                <div>
                    <h2 class = "name_header">
                        <a href=<?="editlogin.php?id={$row['account_id']}" ?>><?= $row['username'] ?></a>
                    </h2>
                    <table>
                        <tr>
                            <th class="small-col">Account ID</th>
                            <th>Username</th>
                            <th>email</th>
                        </tr>
                        <tr>
                            <td class="small-col"><?= $row['account_id'] ?></td>
                            <td><?= $row['username'] ?></td>
                            <td><?= $row['email'] ?></td>
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