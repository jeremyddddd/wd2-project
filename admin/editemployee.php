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
        isset($_POST['blacklist'])) 
    {
        $id  = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $firstName  = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastName = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $startDate = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
        $endDate = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
        $blacklist = filter_input(INPUT_POST, 'blacklist', FILTER_SANITIZE_SPECIAL_CHARS);

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
                    end_date = NULLIF(:end_date, '0000-00-00'), 
                    blacklist = NULLIF(:blacklist, '')
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
        
        $statement->execute();
        
        header("Location: adminemployees.php");
        exit;
    }
    else if (isset($_GET['id'])) 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT * FROM employees WHERE employee_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        
        $statement->execute();

        $employee = $statement->fetch();

        if (empty($employee['employee_id']))
        {
            header("Location: adminemployees.php");
        }
    }
    else
    {
        header("Location: adminemployees.php");
    }
?>

<?php if(isset($_SESSION['username']) && isset($_SESSION['password']) && $_SESSION['role'] == 'admin'):?>
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
                <a href="adminemployees.php">Best Cleaners Solutions - Edit Employee</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="adminemployees.php">Home</a>
            </li>
        </ul>
        <div id="employee_edit">
            <form action="editemployee.php" method="post">
                <fieldset>
                    <legend>Employee Details</legend>
                    <p>
                        <label for="employee_id">Employee ID:</label>
                        <?=$employee['employee_id']?>
                    </p>
                    <p>
                        <label for="first_name">First Name:</label>
                        <input type="text" name="first_name" id="first_name" value=<?=$employee['first_name']?> required>
                    </p>
                    <p>
                        <label for="last_name">Last Name:</label>
                        <input type="text" name="last_name" id="last_name" value=<?=$employee['last_name']?> required>
                    </p>
                    <p>
                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" id="phone" value=<?=$employee['phone']?> required>
                    </p>
                    <p>
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value=<?=$employee['email']?> required>
                    </p>
                </fieldset>
                <fieldset>
                    <legend>Dates and Blacklist</legend>
                    <p>
                        <label for="start_date">Start Date:</label>
                        <input type="date" name="start_date" id="start_date" value=<?=$employee['start_date']?> required>
                    </p>
                    <p>
                        <label for="end_date">End Date:</label>
                        <input type="date" name="end_date" id="end_date" value=<?=$employee['end_date']?>>
                    </p>
                    <p>
                        <label for="blacklist">Blacklist:</label>
                        <select name="blacklist" id="blacklist">
                            <?php if ($employee['blacklist'] == 'x'): ?>
                                <option value="x">Yes</option>
                                <option value="">No</option>
                            <?php else: ?>
                                <option value="">No</option>
                                <option value="x">Yes</option>
                            <?php endif ?>
                        </select>
                    </p>
                </fieldset>
                <input type="hidden" name="id" value=<?= $employee['employee_id'] ?>>
                <input type="submit" name="update" value="Update">
                <input type="submit" name="delete" value="Delete" onclick="return confirm('Are you sure you wish to delete this employee?')">
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