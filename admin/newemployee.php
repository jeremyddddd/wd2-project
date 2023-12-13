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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <title>Edit this Post!</title>
</head>
<body>
    <div id="wrapper" class="container">
        <div id="header" class="text-center">
            <h1 class="my-4">
                <a href="adminemployees.php" class="text-decoration-none">Best Cleaners Solutions - Add Employee</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="adminemployees.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div id="employee_add">
            <form action="newemployee.php" method="post" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
                    <legend>Add Employee</legend>
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone:</label>
                        <input type="tel" name="phone" id="phone" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="start_date" class="form-label">Start Date:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" required>
                    </p>
                    <div class="form-group">
                        <label for="category" class="form-label">Category:</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">None</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>">
                                    <?= $category['category_name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group text-center mt-3">
                        <button type="submit" class="btn btn-primary">Add Employee</button>
                    </div>
                </fieldset>
            </form>
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