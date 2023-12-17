<?php
$dir = "css/";
$files = glob($dir . "*.css");

foreach($files as $file) {
    echo '<link rel="stylesheet" type="text/css" href="' . $file . '">';
}
?>