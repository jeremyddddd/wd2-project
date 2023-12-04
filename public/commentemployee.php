<?php
    session_start();

    require('connect.php');

    $employee = null;

    $statementTwo = null;

    function generateCaptchaCode($length = 4)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $captchaCode = '';
        for ($i = 0; $i < $length; $i++) {
            $captchaCode .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $captchaCode;
    }

    if (isset($_POST['employee_id']) && isset($_POST['commenter_name']) && isset($_POST['comment']))
    {
        $employee_id = $_POST['employee_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];

        $name = filter_input(INPUT_POST, 'commenter_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $captcha_input = filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $query = "SELECT *
                  FROM employees
                  WHERE employee_id = :id";

        $statement = $db->prepare($query);

        $statement->bindValue(':id', $employee_id, PDO::PARAM_INT);

        $statement->execute();

        $employee = $statement->fetch();

        if (empty($captcha_input) || strtoupper($captcha_input) !== $_SESSION['captcha']) 
        {
            $captcha_error = 'CAPTCHA code is incorrect. Please try again.';

            $captcha_code = generateCaptchaCode();
            $_SESSION['captcha'] = strtoupper($captcha_code);
        }
        else
        {
            $insertquery = "INSERT INTO employeepubliccomments (commenter_name, comment, employee_id) VALUES (:name, :comment, :employee_id)";
            $statement = $db->prepare($insertquery);

            $statement->bindValue(":name", $name);
            $statement->bindValue(":comment", $comment);
            $statement->bindValue(":employee_id", $employee_id);

            if($statement->execute())
            {
                header("Location: commentemployee.php?id={$employee_id}");
            }            
        }

        $commentquery = "SELECT * FROM employeepubliccomments WHERE employee_id = :id ORDER BY date DESC";
        $statementTwo = $db->prepare($commentquery);
        $statementTwo->bindValue(':id', $employee_id, PDO::PARAM_INT);
        $statementTwo->execute();            
    }
    
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

        $captcha_code = generateCaptchaCode();
        $_SESSION['captcha'] = strtoupper($captcha_code);

        if ($employee === false || empty($employee['employee_id'])) 
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
                            <?= isset($employee_id) ? $employee_id : $employee['employee_id'] ?>
                        </p>
                        <p>
                            <label for="first_name">First Name:</label>
                            <?= isset($first_name) ? $first_name : $employee['first_name'] ?>
                            <input type="hidden" name="first_name" value="<?= $employee['first_name'] ?>">
                        </p>
                        <p>
                            <label for="last_name">Last Name:</label>
                            <?= isset($last_name) ? $last_name : $employee['last_name'] ?>
                            <input type="hidden" name="last_name" value="<?= $employee['last_name'] ?>">
                        </p>
                        <p>
                            <label for="phone">Phone:</label>
                            <?= isset($phone) ? $phone : $employee['phone'] ?>
                            <input type="hidden" name="phone" value="<?= $employee['phone'] ?>">
                        </p>
                        <p>
                            <label for="email">Email:</label>
                            <?= isset($email) ? $email : $employee['email'] ?>
                            <input type="hidden" name="email" value="<?= $employee['email'] ?>">
                        </p>
                    </fieldset>
                    <fieldset>
                        <legend>Post your comment</legend>
                        <p>
                            <label for="commenter_name">Name:</label>
                            <input type="text" name="commenter_name" id="commenter_name" value="<?= isset($name) ? $name : '' ?>" required>
                        </p>
                        <p>
                            <textarea id="comment" name="comment"><?= isset($comment) ? $comment : '' ?></textarea>
                        </p>
                        <div class="form-group">
                            <div>
                                <label for="captcha">CAPTCHA:</label>
                            </div>
                            <img src="captcha_image.php" alt="CAPTCHA Image">
                            <input type="text" id="captcha" name="captcha" placeholder="Enter CAPTCHA code" required>
                            <?php if (isset($captcha_error)): ?>
                                <p class='error'><?= $captcha_error ?></p>
                            <?php endif ?>
                        </div>
                        <br>
                        <input type="hidden" name="employee_id" value="<?= $employee['employee_id'] ?>">
                        <input type="submit" name="post" value="Post">
                    </fieldset>
                </form>
                <div id="all_comments">
                    <?php while ($statementTwo !== null && $comment = $statementTwo->fetch()): ?>
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