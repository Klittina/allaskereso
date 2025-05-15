<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit;
}

include('../../config/config.php');

$userId = $_SESSION['user_id'];

// POST adatok beolvasása, alap validáció
$school = $_POST['school'] ?? '';
$qualification_type = $_POST['qualification_type'] ?? '';
$qualification_name = $_POST['qualification_name'] ?? '';
$grade = $_POST['grade'] ?? null;
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';

$errors = [];

// Kötelező mezők ellenőrzése
if (!$school) $errors[] = "Iskola megadása kötelező.";
if (!$qualification_type) $errors[] = "Végzettség típusa megadása kötelező.";
if (!$qualification_name) $errors[] = "Végzettség neve megadása kötelező.";
if (!$start_date) $errors[] = "Kezdés dátuma megadása kötelező.";
if (!$end_date) $errors[] = "Befejezés dátuma megadása kötelező.";

// Dátumok validálása (start_date <= end_date)
if ($start_date && $end_date && strtotime($start_date) > strtotime($end_date)) {
    $errors[] = "A kezdés dátuma nem lehet későbbi, mint a befejezés dátuma.";
}

if (!empty($errors)) {
    $_SESSION['study_errors'] = $errors;
    header("Location: ../../views/user/newschool.php");
    exit;
}

// Itt jön az SQL beszúrás:

$sql = "INSERT INTO study (stud_who, stud_where, stud_type, stud_name, stud_grade, stud_start, stud_end) 
        VALUES (:user_id, :school_id, :qualification_type_id, :qualification_name_id, :grade, TO_DATE(:start_date, 'YYYY-MM-DD'), TO_DATE(:end_date, 'YYYY-MM-DD'))";

$stid = oci_parse($conn, $sql);

if (!$stid) {
    $e = oci_error($conn);
    die("oci_parse error: " . $e['message']);
}

oci_bind_by_name($stid, ':user_id', $userId);
oci_bind_by_name($stid, ':school_id', $school);
oci_bind_by_name($stid, ':qualification_type_id', $qualification_type);
oci_bind_by_name($stid, ':qualification_name_id', $qualification_name);
oci_bind_by_name($stid, ':grade', $grade);
oci_bind_by_name($stid, ':start_date', $start_date);
oci_bind_by_name($stid, ':end_date', $end_date);

$execute = oci_execute($stid, OCI_NO_AUTO_COMMIT);

if ($execute) {
    oci_commit($conn);
    $_SESSION['study_success'] = "A képzettség sikeresen hozzáadva.";
    header("Location: ../../views/user/newschool.php");
    exit;
} else {
    oci_rollback($conn);
    $e = oci_error($stid);
    $_SESSION['study_errors'] = ["Adatbázis hiba: " . $e['message']];
    header("Location: ../../views/user/newschool.php");
    exit;
}
?>
