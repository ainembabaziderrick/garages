<?php

include 'db.php';

if (isset($_GET["id"])){
$id = $_GET["id"];

$sql = "DELETE FROM customers WHERE id = $id";
$connection->query($sql);
}

header("location: customers.php");
        exit;
        
        
?>