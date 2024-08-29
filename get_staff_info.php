<?php

include 'db.php';

if (isset($_POST["staff_id"])){
$id = $_POST["staff_id"];
$result = $connection->query("SELECT * FROM staff WHERE id = $id");
if($row = $result->fetch_assoc()){
    echo number_format($row['unit_cost'], 0);
}
}


        
        
?>