<?php

define("RED",    0);
define("GREEN",    1);
define("BLUE",    2);

function MaxPool(&$img){
    // Get the size info from the input image
    $width = imagesx($img);
    $height = imagesy($img);

    // Determine the size of the pool image.
    $max_pool_img_width = $width / 2;
    while(($max_pool_img_width % 2)>0){ 
        $max_pool_img_width++;// if it wont evenly divide into 2... enlarge
    }
    $max_pool_img_height = $height / 2;
    while(($max_pool_img_height % 2)>0){
        $max_pool_img_height++;// if it wont evenly divide into 2... enlarge
    }
     

    // Allocate resource in memory for the image
    $max_pool_img = imagecreatetruecolor($max_pool_img_width, $max_pool_img_height);

    $background = imagecolorallocate($max_pool_img, 0, 0, 0);

    $max_row = 0;
    $max_col = 0;

    // Max Pooling
    for($row = 0; $row < $width; $row += 2){
        for($col = 0; $col < $height; $col+= 2){
            //    C0   C1   C2 
            //R0 [p1] [p2] [..]
            //R1 [p3] [p4] [..]
            //R2 [..] [..] [..]
            
            // Get color
            $p1 = @imagecolorat($img, $row, $col);
            // R+G+B
            $p1_r = ($p1 >> 16) & 0xFF;
            $p1_g = ($p1 >> 8) & 0xFF;
            $p1_b = $p1 & 0xFF;
            $p1_color = $p1_r + $p1_g + $p1_b;
            
            // Get color
            $p2 = @imagecolorat($img, $row, $col+1);
            // R+G+B
            $p2_r = ($p2 >> 16) & 0xFF;
            $p2_g = ($p2 >> 8) & 0xFF;
            $p2_b = $p2 & 0xFF;
            $p2_color = $p2_r + $p2_g + $p2_b;
            
            // Get color
            $p3 = @imagecolorat($img, $row+1, $col);
            // R+G+B
            $p3_r = ($p3 >> 16) & 0xFF;
            $p3_g = ($p3 >> 8) & 0xFF;
            $p3_b = $p3 & 0xFF;
            $p3_color = $p3_r + $p3_g + $p3_b;
            
            // Get color
            $p4 = @imagecolorat($img, $row+1, $col+1);
            // R+G+B
            $p4_r = ($p4 >> 16) & 0xFF;
            $p4_g = ($p4 >> 8) & 0xFF;
            $p4_b = $p4 & 0xFF;
            $p4_color = $p4_r + $p4_g + $p4_b;
            
            $color = $p1;
            // Find the brightest pixel
            $max_pixel = max($p1_color, $p2_color, $p3_color, $p4_color);
            if($max_pixel == $p1_color){
                $color = $p1;
            }
            elseif($max_pixel == $p2_color){
                $color = $p2;
            }
            elseif($max_pixel == $p3_color){
                $color = $p3;
            }
            else{
                $color = $p4;
            }

            // Paint pooled pixel
            imagesetpixel($max_pool_img, $max_row, $max_col, $color);
            $max_col++;
        }
        $max_col = 0;
        $max_row++;
    }

    return $max_pool_img;
}



function Monochrome(&$img, $color_channel){

    // Get the size info from the input image
    $width = imagesx($img);
    $height = imagesy($img);
    
    // Allocate resource in memory for the image
    $monochrome = imagecreatetruecolor($width, $height);
    $background = imagecolorallocate($monochrome, 0, 0, 0);
    
    // Loop through pixels
    for($row = 0; $row < $width; $row++){
        for($col = 0; $col < $height; $col++){
            
            // Get pixel color channels 
            $p = imagecolorat($img, $row, $col);
            $colors = imagecolorsforindex($img, $p);
            
            // Extract desired channel
            if($color_channel == RED){
                $pixelcolor = imagecolorallocate($monochrome, $colors['red'], 0, 0);
            }
            elseif($color_channel == GREEN){
                $pixelcolor = imagecolorallocate($monochrome, 0, $colors['green'], 0);
            }
            elseif($color_channel == BLUE){
                $pixelcolor = imagecolorallocate($monochrome, 0, 0, $colors['blue']);
            }
            else{
                 $pixelcolor = $background;
            }
            
            // Change pixel to contain pure channel
            imagesetpixel($monochrome, $row, $col, $pixelcolor);
        }
    }

    return $monochrome;
}



// Take an image with height and width and return 
// an array of floats for the desired color channel
function Flatten(&$img, $color_channel){
    // Get the size info from the input image
    $width = imagesx($img);
    $height = imagesy($img);

    // the flattened pixel data is stored here
    $pixels = array();
    
    // Loop through pixels
    for($row = 0; $row < $width; $row++){
        for($col = 0; $col < $height; $col++){
            
            // Get pixel color channels 
            $p = imagecolorat($img, $row, $col);
            $colors = imagecolorsforindex($img, $p);

            // Extract desired channel
            if($color_channel == RED){
                $pixels[] = ColorToFloat($colors['red']);
            }
            elseif($color_channel == GREEN){
                $pixels[] = ColorToFloat($colors['green']);
            }
            elseif($color_channel == BLUE){
                $pixels[] = ColorToFloat($colors['blue']);
            }
            else{
                 $pixels[] = 0.00;
            }
        }
    }
        
    return $pixels;
}



function ExtractHealthbarFromScreenshot(&$source_im){
    $x1 = 95;
    $y1 = 725;
    $x2 = 298 - $x1;
    $x2 = 298 - $x1;
    $y2 = 730 - $y1; // 733 whole thing

    return imagecrop($source_im, ['x' => 95, 'y' => 725, 'width' => $x2, 'height' => $y2]);
}


// example will echo 0 - 255
//for($i = 0; $i <= 255; $i++){
//    echo $i . ' ' . ColorToFloat($i) . PHP_EOL;
//}
function ColorToFloat($value)
{
    $max = 255;
    $increment = $max / 100;
    
    return ($value / $increment) / 100;
}

// example will echo 0 - 1
//for($i = 0; $i <= 1; $i+=0.01){
//    echo $i . ' ' . FloatToColor($i) . PHP_EOL;
//}
function FloatToColor($value)
{
    $max = 255;
    $increment = $max / 100;
    
    return round(($value * 100) * $increment);
}

$image_kernel = array(
        array(-1, -1, -1), 
        array(-1, 8, -1), 
        array(-1, -1, -1)
);

// Training batch number for data set
$training_set_batch = 4;
$screenshots = 'screenshots\\';

$filename = "HealthBar_Raw_States.txt"; // Generated by LogHealthbarTags.php
$file = fopen($filename, "r");
$screenshot_images = fread($file, filesize($filename));
fclose($file);

$screenshot_images = explode(PHP_EOL, $screenshot_images);
$i = 0;
foreach($screenshot_images as $data){
    $data = explode(' ', $data);
    @$screenshot_images[$i] = array();
    @$screenshot_images[$i]['file'] = $data[0];
    @$screenshot_images[$i]['stimpack'] = $data[1];
    @$screenshot_images[$i]['radaway'] = $data[2];
    $i++;
}


// Convert images to training data
$training_data = '';

foreach($screenshot_images as &$test_image){
    
    // Load image
    $health_bar_r = imagecreatefromjpeg($screenshots . $test_image['file']);
    
    // Get image size
    $width = imagesx($health_bar_r);
    $height = imagesy($health_bar_r);

    // Create image resources of the correct size
    $health_bar_g = imagecreatetruecolor($width, $height);
    $health_bar_b = imagecreatetruecolor($width, $height);

    // Copy the image to the new image resources
    imagecopy($health_bar_g, $health_bar_r, 0, 0, 0, 0, $width, $height);
    imagecopy($health_bar_b, $health_bar_r, 0, 0, 0, 0, $width, $height);

    // Extract healthbar if the image is full size
    if($width >= 1280 && $height >= 800){
        $health_bar_r = ExtractHealthbarFromScreenshot($health_bar_r);
        $health_bar_g = ExtractHealthbarFromScreenshot($health_bar_g);
        $health_bar_b = ExtractHealthbarFromScreenshot($health_bar_b);
    }
    
    // Split RGB Channels
    $health_bar_r = Monochrome($health_bar_r, RED);
    $health_bar_g = Monochrome($health_bar_g, GREEN);
    $health_bar_b = Monochrome($health_bar_b, BLUE);
    
    // Convolutions
    imageconvolution($health_bar_r, $image_kernel, 1, -1);
    imageconvolution($health_bar_g, $image_kernel, 1, -1);
    imageconvolution($health_bar_b, $image_kernel, 1, -1);
    
    // Pooling
    $health_bar_r = MaxPool($health_bar_r);
    $health_bar_g = MaxPool($health_bar_g);
    $health_bar_b = MaxPool($health_bar_b);
    
    // Save the split channel images if you want
    //imagejpeg($health_bar_r, $screenshots . $test_image . '.HealthBarRconvo.png');
    //imagejpeg($health_bar_g, $screenshots . $test_image . '.HealthBarGconvo.png');
    //imagejpeg($health_bar_b, $screenshots . $test_image . '.HealthBarBconvo.png');
    
    $red_inputs = Flatten($health_bar_r, RED);
    $green_inputs = Flatten($health_bar_g, GREEN);
    $blue_inputs = Flatten($health_bar_b, BLUE);
    
     // Destroy image resources
    imagedestroy($health_bar_r);
    imagedestroy($health_bar_g);
    imagedestroy($health_bar_b);
    
    
    $training_data .= implode(' ', array_merge($red_inputs, $green_inputs, $blue_inputs)) . PHP_EOL . $test_image['stimpack'] . ' ' . $test_image['radaway'] . PHP_EOL;
}

$number_of_training_examples = count($screenshot_images) - 2;
$number_of_inputs = 612;
$number_of_outputs = 2;

// Write Data
$filename = 'Fallout_Bot_HealthBar_Training_Data_' . $training_set_batch . '_Convolutions.data';
$file = fopen($filename, "w");
fwrite($file, "$number_of_training_examples $number_of_inputs $number_of_outputs" . PHP_EOL . $training_data);
fclose($file);
