<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

include('../../config/config.php'); // Itt létrejön a $conn

$sql = "SELECT
            ja.ad_id,
            jp.job_name AS positionName,
            js.sch_name AS schedule,
            q.qu_name AS qualification,
            l.lan_name AS languageName,
            ja.ad_pay AS pay,
            ja.ad_text AS text,
            jn.nature_name AS natures
        FROM job_advertisement ja
        LEFT JOIN job_positions jp ON ja.ad_po = jp.job_id
        LEFT JOIN job_schedule js ON ja.ad_sch = js.sch_id
        LEFT JOIN qualification q ON ja.ad_qualification = q.qu_id
        LEFT JOIN language l ON ja.ad_lan = l.lan_id
        LEFT JOIN job_nature jn ON jp.job_nature = jn.nature_id
        WHERE ja.ad_status = 1
        ORDER BY ja.ad_id DESC";

$stmt = oci_parse($conn, $sql);
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die("SQL hiba: " . $e['message']);
}

$jobs = [];
while ($row = oci_fetch_assoc($stmt)) {
    $jobs[] = $row;
}

oci_free_statement($stmt);
oci_close($conn);

// Megjelenítés (például egy HTML nézetben)
foreach ($jobs as $job) {
    echo "<h3>{$job['POSITIONNAME']}</h3>";
    echo "<p><strong>Munkaidő:</strong> {$job['SCHEDULE']}</p>";
    echo "<p><strong>Képzettség:</strong> {$job['QUALIFICATION']}</p>";
    echo "<p><strong>Nyelv:</strong> {$job['LANGUAGENAME']}</p>";
    echo "<p><strong>Fizetés:</strong> {$job['PAY']}</p>";
    echo "<p><strong>Leírás:</strong> {$job['TEXT']}</p>";
    echo "<p><strong>Jelleg:</strong> {$job['NATURES']}</p>";
    echo "<hr>";
}
?>
