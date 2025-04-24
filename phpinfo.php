<?php
$conn = oci_connect('KRISZTINA', 'KRISZTINA', 'localhost/XE');

if (!$conn) {
    $e = oci_error();
    die("Nem sikerült csatlakozni: " . htmlentities($e['message'], ENT_QUOTES));
} else {
    echo "✅ Sikeres csatlakozás az Oracle adatbázishoz!";
}

// Lekérdezzük az összes táblát
$query = "SELECT table_name FROM user_tables";
$stid = oci_parse($conn, $query);
oci_execute($stid);

// Az összes tábla bejárása
while ($row = oci_fetch_assoc($stid)) {
    echo "Tábla: " . $row['TABLE_NAME'] . "<br>";

    // Az aktuális táblából az adatok lekérdezése
    $table_name = $row['TABLE_NAME'];
    $data_query = "SELECT * FROM $table_name";
    $data_stid = oci_parse($conn, $data_query);
    oci_execute($data_stid);

    // Az adatok megjelenítése
    while ($data_row = oci_fetch_assoc($data_stid)) {
        echo "<pre>";
        print_r($data_row);
        echo "</pre>";
    }

    echo "<hr>";
}

// Bezárjuk a kapcsolatot
oci_free_statement($stid);
oci_free_statement($data_stid);
oci_close($conn);
?>
