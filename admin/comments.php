<?php 
    session_start();

    require('connect.php');

    if (isset($_GET['id']) && !isset($_POST['comment_id']))
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
            header("Location: adminpublicemployees.php");
            exit;
        }

        $commentquery = "SELECT *
                         FROM employeepubliccomments
                         WHERE employee_id = :id AND
                         hidden IS NULL
                         ORDER BY date DESC";

        $statementTwo = $db->prepare($commentquery);

        $statementTwo->bindValue(':id', $id, PDO::PARAM_INT);

        $statementTwo->execute();
    }
    
    if (isset($_POST['comment_id']))
    {
        $id  = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $commentId = filter_input(INPUT_POST, 'comment_id', FILTER_SANITIZE_NUMBER_INT);

        if (isset($_POST['delete_comment'])) 
        {
            $query = "DELETE FROM employeepubliccomments WHERE comment_id = :comment_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':comment_id', $commentId, PDO::PARAM_INT);
            $statement->execute();

            header("Location: comments.php?id={$id}");
            exit;
        }
        else if (isset($_POST['hide_comment'])) 
        {        
            $query = "UPDATE employeepubliccomments SET hidden = 1 WHERE comment_id = :comment_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':comment_id', $commentId, PDO::PARAM_INT);
            $statement->execute();

            header("Location: comments.php?id={$id}");
            exit;
        }
        else if (isset($_POST['disemvowel_comment'])) 
        {       
            $query = "SELECT * FROM employeepubliccomments WHERE comment_id = :comment_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':comment_id', $commentId, PDO::PARAM_INT);
            $statement->execute();
            $comment = $statement->fetch();
            
            $disemvoweledComment = str_replace(array('a', 'e', 'i', 'o', 'u', 'A', 'E', 'I', 'O', 'U'), '', $comment['comment']);

            $query = "UPDATE employeepubliccomments SET comment = :disemvoweled_comment WHERE comment_id = :comment_id";
            $statement = $db->prepare($query);
            $statement->bindValue(':comment_id', $commentId, PDO::PARAM_INT);
            $statement->bindValue(':disemvoweled_comment', $disemvoweledComment, PDO::PARAM_STR);
            $statement->execute();

            header("Location: comments.php?id={$id}");
            exit;
        }

    }
?>

<?php if(isset($_SESSION['username']) && $_SESSION['role'] == 'admin'):?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="table.css">
    <title>Edit Comment</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="adminpublicemployees.php">Best Cleaners Solutions</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="adminpublicemployees.php">Home</a>
            </li>
        </ul>
        <div>
            <form action="comments.php" method="post">
                <fieldset>
                    <legend>Employee Details</legend>
                    <p>
                        <label for="employee_id">Employee ID:</label>
                        <?= isset($employee_id) ? $employee_id : $employee['employee_id'] ?>
                    </p>
                    <p>
                        <label for="first_name">First Name:</label>
                        <?= isset($first_name) ? $first_name : $employee['first_name'] ?>
                    </p>
                    <p>
                        <label for="last_name">Last Name:</label>
                        <?= isset($last_name) ? $last_name : $employee['last_name'] ?>
                    </p>
                    <p>
                        <label for="phone">Phone:</label>
                        <?= isset($phone) ? $phone : $employee['phone'] ?>
                    </p>
                    <p>
                        <label for="email">Email:</label>
                        <?= isset($email) ? $email : $employee['email'] ?>
                    </p>
                </fieldset>
            </form>
            <div id="all_comments">
                <?php while ($comment = $statementTwo->fetch()): ?>
                    <div class="comment_post">
                        <h3><?= $comment['commenter_name'] ?></h3>
                        <small><?= date("F d, Y h:i a", strtotime($comment['date']))?></small>
                        <form action="comments.php" method="post">
                            <input type="hidden" name="id" value="<?= $employee['employee_id'] ?>">
                            <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                            <button type="submit" name="delete_comment">Delete</button>
                            <button type="submit" name="hide_comment">Hide</button>
                            <button type="submit" name="disemvowel_comment">Disemvowel</button>
                        </form>
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
<?php else: ?>
    <script>
        alert('Authorized access only');
        window.location.replace("/wd2/Project/wd2-project/public/Login.php");
    </script>
<?php endif ?>