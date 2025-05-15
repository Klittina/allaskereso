<?php
include('../../config/config.php');


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['job_id'], $_POST['action'])) {
    $jobId = $_POST['job_id'];
    $action = $_POST['action'];

    if ($action === 'accept') {
        // LÉPÉS 1: Megkeressük az álláshirdetéshez tartozó ad_po-t (job_positions.job_id)
        $selectSql = "SELECT ad_po FROM job_advertisement WHERE ad_id = :jobId";
        $selectStid = oci_parse($conn, $selectSql);
        oci_bind_by_name($selectStid, ":jobId", $jobId);
        oci_execute($selectStid);
        $row = oci_fetch_assoc($selectStid);

        if ($row && $row['AD_PO']) {
            $jobPositionId = $row['AD_PO'];

            // LÉPÉS 2: Frissítjük a job_positions táblában a job_name_valid értéket 1-re
            $updateSql = "UPDATE job_positions SET job_name_valid = 1 WHERE job_id = :jobPositionId";
            $updateStid = oci_parse($conn, $updateSql);
            oci_bind_by_name($updateStid, ":jobPositionId", $jobPositionId);
            if (oci_execute($updateStid)) {
                echo "Sikeresen elfogadva.";
               header("Location: ../../views/admin/admindashboard.php");
exit();
            } else {
                echo "Hiba a pozíció érvényesítésénél.";
            }
        } else {
            echo "Nem található pozíció.";
        }

    } elseif ($action === 'reject') {
        // Töröljük az álláshirdetést
        $deleteSql = "DELETE FROM job_advertisement WHERE ad_id = :jobId";
        $deleteStid = oci_parse($conn, $deleteSql);
        oci_bind_by_name($deleteStid, ":jobId", $jobId);
        if (oci_execute($deleteStid)) {
            echo "Sikeresen elutasítva.";
            header("Location: ../../views/admin/admindashboard.php");
        } else {
            echo "Hiba a törlésnél.";
            header("Location: ../../views/admin/admindashboard.php");
        }
    }
} else {
    echo "Hibás kérés.";
}
?>
