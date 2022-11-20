<?php
session_start();
require("define.php");
require("layout.php");
require("database.php");

function debugToConsole($data)
{
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function redirectToPage($page, $delay = 0)
{
    return
        '<script> 
            setTimeout(_ => { window.location.href ="' . $page . '"},' . $delay . ')    
        </script>';
}

function resizeImage($file, $w, $h, $maintainWidth)
{
    $size = getimagesize($file);
    $width = $size[0];
    $height = $size[1];

    $ratio = $width / $height;

    if ($maintainWidth) {
        $newHeight = $w / $ratio;
        $newWidth = $w;
    } else {
        $newWidth = $h * $ratio;
        $newHeight = $h;
    }

    $filePath = imagecreatefromjpeg($file);
    $res = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($res, $filePath, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    return $res;
}