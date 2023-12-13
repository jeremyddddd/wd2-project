<?php
    session_start();

    require('connect.php');


    $categoryQuery = "SELECT * FROM employeecategory";
    $categoryStatement = $db->prepare($categoryQuery);
    $categoryStatement->execute();
    $categories = $categoryStatement->fetchAll();

    if ($_POST &&
    !empty($_POST['first_name']) && 
    !empty($_POST['last_name']) && 
    !empty($_POST['phone']) &&
    !empty($_POST['email']) &&
    !empty($_POST['start_date'])) 
    {
        $firstName  = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
        $categoryid = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

        $query = "INSERT INTO employees (first_name, last_name, phone, email, start_date, category_id) 
                  VALUES (:first_name, :last_name, :phone, :email, :start_date, NULLIF(:category_id, ''))";

        $statement = $db->prepare($query);
        $statement->bindValue(':first_name', $firstName);        
        $statement->bindValue(':last_name', $lastName);
        $statement->bindValue(':phone', $phone);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':start_date', $startDate);
        $statement->bindValue(':category_id', $categoryid);

        if($statement->execute())
        {
            header("Location: adminemployees.php");
            exit;
        }
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
    <title>Edit this Post!</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>
                <a href="adminemployees.php">Best Cleaners Solutions - Add Employee</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="adminemployees.php">Home</a>
            </li>
        </ul>
        <div id="employee_add">
            <form action="newemployee.php" method="post">
                <fieldset>
                    <legend>Add Employee</legend>
                    <p>
                        <label for="first_name">First Name:</label>
                        <input type="text" name="first_name" id="first_name" required>
                    </p>
                    
                    <p>
                        <label for="last_name">Last Name:</label>
                        <input type="text" name="last_name" id="last_name" required>
                    </p>

                    <p>
                        <label for="phone">Phone:</label>
                        <input type="tel" name="phone" id="phone">
                    </p>

                    <p>
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" required>
                    </p>

                    <p>
                        <label for="start_date">Start Date:</label>
                        <input type="date" name="start_date" id="start_date" required>
                    </p>
                    <p>
                        <label for="category">Category:</label>
                        <select name="category" id="category">
                            <option value="">None</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>">
                                    <?= $category['category_name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </p>
                    <button type="submit">Add Employee</button>
                </fieldset>
            </form>
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