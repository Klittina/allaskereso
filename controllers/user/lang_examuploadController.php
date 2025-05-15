<?php
session_start();
include('../../config/config.php');

$language = $_POST['language'] ?? null;
$level = $_POST['level'] ?? null;
$date = $_POST['exam_date'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($language) && !empty($level) && !empty($date) && !empty($user_id)) {
        $sql = "INSERT INTO language_certificates (user_id, language, level, exam_date)
                VALUES (:user_id, :language, :level, TO_DATE(:exam_date, 'YYYY-MM-DD'))";

        $stid = oci_parse($conn, $sql);

        oci_bind_by_name($stid, ":user_id", $user_id);
        oci_bind_by_name($stid, ":language", $language);
        oci_bind_by_name($stid, ":level", $level);
        oci_bind_by_name($stid, ":exam_date", $date);

        if (oci_execute($stid)) {
            echo "Sikeresen hozzáadva a nyelvvizsga!";
        } else {
            $e = oci_error($stid);
            echo "Hiba történt: " . $e['message'];
        }

        oci_free_statement($stid);
    } else {
        echo "Kérlek, tölts ki minden mezőt!";
    }
} else {
    echo "Érvénytelen kérés!";
}
?>