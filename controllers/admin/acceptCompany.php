<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../views/login.php');
    exit();
}

include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['co_id'])) {
    $co_id = $_POST['co_id'];

    $sql = "UPDATE company SET accepted = 1 WHERE co_id = :co_id";
    $stid = oci_parse($conn, $sql);
    oci_bind_by_name($stid, ":co_id", $co_id);

    if (oci_execute($stid)) {
        oci_commit($conn);
        $_SESSION['success'] = "A cég jelentkezése elfogadva.";
    } else {
        $e = oci_error($stid);
        $_SESSION['error'] = "Hiba történt az elfogadás során: " . $e['message'];
    }

    oci_free_statement($stid);
    oci_close($conn);
}

header('Location: ../../views/admin/admindashboard.php');
exit();
