<?php
    session_start();

    require('connect.php');

    require ("C:\\xampp\htdocs\wd2\Project\wd2-project\image_resize\ImageResize.php");
    require ("C:\\xampp\htdocs\wd2\Project\wd2-project\image_resize\ImageResizeException.php");

    use \Gumlet\ImageResize;
    

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
        $name  = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
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
    
            $uploadDir = "C:/xampp/htdocs/wd2/Project/wd2-project/uploads/";

            $filepath = "/wd2/Project/wd2-project/uploads/";
    
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
        $statement->bindValue(':name', $name);        
        $statement->bindValue(':address', $address);
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
<body>
    <div id="wrapper" class="container">
        <div id="header" class="text-center">
            <h1 class="my-4">
                <a href="admincustomers.php" class="text-decoration-none">Best Cleaners Solutions - Edit Customer</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="admincustomers.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div id="customer_edit">
            <form action="editcustomer.php" method="post" enctype="multipart/form-data" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
                    <legend class="w-auto px-2">Customer Details</legend>
                    <div class="form-group">
                        <label class="form-label">Customer ID:</label>
                        <span class="form-control-plaintext"><?=$customer['customer_id']?></span>
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">Company name:</label>
                        <input type="text" name="name" id="name" class="form-control" value="<?=$customer['name']?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" name="address" id="address" class="form-control" value="<?=$customer['address']?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone:</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="<?=$customer['phone']?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?=$customer['email']?>" required>
                    </div>
                    <div class="form-group">
                        <label for="blacklist" class="form-label">Blacklist:</label>
                        <select name="blacklist" id="blacklist" class="form-control">
                        <?php if ($customer['blacklist'] == 'x'): ?>
                                <option value="x">Yes</option>
                                <option value="">No</option>
                            <?php else: ?>
                                <option value="">No</option>
                                <option value="x">Yes</option>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="file" class="form-label">Choose a file:</label>
                        <input type="file" name="file" id="file" class="form-control-file" accept=".jpg, .png, .gif, .pdf">
                    </div>
                    <?php if (!empty($customer['image_filepath'])): ?>
                    <div class="form-group text-center">
                        <img src="<?= $customer['image_filepath']?>" alt="Customer Image" class="img-thumbnail my-3">
                        <button type="submit" name="deleteImage" class="btn btn-danger" onclick="return confirm('Are you sure you wish to delete this image?')">Delete image</button>
                    </div>
                    <?php endif ?>
                    <div class="form-group text-center">
                        <input type="hidden" name="id" value="<?= $customer['customer_id'] ?>">
                        <button type="submit" name="update" class="btn btn-primary">Update</button>
                        <button type="submit" name="delete" class="btn btn-warning" onclick="return confirm('Are you sure you wish to delete this customer?')">Delete customer</button>
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