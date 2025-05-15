<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm']))
{

    if (isset($_SESSION['selected_job_id'], $_POST['app_cv']))
    {
        $jobId = (int) $_SESSION['selected_job_id'];
        $cvId = (int) $_POST['app_cv'];

        $sql = "INSERT INTO application (app_ad, app_cv, app_date, app_stat)
                VALUES (:jobId, :cvId, CURRENT_TIMESTAMP, 'Függőben')";

        $stmt = oci_parse($conn, $sql);
        oci_bind_by_name($stmt, ':jobId', $jobId);
        oci_bind_by_name($stmt, ':cvId', $cvId);

        if (oci_execute($stmt, OCI_COMMIT_ON_SUCCESS))
        {
            echo "<p>✅ Sikeres jelentkezés!</p>";
            echo '<a href="showJobs.php">Vissza az állásokhoz</a>';
        }
        else
        {
            $e = oci_error($stmt);
            echo "<p>❌ Hiba: " . $e['message'] . "</p>";
            echo '<a href="show_application_form.php">Vissza</a>';
        }

        oci_free_statement($stmt);
        oci_close($conn);
    } else {
        echo "<p>❌ Hiányzó adatok!</p>";
        echo '<a href="show_application_form.php">Vissza</a>';
    }
} else {
    header("Location: show_application_form.php");
    exit;
}