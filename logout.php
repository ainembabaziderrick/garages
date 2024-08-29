<?php
session_start();
        $_SESSION['id'] = "";
        $_SESSION['email'] = "";
        $_SESSION['role_id'] = "";
        $_SESSION['username'] = "";
        $_SESSION['garage_id'] = "";
        $_SESSION['business_id'] = "";
        $_SESSION['garage_name'] = "";
session_destroy();
header('Location: index.php');
exit;
?>