<?php
session_start();
include("../../config/config.php");

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Minden mezőt ki kell tölteni!";
    header("Location: ../../views/company/login.php");
    exit();
}

$sql = "SELECT co_id, email, password, name, accepted FROM company WHERE email = :email";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":email", $email);
oci_execute($stid);

$row = oci_fetch_assoc($stid);

if (!$row) {
    $_SESSION['error'] = "Nincs ilyen email címmel regisztrált cég.";
    header("Location: ../../views/company/login.php");
    exit();
}

if (!password_verify($password, $row['PASSWORD'])) {
    $_SESSION['error'] = "Hibás jelszó.";
    header("Location: ../../views/company/login.php");
    exit();
}

if ((int)$row['ACCEPTED'] === 0) {
    $_SESSION['error'] = "A regisztrációját még nem dolgozták fel. Kérjük, várjon az admin jóváhagyására.";
    header("Location: ../../views/company/login.php");
    exit();
}

$_SESSION['user_id'] = $row['CO_ID'];
$_SESSION['user_email'] = $row['EMAIL'];
$_SESSION['user_name'] = $row['NAME'];
$_SESSION['user_role'] = 'company';

header("Location: ../../views/company/companydashboard.php");
exit();
