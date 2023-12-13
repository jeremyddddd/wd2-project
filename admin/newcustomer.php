<?php
    session_start();

    require('connect.php');

    require ("C:\\xampp\htdocs\wd2\Project\wd2-project\image_resize\ImageResize.php");
    require ("C:\\xampp\htdocs\wd2\Project\wd2-project\image_resize\ImageResizeException.php");


    use \Gumlet\ImageResize;

    if ($_POST &&
    !empty($_POST['name']) && 
    !empty($_POST['address']) && 
    !empty($_POST['phone']) &&
    !empty($_POST['email'])) 
    {
        $firstName  = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $filepath = null;

        // Contract upload
        if (!empty($_FILES['file']['name'])) 
        {
            $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

            $fileType = mime_content_type($_FILES['file']['tmp_name']);

            if (in_array($fileType, $allowedFileTypes)) 
            {
                $filename = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);
                $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                $uploadDir = "C:/xampp/htdocs/wd2/Project/wd2-project/uploads/" . $filename . '_resized.' . $extension;
                $filepath = "/wd2/Project/wd2-project/uploads/" . $filename . '_resized.' . $extension;

                $resizer = new ImageResize($_FILES['file']['tmp_name']);
                $resizer->resizeToWidth(600);

                if ($resizer->save($uploadDir)) 
                {   
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

        $query = "INSERT INTO customers (name, address, phone, email, image_filepath) 
                  VALUES (:name, :address, :phone, :email, :image_filepath)";

        $statement = $db->prepare($query);
        $statement->bindValue(':name', $firstName);         
        $statement->bindValue(':address', $address);
        $statement->bindValue(':phone', $phone);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':image_filepath', $filepath);

        if($statement->execute())
        {
            header("Location: admincustomers.php");
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
                <a href="admincustomers.php" class="text-decoration-none">Best Cleaners Solutions - Add Customer</a>
            </h1>
        </div>
        <ul id="menu" class="nav justify-content-center mb-4">
            <li class="nav-item">
                <a href="admincustomers.php" class="nav-link">Home</a>
            </li>
        </ul>
        <div id="customer_add">
            <form action="newcustomer.php" method="post" enctype="multipart/form-data" class="w-50 mx-auto">
                <fieldset class="border p-4 rounded">
                    <legend>Add New Customer</legend>
                    <div class="form-group">
                        <label for="name" class="form-label">Company Name:</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="address" class="form-label">Address:</label>
                        <input type="text" name="address" id="address" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">Phone:</label>
                        <input type="tel" name="phone" id="phone" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <fieldset class="border p-4 rounded mt-3">
                        <legend>Contract Upload</legend>
                        <div class="form-group">
                            <label for="file" class="form-label">Choose a file:</label>
                            <input type="file" name="file" id="file" class="form-control-file" accept=".jpg, .png, .gif, .pdf">
                        </div>
                    </fieldset>
                    <div class="form-group text-center mt-3">
                        <button type="submit" class="btn btn-primary">Add Customer</button>
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