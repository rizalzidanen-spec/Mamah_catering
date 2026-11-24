<?php
session_start();
unset($_SESSION['admin_login']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_nama']);

header("Location: login_admin.php");
exit;
?>