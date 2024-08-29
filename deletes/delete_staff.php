<?php

include '../db.php';

if (isset($_GET["id"])){
$id = $_GET["id"];
$status = "inactive";
$sql = "UPDATE staff SET status = '$status' WHERE id = '$id' ";
$connection->query($sql);
}


echo "<script>history.go(-1)</script>";

        
        
?>