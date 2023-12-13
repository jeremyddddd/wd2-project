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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Edit Comment</title>
</head>
<body>
    <div id="wrapper" class="container">
        <div id="header" class="text-center">
            <h1 class="my-4">
                <a href="adminpublicemployees.php" class="text-decoration-none">Best Cleaners Solutions</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="adminpublicemployees.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div>
            <form action="comments.php" method="post">
                <fieldset class="border p-4 rounded">
                    <legend class="w-auto px-2">Employee Details</legend>
                    <p class="form-group">
                        <label for="employee_id" class="form-label">Employee ID:</label>
                        <span><?= isset($employee_id) ? $employee_id : $employee['employee_id'] ?></span>
                    </p>
                    <p class="form-group">
                        <label for="first_name" class="form-label">First Name:</label>
                        <span><?= isset($first_name) ? $first_name : $employee['first_name'] ?></span>
                    </p>
                    <p class="form-group">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <span><?= isset($last_name) ? $last_name : $employee['last_name'] ?></span>
                    </p>
                    <p class="form-group">
                        <label for="phone" class="form-label">Phone:</label>
                        <span><?= isset($phone) ? $phone : $employee['phone'] ?></span>
                    </p>
                    <p class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <span><?= isset($email) ? $email : $employee['email'] ?></span>
                    </p>
                </fieldset>
            </form>
            <div id="all_comments">
                <?php while ($comment = $statementTwo->fetch()): ?>
                    <div class="comment_post p-3 my-2 border rounded">
                        <h3><?= $comment['commenter_name'] ?></h3>
                        <small><?= date("F d, Y h:i a", strtotime($comment['date'])) ?></small>
                        <form action="comments.php" method="post" class="my-2">
                            <input type="hidden" name="id" value="<?= $employee['employee_id'] ?>">
                            <input type="hidden" name="comment_id" value="<?= $comment['comment_id'] ?>">
                            <div class="btn-group">
                                <button type="submit" name="delete_comment" class="btn btn-danger btn-sm">Delete</button>
                                <button type="submit" name="hide_comment" class="btn btn-secondary btn-sm">Hide</button>
                                <button type="submit" name="disemvowel_comment" class="btn btn-warning btn-sm">Disemvowel</button>
                            </div>
                        </form>
                        <div class="comment_content">
                            <?= $comment['comment'] ?>                            
                        </div>
                    </div>
                <?php endwhile ?>                
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