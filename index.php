<?php
/**
 * OpenCropCare - Apache / Subdirectory Entry Router
 * Automatically forwards root directory requests to the public/ application controller.
 */
$target = 'public/';
if (!empty($_SERVER['QUERY_STRING'])) {
    $target .= '?' . $_SERVER['QUERY_STRING'];
}
header('Location: ' . $target);
exit();
