<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Nincs jogosultság.");
}

include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cv_path'])) {
    $userId = $_SESSION['user_id'];
    $cvPath = $_POST['cv_path'];

    // Fájl törlése a szerverről
    $absolutePath = __DIR__ . "/../../" . $cvPath;
    if (file_exists($absolutePath)) {
        unlink($absolutePath);
    }

    // Adatbázisból is töröljük
    $delete_sql = "DELETE FROM cv WHERE cv_user = :user_id AND cv_path = :cv_path";
    $stmt = oci_parse($conn, $delete_sql);
    oci_bind_by_name($stmt, ":user_id", $userId);
    oci_bind_by_name($stmt, ":cv_path", $cvPath);

    if (oci_execute($stmt)) {
        $_SESSION['upload_success'] = "Önéletrajz sikeresen törölve.";
    } else {
        $_SESSION['upload_success'] = "Hiba történt az önéletrajz törlésekor.";
    }

    oci_free_statement($stmt);
    oci_close($conn);

    header("Location: ../../views/dashboard.php");
    exit();
}
?>
