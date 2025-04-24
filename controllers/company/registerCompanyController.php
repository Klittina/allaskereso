<?php
session_start();
include("../../config/config.php");

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$tax_num = $_POST['tax_num'] ?? '';
$co_firstname = $_POST['co_firstname'] ?? '';
$co_lastname = $_POST['co_lastname'] ?? '';
$co_phone = $_POST['co_phone'] ?? '';
$country = $_POST['country'] ?? '';
$city = $_POST['city'] ?? '';
$zipcode = $_POST['zipcode'] ?? '';
$street = $_POST['street'] ?? '';
$num = $_POST['num'] ?? '';

// Adatok ellenőrzése
if (empty($name) || empty($email) || empty($password) || empty($password_confirm) || empty($tax_num) || empty($co_firstname) || empty($co_lastname) || empty($co_phone) || empty($country) || empty($city) || empty($zipcode) || empty($street) || empty($num)) {
    $_SESSION['error'] = "Minden mezőt ki kell tölteni!";
    header("Location: ../views/register_company.php");
    exit();
}

// Jelszavak egyezősége
if ($password !== $password_confirm) {
    $_SESSION['error'] = "A két jelszó nem egyezik!";
    header("Location: ../views/register_company.php");
    exit();
}

// ✅ Duplikált email / adószám / telefonszám ellenőrzése itt jön:
$check_sql = "SELECT email, tax_num, co_phone FROM company WHERE email = :email OR tax_num = :tax_num OR co_phone = :co_phone";
$check_stid = oci_parse($conn, $check_sql);
oci_bind_by_name($check_stid, ":email", $email);
oci_bind_by_name($check_stid, ":tax_num", $tax_num);
oci_bind_by_name($check_stid, ":co_phone", $co_phone);
oci_execute($check_stid);

if ($existing = oci_fetch_assoc($check_stid)) {
    if ($existing['EMAIL'] == $email) {
        $_SESSION['error'] = "Ez az e-mail cím már foglalt.";
    } elseif ($existing['TAX_NUM'] == $tax_num) {
        $_SESSION['error'] = "Ez az adószám már szerepel az adatbázisban.";
    } elseif ($existing['CO_PHONE'] == $co_phone) {
        $_SESSION['error'] = "Ez a telefonszám már használatban van.";
    } else {
        $_SESSION['error'] = "Duplikált adatot adtál meg.";
    }
    header("Location: ../../views/company/register.php");
    exit();
}


// Jelszó titkosítása
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Cég regisztrálása
$sql = "INSERT INTO company (email, password, name, tax_num, co_firstname, co_lastname, co_phone, country, city, zipcode, street, num, accepted) 
        VALUES (:email, :password, :name, :tax_num, :co_firstname, :co_lastname, :co_phone, :country, :city, :zipcode, :street, :num, 0)";

$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":email", $email);
oci_bind_by_name($stid, ":password", $hashed_password);
oci_bind_by_name($stid, ":name", $name);
oci_bind_by_name($stid, ":tax_num", $tax_num);
oci_bind_by_name($stid, ":co_firstname", $co_firstname);
oci_bind_by_name($stid, ":co_lastname", $co_lastname);
oci_bind_by_name($stid, ":co_phone", $co_phone);
oci_bind_by_name($stid, ":country", $country);
oci_bind_by_name($stid, ":city", $city);
oci_bind_by_name($stid, ":zipcode", $zipcode);
oci_bind_by_name($stid, ":street", $street);
oci_bind_by_name($stid, ":num", $num);

oci_execute($stid);
oci_commit($conn); // EZ KELL!

if (oci_execute($stid)) {
    oci_commit($conn);
    $_SESSION['success'] = "A cég sikeresen regisztrálva lett!";
    header("Location: ../../views/login.php");
    exit();
} else {
    $e = oci_error($stid);
    $_SESSION['error'] = "Hiba történt: " . htmlentities($e['message']);
    header("Location: ../../views/company/register.php");
    exit();
}


oci_free_statement($stid);
oci_close($conn);
