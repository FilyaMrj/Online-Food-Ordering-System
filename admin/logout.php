<?php
require_once '../config.php';

unset($_SESSION['user_id']);
unset($_SESSION['user_name']);
unset($_SESSION['user_email']);
unset($_SESSION['user_role']);

session_destroy();

header("Location: login.php");
exit();
?>
