<?php

include 'db.php';

if (isset($_GET["id"])){
$id = $_GET["id"];

$sql = "DELETE FROM cart_items WHERE id = $id";
$connection->query($sql);
}

header("location: /garages/cart.php");
        exit;
        
        
?>