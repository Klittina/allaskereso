<?php
session_start();
include('../../config/config.php');

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];
$phone = $_POST['phone'];
$birth_date = $_POST['birth_date'];

if ($password !== $password_confirm) {
    $_SESSION['error'] = "A két jelszó nem egyezik!";
    header("Location: ../../views/admin/admindashboard.php");
    exit();
}

$sql_check = "SELECT * FROM users WHERE email = :email";
$stid_check = oci_parse($conn, $sql_check);
oci_bind_by_name($stid_check, ":email", $email);
oci_execute($stid_check);

if (oci_fetch_assoc($stid_check)) {
    $_SESSION['error'] = "Ez az email már regisztrálva van!";
    header("Location: ../../views/admin/admindashboard.php");
    exit();
}

$hashed_password = password_hash($password, PASSWORD_BCRYPT);

$sql_insert = "INSERT INTO users (firstname, lastname, email, password, phone, birth_date, role, status)
               VALUES (:firstname, :lastname, :email, :password, :phone, TO_DATE(:birth_date, 'YYYY-MM-DD'), 'admin', 1)";
$stid_insert = oci_parse($conn, $sql_insert);
oci_bind_by_name($stid_insert, ":firstname", $firstname);
oci_bind_by_name($stid_insert, ":lastname", $lastname);
oci_bind_by_name($stid_insert, ":email", $email);
oci_bind_by_name($stid_insert, ":password", $hashed_password);
oci_bind_by_name($stid_insert, ":phone", $phone);
oci_bind_by_name($stid_insert, ":birth_date", $birth_date);

oci_execute($stid_insert);

$_SESSION['success'] = "Új admin sikeresen hozzáadva!";
header("Location: ../../views/admin/admindashboard.php");
exit();
?>
