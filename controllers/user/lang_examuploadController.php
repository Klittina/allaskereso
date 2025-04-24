<?php
session_start();
include('../../config/config.php');

$language = $_POST['language'] ?? null;
$level = $_POST['level'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($language) && !empty($level) && !empty($user_id)) {
        $sql = "INSERT INTO language_exam (ex_user, ex_lan, ex_level)
                VALUES (:uid, :lan, :lvl)";

        $stid = oci_parse($conn, $sql);

        oci_bind_by_name($stid, ":uid", $user_id);
        oci_bind_by_name($stid, ":lan", $language);
        oci_bind_by_name($stid, ":lvl", $level);

        if (oci_execute($stid)) {
            echo "✅ Sikeresen hozzáadva a nyelvvizsga!";
        } else {
            $e = oci_error($stid);
            echo "❌ Hiba történt: " . $e['message'];
        }

        oci_free_statement($stid);
    } else {
        echo "⚠️ Kérlek, tölts ki minden mezőt!";
    }
} else {
    echo "❌ Érvénytelen kérés!";
}
?>
