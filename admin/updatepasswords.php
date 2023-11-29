<?php
require('connect.php');

session_start();

if($_SESSION['role'] = 'admin')
{
    $query = "SELECT account_id, password FROM login";
    $statement = $db->query($query);

    while ($row = $statement->fetch()) {

        $hashedPassword = password_hash($row['password'], PASSWORD_DEFAULT);
        
        $updateQuery = "UPDATE login SET password = :password WHERE account_id = :id";
        $updateStatement = $db->prepare($updateQuery);
        $updateStatement->bindValue(':password', $hashedPassword);
        $updateStatement->bindValue(':id', $row['account_id']);
        $updateStatement->execute();
    }

    echo '<script type="text/javascript">'.       
            'alert("Passwords have been successully updated/hashed.");'.
            'window.location.href = "wd2/Project/wd2-project/public/login.php";'.
         '</script>';
}
else
{
    echo '<script type="text/javascript">'.       
            'alert("Authorized access only.");'.
            'window.location.href = "wd2/Project/wd2-project/public/login.php";'.
         '</script>';
}



?>