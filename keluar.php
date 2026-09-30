<?php
session_start();
unset($_SESSION['user_id'], $_SESSION['user_nama'], $_SESSION['user_username'], $_SESSION['user_email'], $_SESSION['user_foto'], $_SESSION['profile_csrf']);
header('Location: index.php');
exit;
