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
                <a href="admincustomers.php">Best Cleaners Solutions - Add Customer</a>
            </h1>
        </div>
        <ul id="menu">
            <li>
                <a href="admincustomers.php">Home</a>
            </li>
        </ul>
        <div id="customer_add">
            <form action="newcustomer.php" method="post" enctype="multipart/form-data">
                <fieldset>
                    <legend>Add New Customer</legend>
                    <p>
                        <label for="name">Company Name:</label>
                        <input type="text" name="name" id="name" required>
                    </p>
                    
                    <p>
                        <label for="address">Address:</label>
                        <input type="text" name="address" id="address" required>
                    </p>

                    <p>
                        <label for="phone">Phone:</label>
                        <input type="tel" name="phone" id="phone">
                    </p>

                    <p>
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" required>
                    </p>
                    <fieldset>
                        <legend>Contract Upload</legend>
                        <p>
                            <label for="file">Choose a file:</label>
                            <input type="file" name="file" id="file" accept=".jpg, .png, .gif, .pdf">
                        </p>
                    </fieldset>
                    <br>
                    <button type="submit">Add Customer</button>
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