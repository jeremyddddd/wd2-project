<?php
    session_start();

    header('Content-type: image/png');

    $font = 4; 
    $image_width = 120;
    $image_height = 40;

    $image = imagecreate($image_width, $image_height);
    $background_color = imagecolorallocate($image, 255, 255, 255);
    $text_color = imagecolorallocate($image, 0, 0, 0);

    $captcha_code = $_SESSION['captcha'];

    imagestring($image, $font, 10, 10, $captcha_code, $text_color);

    imagepng($image);
    imagedestroy($image);
?>