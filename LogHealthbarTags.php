<?php

$directory = "screenshots";
$screenshot_images = glob(__DIR__ . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . "*.HealthBar.jpg");
$output = '';
foreach($screenshot_images as $imgnum=>$image){        
    // Post data and determine values
    if(isset($_POST["$imgnum-stim"])){
        $stim = $_POST["$imgnum-stim"];
    }
    else{
        $stim = -1;
    }
    if(isset($_POST["$imgnum-rad"])){
       $rad = $_POST["$imgnum-rad"];
    }
    else{
       $rad = -1;
    }

    // Generate states output e.g.:
    // image.jpg -1 -1
    // image.jpg -1 1
    // image.jpg 1 -1
    // image.jpg 1 1
    $states = '';
    if ($stim == '1'){
        $states .= '1 ';
    }
    else{
        $states .= '-1 ';
    }
    if($rad == '1'){
        $states .= '1';
    }
    else{
        $states .= '-1';
    }
    $output .= basename($image) . " $states" . PHP_EOL;
}

$file = fopen('HealthBar_Raw_States.txt', 'w');

fwrite($file, $output);

fclose($file);

echo "All Done!";
