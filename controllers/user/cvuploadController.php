<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Bejelentkezés szükséges a feltöltéshez.");
}

include('../../config/config.php');
$userId = $_SESSION['user_id'];

if (!isset($_FILES["fileToUpload"]) || !isset($_POST["cvLanguage"])) {
    die("Hiányzó fájl vagy nyelvi beállítás.");
}

$targetDir = __DIR__ . "/../../cv/"; 
$fileName = uniqid() . "_" . basename($_FILES["fileToUpload"]["name"]); 
$targetFilePath = $targetDir . $fileName;
$relativePath = "cv/" . $fileName; 
$fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

if ($fileType !== "pdf") {
    die("Csak PDF fájlok feltöltése engedélyezett.");
}

if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFilePath)) {
    $conn = oci_connect($username, $password, "(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=$host)(PORT=$port))(CONNECT_DATA=(SID=$sid)))");

    if (!$conn) {
        $e = oci_error();
        die("Nem sikerült csatlakozni az adatbázishoz: " . htmlentities($e['message'], ENT_QUOTES));
    }

    $cvLanguage = $_POST['cvLanguage'];

    $sql = "INSERT INTO cv (cv_user, cv_lan, cv_path) VALUES (:user_id, :lan_id, :path)";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ":user_id", $userId);
    oci_bind_by_name($stmt, ":lan_id", $cvLanguage);
    oci_bind_by_name($stmt, ":path", $relativePath);

    if (oci_execute($stmt)) {
        $_SESSION['upload_success'] = 'A fájl sikeresen feltöltve és mentve.';
        header("Location: ../../views/dashboard.php"); 
        exit();
    } else {
        $e = oci_error($stmt);
        echo "Hiba történt az adatbázis mentése közben: " . htmlentities($e['message'], ENT_QUOTES);
    }

    oci_free_statement($stmt);
    oci_close($conn);
} else {
    echo "Hiba történt a fájl feltöltése során.";
}
?>
