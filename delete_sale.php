<?php

include 'db.php';

if (isset($_GET["id"])){
$id = $_GET["id"];

$sql = "DELETE FROM sales WHERE id = $id";
$connection->query($sql);
}

header("location: /garages/view_sales.php");
        exit;
        
        
?>