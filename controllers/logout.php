<?php
session_start(); // Session indítása

// Minden session változó törlése
session_unset();

// A session megsemmisítése
session_destroy();

// Visszairányítás a főoldalra
header("Location: ../index.php");
exit();
?>
