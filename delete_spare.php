<?php

include 'db.php';

if (isset($_GET["id"])){
$id = $_GET["id"];

$sql = "DELETE FROM accounts WHERE id = $id";
$connection->query($sql);
}

header("location: /garages/spares.php");
        exit;
        
        
?>