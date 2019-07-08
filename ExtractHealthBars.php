<?php

// Input: Image is a screenshot 1280x800 px
// Output: Is cropped image 203x5 px
function ExtractHealthbarFromScreenshot(&$source_im){
    $x1 = 95;
    $y1 = 725;
    $x2 = 298 - $x1;
    $x2 = 298 - $x1;
    $y2 = 730 - $y1; // 733 whole thing

    return imagecrop($source_im, array('x' => 95, 'y' => 725, 'width' => $x2, 'height' => $y2));
}

$directory = "screenshots";
$screenshot_images = glob(__DIR__ . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . "*.jpg");

foreach($screenshot_images as $image)
{    
    $health_bar = imagecreatefromjpeg($image);
    $health_bar = ExtractHealthbarFromScreenshot($health_bar);
    imageJPEG($health_bar, "$image.HealthBar.jpg");
}
