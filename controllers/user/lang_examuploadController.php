<?php
session_start();
include('../../config/config.php');

// 1. Ellenőrzés: be vagyunk jelentkezve?
if (!isset($_SESSION['user_id'])) {
    die("Nincs bejelentkezve.");
}

// 2. POST adatok ellenőrzése
$userId    = $_SESSION['user_id'];
$language  = $_POST['language'] ?? null;
$level     = $_POST['level'] ?? null;
$exam_date = $_POST['exam_date'] ?? null;

if (!$language || !$level || !$exam_date) {
    die("Hiányzó adat: minden mezőt ki kell tölteni.");
}

// 3. Vizsga szint validálása
$level = trim($level);
$level = mb_strtolower($level, 'UTF-8');
$allowed_levels = ['alap', 'közép', 'emelt'];
if (!in_array($level, $allowed_levels, true)) {
    die("Hibás vizsga szint! Kérlek, válassz az alábbi lehetőségek közül: alap, közép, emelt.");
}

// 4. Dátum konvertálása Oracle formátumra
try {
    $dateObj = new DateTime($exam_date);
    $exam_date_oracle = strtoupper($dateObj->format('d-M-Y')); // pl. 15-MAY-2025
} catch (Exception $ex) {
    die("Érvénytelen dátum formátum!");
}

// 5. SQL lekérdezés
$sql = "INSERT INTO language_exam (ex_user, ex_lan, ex_level, ex_date) 
        VALUES (:user_id, :lan_id, :exlevel, TO_DATE(:exam_date, 'DD-MON-YYYY'))";

// 6. Lekérdezés előkészítése
$stmt = oci_parse($conn, $sql);
if (!$stmt) {
    $e = oci_error($conn);
    die("OCI parse hiba: " . htmlentities($e['message']));
}

// 7. Bindelés
oci_bind_by_name($stmt, ":user_id", $userId);
oci_bind_by_name($stmt, ":lan_id", $language);
oci_bind_by_name($stmt, ":exlevel", $level);
oci_bind_by_name($stmt, ":exam_date", $exam_date_oracle);

// 8. Futtatás és trigger hiba feldolgozása
// 8. Futtatás és trigger hiba feldolgozása
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    $errorMsg = $e['message'];

    if (strpos($errorMsg, 'ORA-20001') !== false) {
        // Trigger által dobott hiba
        preg_match('/ORA-20001: (.+?)ORA-06512:/s', $errorMsg, $matches);
        $userFriendlyMsg = isset($matches[1]) ? trim($matches[1]) : "Adatbázis hiba történt.";

        echo "<script>alert('❌ $userFriendlyMsg'); window.history.back();</script>";
        exit;
    } else {
        $safeError = htmlentities($errorMsg);
        echo "<script>alert('Hiba történt: $safeError'); window.history.back();</script>";
        exit;
    }
}


// 9. Commit és zárás
oci_commit($conn);
oci_free_statement($stmt);
oci_close($conn);

// 10. Sikeres visszajelzés és átirányítás
echo "Sikeres nyelvvizsga hozzáadás!";
header("Location: ../../views/dashboard.php");
exit;
?>
