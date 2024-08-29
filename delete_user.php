<?php

include 'db.php';

if (isset($_GET["id"])){
$id = $_GET["id"];

$sql = "DELETE FROM users WHERE id = $id";
$connection->query($sql);
}

header("location: /garages/users.php");
        exit;
        
        
?>