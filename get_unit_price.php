<?php

include 'db.php';

if (isset($_POST["accountid"])){
$id = $_POST["accountid"];
$result = $connection->query("SELECT unit_cost FROM accounts WHERE id = $id");
if($row = $result->fetch_assoc()){
    echo number_format($row['unit_cost'], 0);
}
}


        
        
?>