<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && 
   (time() - $_SESSION['LAST_ACTIVITY'] > 7200)) {

    session_unset();
    session_destroy();

    header("Location: login.php");
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();