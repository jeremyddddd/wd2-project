<?php

    require('connect.php');

    if (isset($_GET['id']))
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT *
                  FROM employees
                  WHERE employee_id = :id";

        $statement = $db->prepare($query);

        $statement->bindValue(':id', $id, PDO::PARAM_INT);

        $statement->execute();

        $employee = $statement->fetch();

        if (empty($employee['employee_id']))
        {
            header("Location: publicemployees.php");
            exit;
        }

        $commentquery = "SELECT *
                         FROM employeepubliccomments
                         WHERE employee_id = :id 
                         ORDER BY date DESC";

        $statementTwo = $db->prepare($commentquery);

        $statementTwo->bindValue(':id', $id, PDO::PARAM_INT);

        $statementTwo->execute();
    }
    else if (isset($_POST['id']) && isset($_POST['commenter_name']) && isset($_POST['comment']))
    {
        $id = $_POST['id'];
        $name = filter_input(INPUT_POST, 'commenter_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $insertquery = "INSERT INTO employeepubliccomments (commenter_name, comment, employee_id) VALUES (:name, :comment, :employee_id)";
        $statement = $db->prepare($insertquery);

        $statement->bindValue(":name", $name);
        $statement->bindValue(":comment", $comment);
        $statement->bindValue(":employee_id", $id);

        if($statement->execute())
        {
            header("Location: commentemployee.php?id={$id}");
            exit;
        }            
    }
    else
    {
        header("Location: publicemployees.php");
        exit;
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <title>Edit this Post!</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="publicemployees.php">Best Cleaners Solutions</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="publicemployees.php">Home</a>
            </li>
        </ul>
            <div>
                <form action="commentemployee.php" method="post">
                    <fieldset>
                        <legend>Employee Details</legend>
                        <p>
                            <label for="employee_id">Employee ID:</label>
                            <?=$employee['employee_id']?>
                        </p>
                        <p>
                            <label for="first_name">First Name:</label>
                            <?=$employee['first_name']?>
                        </p>
                        <p>
                            <label for="last_name">Last Name:</label>
                            <?=$employee['last_name']?>
                        </p>
                        <p>
                            <label for="phone">Phone:</label>
                            <?=$employee['phone']?>
                        </p>
                        <p>
                            <label for="email">Email:</label>
                            <?=$employee['email']?>
                        </p>
                    </fieldset>
                    <fieldset>
                        <legend>Post your comment</legend>
                        <p>
                            <label for="commenter_name">Name:</label>
                            <input type="text" name="commenter_name" id="commenter_name" required>
                        </p>
                        <p>
                            <textarea id="comment" name="comment"></textarea>
                        </p>
                        <input type="hidden" name="id" value=<?= $employee['employee_id'] ?>>
                        <input type="submit" name="post" value="Post">
                    </fieldset>
                </form>
                <div id="all_comments">
                    <?php while ($comment = $statementTwo->fetch()): ?>
                        <div class="comment_post">
                            <h3><?= $comment['commenter_name'] ?></h3>
                            <small><?= date("F d, Y h:i a", strtotime($comment['date']))?></small>
                            <div class="comment_content">
                                <?= $comment['comment']?>                            
                            </div>
                        </div>
                    <?php endwhile ?>                
                </div>
            </div>
    </div>
</body>
</html>