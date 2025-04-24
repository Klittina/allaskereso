<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: login.php');
    exit();
}

include('../config/config.php');

// Lekérdezzük a felhasználó adatait
$user_id = $_SESSION['user_id'];

// Felhasználó alapadatok lekérdezése
$user_sql = "SELECT * FROM users WHERE user_id = :id";
$stid = oci_parse($conn, $user_sql);
oci_bind_by_name($stid, ":id", $user_id);
if (!oci_execute($stid)) {
    die("Felhasználói adatok lekérdezése nem sikerült: " . oci_error($stid)['message']);
}
$user = oci_fetch_assoc($stid);

// Iskolák lekérdezése
$school_sql = "
SELECT s.name, s.country, st.stud_start, st.stud_end, st.stud_grade 
FROM study st
JOIN schools s ON st.stud_where = s.sc_id
WHERE st.stud_who = :id";
$stid_sch = oci_parse($conn, $school_sql);
oci_bind_by_name($stid_sch, ":id", $user_id);
if (!oci_execute($stid_sch)) {
    die("Iskolák lekérdezése nem sikerült: " . oci_error($stid_sch)['message']);
}

// Nyelvvizsgák lekérdezése
$lang_sql = "
SELECT l.lan_name, le.ex_level 
FROM language_exam le
JOIN language l ON le.ex_lan = l.lan_id
WHERE le.ex_user = :id";
$stid_lang = oci_parse($conn, $lang_sql);
oci_bind_by_name($stid_lang, ":id", $user_id);
if (!oci_execute($stid_lang)) {
    die("Nyelvvizsgák lekérdezése nem sikerült: " . oci_error($stid_lang)['message']);
}

// Önéletrajzok lekérdezése
$cv_sql = "
SELECT c.cv_path, l.lan_name 
FROM cv c
LEFT JOIN language l ON c.cv_lan = l.lan_id
WHERE c.cv_user = :id";
$stid_cv = oci_parse($conn, $cv_sql);
oci_bind_by_name($stid_cv, ":id", $user_id);
if (!oci_execute($stid_cv)) {
    die("Önéletrajzok lekérdezése nem sikerült: " . oci_error($stid_cv)['message']);
}

// Adatok átadása a nézetnek
$data = [
    'user' => $user,
    'schools' => $stid_sch,
    'languages' => $stid_lang,
    'cv' => $stid_cv
];

// Összesítjük a lekérdezéseket
oci_free_statement($stid);
oci_free_statement($stid_sch);
oci_free_statement($stid_lang);
oci_free_statement($stid_cv);
oci_close($conn);

return $data; // Adatok visszaadása a nézetnek
?>
