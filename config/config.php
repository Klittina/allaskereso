<?php
$host = 'localhost';
$port = '1521';
$sid = 'XE';
$username = 'KRISZTINA';
$password = 'KRISZTINA';

putenv("NLS_LANG=AMERICAN_AMERICA.AL32UTF8"); // Oracle kliens karakterkészlet

$conn = oci_connect(
    $username,
    $password,
    "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$sid)))"
);

if (!$conn) {
    $e = oci_error();
    die("Nem sikerült csatlakozni: " . htmlentities($e['message']));
}
?>
