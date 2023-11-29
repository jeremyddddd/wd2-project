<?php
    session_start();

    require('connect.php');

    require ("C:\\xampp\htdocs\wd2\Project\wd2-project\image_resize\ImageResize.php");
    require ("C:\\xampp\htdocs\wd2\Project\wd2-project\image_resize\ImageResizeException.php");

    use \Gumlet\ImageResize;

    if (isset($_GET['logout']) && $_GET['logout'] == 'true') 
    {
        session_destroy();
        header("Location: /wd2/Project/wd2-project/public/Login.php");
        exit;
    }

    if ($_POST &&
        isset($_POST['id']) && 
        isset($_POST['name']) && 
        isset($_POST['address']) && 
        isset($_POST['phone']) &&
        isset($_POST['email']) ||
        isset($_POST['blacklist']) ||
        isset($_POST['deleteImage']) ||
        isset($_FILES['file'])) 
    {
        $id  = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
        $firstName  = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastName = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $blacklist = filter_input(INPUT_POST, 'blacklist', FILTER_SANITIZE_SPECIAL_CHARS);

        if (isset($_POST['delete']))
        {         
            $query = "DELETE FROM customers WHERE customer_id = :id LIMIT 1";
    
            $statement = $db->prepare($query);
    
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
    
            $statement->execute();
    
            header("Location: admincustomers.php");
            exit;
        }

        // Contract Deletion
        if (isset($_POST['deleteImage']))
        {
            $query = "SELECT * FROM customers WHERE customer_id = :id";
            $statement = $db->prepare($query);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            
            $statement->execute();
    
            $customer = $statement->fetch();

            $uploadDir = "C:/xampp/htdocs";

            $filepath = $customer['image_filepath'];

            $uploadDir = "C:/xampp/htdocs" . $customer['image_filepath'];

            if (file_exists($uploadDir)) 
            {
                unlink($uploadDir);
            }
        
            $statement = $db->prepare("UPDATE customers SET image_filepath = NULL WHERE customer_id = :id");
            $statement->bindValue(":id", $id);
            $statement->execute();
        
            echo 'Image removed successfully. Click <a href="editcustomer.php">here</a> to return to customer page.';
        
            exit;
        }
        else
        {
            header("Location: admincustomers.php");
        }

        // Contract upload
        if (!empty($_FILES['file']['name'])) 
        {
            $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    
            $uploadDir = "C:/xampp/htdocs/wd2/Project/uploads/";

            $filepath = "/wd2/Project/uploads/";
    
            $fileType = mime_content_type($_FILES['file']['tmp_name']);
    
            if (in_array($fileType, $allowedFileTypes)) 
            {
                $filename = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
                $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $newFileDir = $uploadDir . $filename . '_resized.' . $extension;
                $newfilepath = $filepath . $filename . '_resized.' . $extension;
    
                $resizer = new ImageResize($_FILES['file']['tmp_name']);
                $resizer->resizeToWidth(600);
    
                if ($resizer->save($newFileDir)) 
                {   
                    $statement = $db->prepare("UPDATE customers SET image_filepath = :image_filepath WHERE customer_id = :id");
                    $statement->bindValue(":image_filepath", $newfilepath);
                    $statement->bindValue(":id", $id, PDO::PARAM_INT);
                    $statement->execute(); 
                    
                    echo 'File updated successfully.';
                } 
                else 
                {
                    echo 'Sorry, a problem occurred when updating the file.';
                }
                
            } 
            else 
            {
                echo 'Invalid file type. Only JPG, PNG, GIF, and PDF files are allowed.';
            }
        } 
        else 
        {
            echo 'Please select a valid file type. (eg. JPG, PNG, GIF, and PDF)';
        }

        $query = "UPDATE customers 
                  SET name = :name, 
                    address = :address, 
                    phone = :phone, 
                    email = :email, 
                    blacklist = NULLIF(:blacklist, '')
                  WHERE customer_id = :id";
        
        $statement = $db->prepare($query);
        $statement->bindValue(':name', $firstName);        
        $statement->bindValue(':address', $lastName);
        $statement->bindValue(':phone', $phone);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':blacklist', $blacklist);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        
        $statement->execute();
        
        header("Location: admincustomers.php");
        exit;
    }
    else if (isset($_GET['id'])) 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);

        $query = "SELECT * FROM customers WHERE customer_id = :id";
        $statement = $db->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        
        $statement->execute();

        $customer = $statement->fetch();

        if (empty($customer['customer_id']))
        {
            header("Location: admincustomers.php");
        }
    }
    else
    {
        header("Location: admincustomers.php");
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
                <a href="admincustomers.php">Best Cleaners Solutions - Edit Customer</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="admincustomers.php">Home</a>
            </li>
        </ul>
        <div id="customer_edit">
            <form action="editcustomer.php" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Customer Details</legend>
                    <p>
                        <label>Customer ID:</label>
                        <?=$customer['customer_id']?>
                    </p>
                    <p>
                        <label for="name">Company name:</label>
                        <input type="text" name="name" id="name" value="<?=$customer['name']?>" required>
                    </p>
                    <p>
                        <label for="address">Address:</label>
                        <input type="text" name="address" id="address" value="<?=$customer['address']?>" required>
                    </p>
                    <p>
                        <label for="phone">Phone:</label>
                        <input type="text" name="phone" id="phone" value=<?=$customer['phone']?> required>
                    </p>
                    <p>
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" value=<?=$customer['email']?> required>
                    </p>
                    <p>
                        <label for="blacklist">Blacklist:</label>
                        <select name="blacklist" id="blacklist">
                            <?php if ($customer['blacklist'] == 'x'): ?>
                                <option value="x">Yes</option>
                                <option value="">No</option>
                            <?php else: ?>
                                <option value="">No</option>
                                <option value="x">Yes</option>
                            <?php endif ?>
                        </select>
                    </p>
                    <p>
                        <label for="file">Choose a file:</label>
                        <input type="file" name="file" id="file" accept=".jpg, .png, .gif, .pdf">
                    </p>
                    <?php if (!empty($customer['image_filepath'])): ?>
                    <p>
                        <img src="<?= $customer['image_filepath']?>" alt="Customer Image">
                        <input type="submit" name="deleteImage" value="Delete image" onclick="return confirm('Are you sure you wish to delete this image?')">
                    </p>
                    <br>
                    <br>
                    <?php endif ?>
                    <input type="hidden" name="id" value=<?= $customer['customer_id'] ?>>
                    <input type="submit" name="update" value="Update">
                    <input type="submit" name="delete" value="Delete customer" onclick="return confirm('Are you sure you wish to delete this customer?')">
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