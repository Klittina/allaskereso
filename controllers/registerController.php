<?php
session_start();

$conn = oci_connect('KRISZTINA', 'KRISZTINA', 'localhost/XE');
if (!$conn) {
    $_SESSION['error'] = 'Nem sikerült kapcsolódni az adatbázishoz.';
    header("Location: ../views/register.php");
    exit();
}

$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$password = $_POST['password'];
$password_confirm = $_POST['password_confirm'];
$phone = $_POST['phone'];
$birth_date = $_POST['birth_date'];

if ($password !== $password_confirm) {
    $_SESSION['error'] = "A jelszavak nem egyeznek meg!";
    header("Location: ../views/register.php");
    exit();
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (firstname, lastname, email, password, phone, birth_date, role, status)
        VALUES (:firstname, :lastname, :email, :password, :phone, TO_DATE(:birth_date, 'YYYY-MM-DD'), 'user', 1)";

$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":firstname", $firstname);
oci_bind_by_name($stid, ":lastname", $lastname);
oci_bind_by_name($stid, ":email", $email);
oci_bind_by_name($stid, ":password", $hashed_password);
oci_bind_by_name($stid, ":phone", $phone);
oci_bind_by_name($stid, ":birth_date", $birth_date);

if (oci_execute($stid)) {
    $_SESSION['success'] = "Sikeres regisztráció! Jelentkezz be.";
    header("Location: ../views/login.php");
} else {
    $e = oci_error($stid);
    $_SESSION['error'] = "Hiba történt: " . htmlentities($e['message'], ENT_QUOTES);
    header("Location: ../views/register.php");
}

oci_free_statement($stid);
oci_close($conn);
exit();
?>
