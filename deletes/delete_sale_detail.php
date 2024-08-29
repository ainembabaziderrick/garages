<?php

include '../db.php';

if (isset($_GET["id"])){
$id = $_GET["id"];

$sql = "DELETE FROM transactions WHERE id = '$id' ";
$connection->query($sql);
}

// header("location: ../view_sales.php");
// exit;
echo "<script>history.go(-1)</script>";

        
        
?>