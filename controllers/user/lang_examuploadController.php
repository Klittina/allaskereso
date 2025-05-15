<?php
session_start();

include('../../config/config.php');

// 1. Kapcsolódás az adatbázishoz
$conn = oci_connect($username, $password, "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$sid)))");
if (!$conn) {
    $e = oci_error();
    die("Nem sikerült csatlakozni: " . htmlentities($e['message']));
}

// 2. Ellenőrzés: be vagyunk jelentkezve?
if (!isset($_SESSION['user_id'])) {
    die("Nincs bejelentkezve.");
}

// 3. POST adatok ellenőrzése
$userId   = $_SESSION['user_id'];
$language = $_POST['language'] ?? null;
$level    = $_POST['level'] ?? null;
$exam_date = $_POST['exam_date'] ?? null;

if (!$language || !$level || !$exam_date) {
    die("Hiányzó adat: minden mezőt ki kell tölteni.");
}

// 3.1. Vizsga szint validálása (backend validáció)
$level = trim($level);
$level = mb_strtolower($level, 'UTF-8');
$allowed_levels = ['alap', 'közép', 'emelt'];
if (!in_array($level, $allowed_levels, true)) {
    die("Hibás vizsga szint! Kérlek, válassz az alábbi lehetőségek közül: alap, közép, emelt.");
}

// 4. Dátum konvertálása Oracle formátumra
try {
    $dateObj = new DateTime($exam_date);
    // Az Oracle által elvárt formátum: 'd-M-Y', például: 15-MAY-2025
    $exam_date_oracle = strtoupper($dateObj->format('d-M-Y'));
} catch (Exception $ex) {
    die("Érvénytelen dátum formátum!");
}

// 5. SQL lekérdezés készítése
$sql = "INSERT INTO language_exam (ex_user, ex_lan, ex_level, ex_date) 
        VALUES (:user_id, :lan_id, :exlevel, TO_DATE(:exam_date, 'DD-MON-YYYY'))";

// 6. OCI parszolás
$stmt = oci_parse($conn, $sql);
if (!$stmt) {
    $e = oci_error($conn);
    die("OCI parse hiba: " . htmlentities($e['message']));
}

// 7. Bind változók hozzárendelése
oci_bind_by_name($stmt, ":user_id", $userId);
oci_bind_by_name($stmt, ":lan_id", $language);
oci_bind_by_name($stmt, ":exlevel", $level);
oci_bind_by_name($stmt, ":exam_date", $exam_date_oracle);

// 8. Lekérdezés futtatása
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die("OCI execute hiba: " . htmlentities($e['message']));
}

// 9. Commit
oci_commit($conn);

// 10. Takarítás
oci_free_statement($stmt);
oci_close($conn);

echo "Sikeres nyelvvizsga hozzáadás!";
header("Location: ../../views/dashboard.php"); 
?>
