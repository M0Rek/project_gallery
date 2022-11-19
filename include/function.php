<?php
session_start();
include("define.php");
include("layout.php");

function debugToConsole($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug Objects: " . $output . "' );</script>";
}

function redirectToPage($page, $delay = 0) {
    return
        '<script> 
            setTimeout(_ => { window.location.href ="'.$page.'"},'.$delay.')    
        </script>';
}