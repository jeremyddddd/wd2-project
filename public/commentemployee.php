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
    else if (isset($_GET['id']))
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
                         WHERE employee_id = :id AND
                         hidden IS NULL
                         ORDER BY date DESC";

        $statementTwo = $db->prepare($commentquery);

        $statementTwo->bindValue(':id', $id, PDO::PARAM_INT);

        $statementTwo->execute();
    }
    else
    {
        header("Location: publicemployees.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="main.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Edit this Post!</title>
</head>
<body>
<body>
    <div id="wrapper" class="container">
        <div id="header" class="text-center my-3">
            <h1>
                <a href="publicemployees.php">Best Cleaners Solutions</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center">
            <li class="nav-item">
                <a href="publicemployees.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div>
            <form action="commentemployee.php" method="post" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
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
                <fieldset class="border p-4 rounded mt-3">
                    <legend>Post your comment</legend>
                    <p>
                        <label for="commenter_name">Name:</label>
                        <input type="text" name="commenter_name" id="commenter_name" class="form-control" value="<?= isset($name) ? $name : '' ?>" required>
                    </p>
                    <p>
                        <textarea id="comment" name="comment" class="form-control"><?= isset($comment) ? $comment : '' ?></textarea>
                    </p>
                    <div class="form-group">
                        <div>
                            <label for="captcha">CAPTCHA:</label>
                        </div>
                        <img src="captcha_image.php" alt="CAPTCHA Image" class="mb-2">
                        <input type="text" id="captcha" name="captcha" class="form-control" placeholder="Enter CAPTCHA code" required>
                        <?php if (isset($captcha_error)): ?>
                            <p class='error text-danger'><?= $captcha_error ?></p>
                        <?php endif ?>
                    </div>
                    <div class="text-center mt-3">
                        <input type="hidden" name="employee_id" value="<?= $employee['employee_id'] ?>">
                        <button type="submit" name="post" class="btn btn-primary">Post</button>
                    </div>
                </fieldset>
            </form>
            <div id="all_comments" class="mt-4">
                <?php while ($statementTwo !== null && $comment = $statementTwo->fetch()): ?>
                    <div class="comment_post border p-3 mb-3">
                        <h3><?= $comment['commenter_name'] ?></h3>
                        <small><?= date("F d, Y h:i a", strtotime($comment['date'])) ?></small>
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