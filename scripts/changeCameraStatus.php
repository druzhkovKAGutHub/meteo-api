<?php
    $id = $argv[1];
    $status = $argv[2];
    $db=mysqli_connect('localhost', 'root', '3422671786', 'meteo') or die;
    $q=mysqli_query($db, "UPDATE `cameras` SET `status`='$status' WHERE `id`=$id") or die;
?>
