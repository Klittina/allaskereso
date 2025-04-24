<?php
session_start();
include('../../config/config.php');

// Csak bejelentkezett cég használhatja
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../../views/login.php');
    exit();
}

// A form adatainak lekérése
$positionName = trim($_POST['position']); // szövegként jön a pozíció
$schedule = $_POST['schedule'];
$qualification = $_POST['qualification'];
$language = $_POST['language'];
$pay = $_POST['pay'];
$text = $_POST['text'];
$natures = isset($_POST['natures']) ? $_POST['natures'] : [];

// Először megnézzük, hogy létezik-e ez a pozíció
$checkQuery = "SELECT job_id FROM job_positions WHERE LOWER(job_name) = LOWER(:job_name)";
$checkStid = oci_parse($conn, $checkQuery);
oci_bind_by_name($checkStid, ":job_name", $positionName);
oci_execute($checkStid);

$row = oci_fetch_assoc($checkStid);

if ($row) {
    // Ha létezik, használjuk a meglévő job_id-t
    $positionId = $row['JOB_ID'];
} else {
    // Ha nem létezik, beszúrjuk és lekérjük az új ID-t
    $insertPosQuery = "INSERT INTO job_positions (job_name) VALUES (:job_name) RETURNING job_id INTO :job_id";
    $insertPosStid = oci_parse($conn, $insertPosQuery);
    oci_bind_by_name($insertPosStid, ":job_name", $positionName);
    oci_bind_by_name($insertPosStid, ":job_id", $positionId, 32);
    oci_execute($insertPosStid);
}

// Hirdetés létrehozása
$query = "INSERT INTO job_advertisement (ad_co, ad_po, ad_sch, ad_qualification, ad_pay, ad_status, ad_lan, ad_date, ad_text) 
          VALUES (:ad_co, :ad_po, :ad_sch, :ad_qualification, :ad_pay, 0, :ad_lan, SYSDATE, :ad_text)
          RETURNING ad_id INTO :ad_id";

$stid = oci_parse($conn, $query);

// Paraméterek kötése
oci_bind_by_name($stid, ":ad_co", $_SESSION['user_id']);
oci_bind_by_name($stid, ":ad_po", $positionId); // A lekért vagy beszúrt pozíció ID
oci_bind_by_name($stid, ":ad_sch", $schedule);
oci_bind_by_name($stid, ":ad_qualification", $qualification);
oci_bind_by_name($stid, ":ad_pay", $pay);
oci_bind_by_name($stid, ":ad_lan", $language);
oci_bind_by_name($stid, ":ad_text", $text);
oci_bind_by_name($stid, ":ad_id", $ad_id, 32);

if (oci_execute($stid)) {
    foreach ($natures as $nat_id) {
        $natureQuery = "INSERT INTO job_ad_nature (job_ad_id, nat_id) VALUES (:job_ad_id, :nat_id)";
        $natureStid = oci_parse($conn, $natureQuery);
        oci_bind_by_name($natureStid, ":job_ad_id", $ad_id);
        oci_bind_by_name($natureStid, ":nat_id", $nat_id);
        oci_execute($natureStid);
    }

    $_SESSION['message'] = 'A hirdetés sikeresen létrejött!';
    header('Location: ../../views/company/createad.php');
    exit();
} else {
    $_SESSION['error_message'] = 'Hiba történt a hirdetés létrehozása során. Kérjük próbálja újra!';
    header('Location: ../../views/company/createad.php');
    exit();
}
?>
