<?php
$conn = oci_connect('KRISZTINA', 'KRISZTINA', 'localhost:1521/xe');

if (!$conn) {
    $e = oci_error();
    die("❌ Nem sikerült csatlakozni: " . htmlentities($e['message'], ENT_QUOTES));
} else {
    echo "✅ Sikeres csatlakozás az Oracle adatbázishoz!<br>";
}

// Lekérdezzük a saját táblákat
$query = "SELECT table_name FROM user_tables";
$stid = oci_parse($conn, $query);
$execute_result = oci_execute($stid);

if (!$execute_result) {
    $e = oci_error($stid);
    die("❌ Hiba a táblák lekérdezése közben: " . htmlentities($e['message'], ENT_QUOTES));
}

echo "📋 Táblák lekérdezése sikeres:<br><br>";

// Az összes tábla bejárása
while ($row = oci_fetch_assoc($stid)) {
    $table_name = strtoupper($row['TABLE_NAME']);  // Biztos, ami biztos
    echo "<strong>📄 Tábla: $table_name</strong><br>";

    // Az aktuális táblából az adatok lekérdezése
    $data_query = "SELECT * FROM \"$table_name\""; // idézőjel, ha kisbetűs vagy spec. karakteres lenne
    $data_stid = oci_parse($conn, $data_query);
    $execute_result = @oci_execute($data_stid); // @ hogy ne dobjon warningot

    if (!$execute_result) {
        $e = oci_error($data_stid);
        echo "⚠️ Hiba a $table_name tábla lekérdezése közben: " . htmlentities($e['message'], ENT_QUOTES) . "<br><br>";
    } else {
        $data_found = false;
        while ($data_row = oci_fetch_assoc($data_stid)) {
            echo "<pre>";
            print_r($data_row);
            echo "</pre>";
            $data_found = true;
        }
        if (!$data_found) {
            echo "ℹ️ Nincsenek adatok a $table_name táblában.<br>";
        }
    }

    echo "<hr>";
}

oci_free_statement($stid);
oci_close($conn);
?>
