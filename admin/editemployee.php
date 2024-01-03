<?php
    session_start();
    
    require('connect.php');
    
    if ($_POST &&
        isset($_POST['id']) && 
        isset($_POST['first_name']) && 
        isset($_POST['last_name']) && 
        isset($_POST['phone']) &&
        isset($_POST['email']) &&
        isset($_POST['start_date']) ||
        isset($_POST['end_date']) ||
        isset($_POST['blacklist']) ||
        isset($_POST['category'])) 
    {
        $id  = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $firstName  = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
        $endDate = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
        $blacklist = filter_input(INPUT_POST, 'blacklist', FILTER_SANITIZE_SPECIAL_CHARS);
        $category_id = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

        if (isset($_POST['delete']))
        {         
            $query = "DELETE FROM employees WHERE employee_id = :id LIMIT 1";
    
            $statement = $db->prepare($query);
    
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
    
            $statement->execute();
    
            header("Location: adminemployees.php");
            exit;
        }

        $query = "UPDATE employees 
                  SET first_name = :first_name, 
                    last_name = :last_name, 
                    phone = :phone, 
                    email = :email, 
                    start_date = :start_date, 
                    end_date = NULLIF(:end_date, 0000-00-00), 
                    blacklist = NULLIF(:blacklist, ''), 
                    category_id = NULLIF(:category_id, '')
                  WHERE employee_id = :id";

        
        $statement = $db->prepare($query);
        $statement->bindValue(':first_name', $firstName);        
        $statement->bindValue(':last_name', $lastName);
        $statement->bindValue(':phone', $phone);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':start_date', $startDate);
        $statement->bindValue(':end_date', $endDate);
        $statement->bindValue(':blacklist', $blacklist);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);

        $statement->execute();
        
        header("Location: adminemployees.php");
        exit;
    }
    else if (isset($_GET['id'])) 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT e.*, ec.category_name 
                  FROM employees e 
                  LEFT JOIN employeecategory ec ON e.category_id = ec.category_id 
                  WHERE e.employee_id = :id";

        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        
        $statement->execute();

        $employee = $statement->fetch();

        $categoryQuery = "SELECT * FROM employeecategory";

        $categoryStatement = $db->query($categoryQuery);

        $categories = $categoryStatement->fetchAll();

        if (empty($employee['employee_id']))
        {
            header("Location: adminemployees.php");
            exit;
        }
    }
    else
    {
        header("Location: adminemployees.php");
        exit;
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
                <a href="adminemployees.php" class="text-decoration-none">Best Cleaners Solutions - Edit Employee</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="adminemployees.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div id="employee_edit">
            <form action="editemployee.php" method="post" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
                    <legend>Employee Details</legend>
                    <div class="form-group">
                        <label for="employee_id" class="form-label">Employee ID:</label>
                        <span class="form-control-plaintext"><?= $employee['employee_id'] ?></span>
                    </div>
                    <div class="form-group">
                        <label for="first_name" class="form-label">First Name:</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="<?= $employee['first_name'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name" class="form-label">Last Name:</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="<?= $employee['last_name'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone:</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?= $employee['phone'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?= $employee['email'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="category" class="form-label">Category:</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">None</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= $category['category_id'] ?>" <?= (isset($employee['category_id']) && $category['category_id'] == $employee['category_id']) ? 'selected' : '' ?>>
                                <?= $category['category_name'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </fieldset>
                <fieldset class="border p-4 rounded mt-3">
                    <legend>Dates and Blacklist</legend>
                    <div class="form-group">
                        <label for="start_date" class="form-label">Start Date:</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $employee['start_date'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="end_date" class="form-label">End Date:</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $employee['end_date'] ?>">
                    </div>
                    <div class="form-group">
                        <label for="blacklist" class="form-label">Blacklist:</label>
                        <select name="blacklist" id="blacklist" class="form-control">
                            <?php if ($employee['blacklist'] == 'x'): ?>
                            <option value="x">Yes</option>
                            <option value="">No</option>
                            <?php else: ?>
                            <option value="">No</option>
                            <option value="x">Yes</option>
                            <?php endif ?>
                        </select>
                    </div>
                </fieldset>
                <input type="hidden" name="id" value="<?= $employee['employee_id'] ?>">
                <div class="form-group text-center mt-3">
                    <button type="submit" name="update" class="btn btn-primary">Update</button>
                    <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Are you sure you wish to delete this employee?')">Delete</button>
                </div>
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