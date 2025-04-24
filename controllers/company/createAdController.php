<?php
session_start();
include('../../config/config.php');

// Csak bejelentkezett cég használhatja
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../../views/login.php');
    exit();
}

// A form adatainak lekérése
$position = $_POST['position'];
$schedule = $_POST['schedule'];
$qualification = $_POST['qualification'];
$language = $_POST['language'];
$pay = $_POST['pay'];
$text = $_POST['text'];
$natures = isset($_POST['natures']) ? $_POST['natures'] : []; // Ha nincs kiválasztva természet, akkor üres tömb

// Hirdetés létrehozása
$query = "INSERT INTO job_advertisement (ad_co, ad_po, ad_sch, ad_qualification, ad_pay, ad_status, ad_lan, ad_date, ad_text) 
          VALUES (:ad_co, :ad_po, :ad_sch, :ad_qualification, :ad_pay, 0, :ad_lan, SYSDATE, :ad_text)
          RETURNING ad_id INTO :ad_id";

$stid = oci_parse($conn, $query);

// Paraméterek kötése
oci_bind_by_name($stid, ":ad_co", $_SESSION['user_id']); // Cég azonosítója
oci_bind_by_name($stid, ":ad_po", $position);
oci_bind_by_name($stid, ":ad_sch", $schedule);
oci_bind_by_name($stid, ":ad_qualification", $qualification);
oci_bind_by_name($stid, ":ad_pay", $pay);
oci_bind_by_name($stid, ":ad_lan", $language);
oci_bind_by_name($stid, ":ad_text", $text);

// A visszatérő ad_id változó lekötése
oci_bind_by_name($stid, ":ad_id", $ad_id, 32); // A változó típusa és mérete (32 lehet egy megfelelő méret)

if (oci_execute($stid)) {
    // Ha sikeres, akkor az ad_id változó tartalmazza a beszúrt rekord ID-ját
    // Most a természetek (natures) hozzáadása
    foreach ($natures as $nat_id) {
        $natureQuery = "INSERT INTO job_ad_nature (job_ad_id, nat_id) VALUES (:job_ad_id, :nat_id)";
        $natureStid = oci_parse($conn, $natureQuery);
        
        oci_bind_by_name($natureStid, ":job_ad_id", $ad_id);  // Az új hirdetés ID-ja
        oci_bind_by_name($natureStid, ":nat_id", $nat_id);
        
        oci_execute($natureStid);
    }

    // Sikeres üzenet beállítása
    $_SESSION['message'] = 'A hirdetés sikeresen létrejött!';
    header('Location: ../../views/company/createad.php');
    exit();
} else {
    // Hiba üzenet beállítása
    $_SESSION['error_message'] = 'Hiba történt a hirdetés létrehozása során. Kérjük próbálja újra!';
    header('Location: ../../views/company/createad.php');
    exit();
}
?>
