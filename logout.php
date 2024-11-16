<?php 
session_start();
if(isset($_SESSION["users"])){
    unset($_SESSION["users"]);
    session_destroy();
}

header("location: ../login.php");
?>