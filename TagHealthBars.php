<?php

$directory = "screenshots";
$screenshot_images = glob(__DIR__ . DIRECTORY_SEPARATOR . $directory . DIRECTORY_SEPARATOR . "*.HealthBar.jpg");
$output = '';
foreach($screenshot_images as $imgnum=>$image){
    $output .= "<h4>$imgnum</h4>";
    $output .= "<img src='$directory/" . basename($image) . "'><br>";
    $output .= "<input type='checkbox' name='$imgnum-stim' value='1'> Stimpack<br>";
    $output .= "<input type='checkbox' name='$imgnum-rad' value='1'> Radaway<br>";
    $output .= "<hr/>";
}
?>
<html>
<head>
</head>
<body>
    <form action="LogHealthbarTags.php" method="post">
      <?php echo $output; ?>
      <input type="submit" value="Submit">
    </form> 
</body>
</html>
