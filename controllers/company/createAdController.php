<?php
session_start();
include('../../config/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../../views/login.php');
    exit();
}

$positionName = trim($_POST['position']); 
$schedule = $_POST['schedule'];
$qualification = $_POST['qualification'];
$languageName = trim($_POST['language']); 
$pay = $_POST['pay'];
$text = $_POST['text'];
$natures = isset($_POST['natures']) ? $_POST['natures'] : [];

$positionCreatedMsg = "";  // Ez tárolja majd az értesítést, ha új pozíció lett létrehozva

// Pozíció ellenőrzése
$checkPosQuery = "SELECT job_id FROM job_positions WHERE LOWER(job_name) = LOWER(:job_name)";
$checkPosStid = oci_parse($conn, $checkPosQuery);
oci_bind_by_name($checkPosStid, ":job_name", $positionName);
oci_execute($checkPosStid);
$rowPos = oci_fetch_assoc($checkPosStid);

if ($rowPos) {
    $positionId = $rowPos['JOB_ID'];
} else {
    // Új pozíció beszúrása
    $insertPosQuery = "INSERT INTO job_positions (job_name, job_name_valid) 
                       VALUES (:job_name, 0) 
                       RETURNING job_id INTO :job_id";
    $insertPosStid = oci_parse($conn, $insertPosQuery);
    oci_bind_by_name($insertPosStid, ":job_name", $positionName);
    oci_bind_by_name($insertPosStid, ":job_id", $positionId, 32);
    oci_execute($insertPosStid);

    // Üzenet beállítása, hogy új pozíció lett mentve
    $positionCreatedMsg = 'Az új pozíció mentésre került, és jóváhagyásra vár.';
}

// Nyelv ellenőrzése és/vagy beszúrása
$checkLanQuery = "SELECT lan_id FROM language WHERE LOWER(lan_name) = LOWER(:lan_name)";
$checkLanStid = oci_parse($conn, $checkLanQuery);
oci_bind_by_name($checkLanStid, ":lan_name", $languageName);
oci_execute($checkLanStid);
$rowLan = oci_fetch_assoc($checkLanStid);

if ($rowLan) {
    $languageId = $rowLan['LAN_ID'];
} else {
    $insertLanQuery = "INSERT INTO language (lan_name) VALUES (:lan_name) RETURNING lan_id INTO :lan_id";
    $insertLanStid = oci_parse($conn, $insertLanQuery);
    oci_bind_by_name($insertLanStid, ":lan_name", $languageName);
    oci_bind_by_name($insertLanStid, ":lan_id", $languageId, 32);
    oci_execute($insertLanStid);
}

$query = "INSERT INTO job_advertisement (ad_co, ad_po, ad_sch, ad_qualification, ad_pay, ad_status, ad_lan, ad_date, ad_text) 
          VALUES (:ad_co, :ad_po, :ad_sch, :ad_qualification, :ad_pay, 0, :ad_lan, SYSDATE, :ad_text)
          RETURNING ad_id INTO :ad_id";

$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":ad_co", $_SESSION['user_id']);
oci_bind_by_name($stid, ":ad_po", $positionId);
oci_bind_by_name($stid, ":ad_sch", $schedule);
oci_bind_by_name($stid, ":ad_qualification", $qualification);
oci_bind_by_name($stid, ":ad_pay", $pay);
oci_bind_by_name($stid, ":ad_lan", $languageId); 
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

    // Ha volt új pozíció, az üzenetet hozzáadjuk a sikeres létrehozás üzenetéhez
    if ($positionCreatedMsg !== "") {
        $_SESSION['message'] = 'A hirdetés sikeresen létrejött! ' . $positionCreatedMsg;
    } else {
        $_SESSION['message'] = 'A hirdetés sikeresen létrejött!';
    }

    header('Location: ../../views/company/createad.php');
    exit();
} else {
    $_SESSION['error_message'] = 'Hiba történt a hirdetés létrehozása során. Kérjük próbálja újra!';
    header('Location: ../../views/company/createad.php');
    exit();
}

?>
