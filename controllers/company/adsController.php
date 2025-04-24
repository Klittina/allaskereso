<?php
session_start();
include('../../config/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../../views/login.php');
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {

    case 'delete':
        if (isset($_GET['id'])) {
            $ad_id = $_GET['id'];

            $checkQuery = "SELECT ad_id FROM job_advertisement WHERE ad_id = :id AND ad_co = :co";
            $checkStid = oci_parse($conn, $checkQuery);
            oci_bind_by_name($checkStid, ':id', $ad_id);
            oci_bind_by_name($checkStid, ':co', $_SESSION['user_id']);
            oci_execute($checkStid);

            if ($row = oci_fetch_assoc($checkStid)) {
                $delQuery = "DELETE FROM job_advertisement WHERE ad_id = :id";
                $delStid = oci_parse($conn, $delQuery);
                oci_bind_by_name($delStid, ':id', $ad_id);
                oci_execute($delStid);
                $_SESSION['message'] = "Hirdetés sikeresen törölve.";
            } else {
                $_SESSION['error_message'] = "Ehhez a hirdetéshez nincs jogosultságod.";
            }
        }
        break;

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ad_id = $_POST['ad_id'];
            $pay = $_POST['pay'];
            $text = $_POST['text'];
            $position = $_POST['position'];
            $schedule = $_POST['schedule'];
            $qualification = $_POST['qualification'];
            $language = $_POST['language'];
            $status = isset($_POST['status']) ? 1 : 0;

            $updateQuery = "UPDATE job_advertisement SET 
                ad_po = :pos, 
                ad_sch = :sch, 
                ad_qualification = :qual, 
                ad_pay = :pay, 
                ad_lan = :lan, 
                ad_text = :text, 
                ad_status = :status 
                WHERE ad_id = :id AND ad_co = :co";
            $updateStid = oci_parse($conn, $updateQuery);
            oci_bind_by_name($updateStid, ':pos', $position);
            oci_bind_by_name($updateStid, ':sch', $schedule);
            oci_bind_by_name($updateStid, ':qual', $qualification);
            oci_bind_by_name($updateStid, ':pay', $pay);
            oci_bind_by_name($updateStid, ':lan', $language);
            oci_bind_by_name($updateStid, ':text', $text);
            oci_bind_by_name($updateStid, ':status', $status);
            oci_bind_by_name($updateStid, ':id', $ad_id);
            oci_bind_by_name($updateStid, ':co', $_SESSION['user_id']);
            oci_execute($updateStid);

            $deleteNature = oci_parse($conn, "DELETE FROM job_ad_nature WHERE job_ad_id = :id");
            oci_bind_by_name($deleteNature, ':id', $ad_id);
            oci_execute($deleteNature);

            if (isset($_POST['natures'])) {
                foreach ($_POST['natures'] as $nat_id) {
                    $insertNature = oci_parse($conn, "INSERT INTO job_ad_nature (job_ad_id, nat_id) VALUES (:ad_id, :nat_id)");
                    oci_bind_by_name($insertNature, ':ad_id', $ad_id);
                    oci_bind_by_name($insertNature, ':nat_id', $nat_id);
                    oci_execute($insertNature);
                }
            }

            $_SESSION['message'] = "Hirdetés sikeresen frissítve.";
        }
        break;

    default:
        $_SESSION['error_message'] = "Ismeretlen művelet.";
        break;
}

header("Location: ../../views/company/companyads.php");
exit();
