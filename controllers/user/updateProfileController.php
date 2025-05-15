<?php
session_start();
require_once '../../config/database.php'; // vagy ahová a DB kapcsolatod van mentve

// Ellenőrizzük, hogy be van-e jelentkezve
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// POST adatok lekérése
$firstname = trim($_POST['firstname'] ?? '');
$lastname = trim($_POST['lastname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');


$errors = [];

if (empty($firstname)) $errors[] = "Vezetéknév kötelező";
if (empty($lastname)) $errors[] = "Keresztnév kötelező";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Érvénytelen e-mail cím";
if (empty($phone)) $errors[] = "Telefonszám kötelező";

// Ha vannak hibák, visszairányítjuk
if (!empty($errors)) {
    $_SESSION['error'] = implode("<br>", $errors);
    header("Location: ../views/profile/edit_profile.php");
    exit();
}

try {
    $stmt = $conn->prepare("UPDATE users SET firstname = ?, lastname = ?, email = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $firstname, $lastname, $email, $phone, $user_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Sikeres mentés!";
    } else {
        $_SESSION['error'] = "Hiba történt frissítés közben.";
    }

    $stmt->close();
} catch (Exception $e) {
    $_SESSION['error'] = "Adatbázis hiba: " . $e->getMessage();
}

// Visszairányítás
header("Location: ../views/profile/edit_profile.php");
exit();