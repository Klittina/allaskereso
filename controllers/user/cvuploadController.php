<?php
session_start();

// Ellenőrizzük, hogy be van-e jelentkezve a felhasználó
if (!isset($_SESSION['user_id'])) {
    die("Bejelentkezés szükséges a feltöltéshez.");
}

include('../../config/config.php'); // Adatbázis kapcsolat
$userId = $_SESSION['user_id'];

if (!isset($_FILES["fileToUpload"]) || !isset($_POST["cvLanguage"])) {
    die("Hiányzó fájl vagy nyelvi beállítás.");
}

$targetDir = __DIR__ . "/../../cv/"; // A projekt gyökérben lévő 'cv' mappa
$fileName = uniqid() . "_" . basename($_FILES["fileToUpload"]["name"]); // Egyedi fájlnév
$targetFilePath = $targetDir . $fileName;
$relativePath = "cv/" . $fileName; // Ez kerül az adatbázisba
$fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

// Ellenőrizzük, hogy PDF fájlt töltenek-e fel
if ($fileType !== "pdf") {
    die("Csak PDF fájlok feltöltése engedélyezett.");
}

// Mappát hozunk létre, ha nem létezik
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// A fájl feltöltése
if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFilePath)) {
    // Kapcsolódás az Oracle adatbázishoz
    $conn = oci_connect($username, $password, "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$sid)))");

    if (!$conn) {
        $e = oci_error();
        die("Nem sikerült csatlakozni az adatbázishoz: " . htmlentities($e['message'], ENT_QUOTES));
    }

    // A nyelvet a POST adatból vesszük
    $cvLanguage = $_POST['cvLanguage'];

    // SQL parancs előkészítése
    $sql = "INSERT INTO cv (cv_user, cv_lan, cv_path) VALUES (:user_id, :lan_id, :path)";
    $stmt = oci_parse($conn, $sql);

    // Paraméterek bindolása
    oci_bind_by_name($stmt, ":user_id", $userId);
    oci_bind_by_name($stmt, ":lan_id", $cvLanguage);
    oci_bind_by_name($stmt, ":path", $relativePath);

    // A művelet végrehajtása
    if (oci_execute($stmt)) {
        // Ha sikerült, irányítsuk át a dashboardra és jelezzük a sikerességet
        $_SESSION['upload_success'] = 'A fájl sikeresen feltöltve és mentve.';
        header("Location: ../../views/dashboard.php"); // A dashboard oldalra irányítjuk
        exit();
    } else {
        $e = oci_error($stmt);
        echo "Hiba történt az adatbázis mentése közben: " . htmlentities($e['message'], ENT_QUOTES);
    }

    // Kapcsolat bezárása
    oci_free_statement($stmt);
    oci_close($conn);
} else {
    echo "Hiba történt a fájl feltöltése során.";
}
?>
