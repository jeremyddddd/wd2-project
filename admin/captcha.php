<?php
    session_start();

    function generateCaptchaCode($length = 4) 
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($characters), 0, $length);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_captcha'])) 
    {
        $enteredCaptcha = strtoupper(filter_input(INPUT_POST, 'captcha', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        if ($enteredCaptcha === $_SESSION['captcha_code']) {
            echo "CAPTCHA is correct! Proceed with form processing.";
        } else {
            echo "Incorrect CAPTCHA code. Please try again.";
        }

        $_SESSION['captcha_code'] = generateCaptchaCode();
    } 
    else 
    {
        $_SESSION['captcha_code'] = generateCaptchaCode();

        $image = imagecreate(150, 50);
        imagestring($image, 5, 20, 15, $_SESSION['captcha_code'], imagecolorallocate($image, 0, 0, 0));

        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }
?>