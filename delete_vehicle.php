<?php

include 'db.php';

if (isset($_GET["id"])){
$id = $_GET["id"];

$sql = "DELETE FROM vehicles WHERE id = $id";
$connection->query($sql);
}

header("location: /garages/vehicles.php");
        exit;
        
        
?>