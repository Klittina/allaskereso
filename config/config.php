<?php
// Adatbázis kapcsolat beállítások
$host = 'localhost';
$port = '1521';
$sid = 'XE';
$username = 'KRISZTINA';
$password = 'KRISZTINA';

// Csatlakozás az Oracle adatbázishoz
$conn = oci_connect($username, $password, "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$sid)))");

if (!$conn) {
    $e = oci_error();
    die("Nem sikerült csatlakozni az adatbázishoz: " . htmlentities($e['message'], ENT_QUOTES));
}
?>
