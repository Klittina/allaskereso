<?php
session_start();

// Ha nincs bejelentkezve, irányítsd át a bejelentkező oldalra
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
