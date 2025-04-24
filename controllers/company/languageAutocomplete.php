<?php
include('../../config/config.php');

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = strtolower($_GET['q']);
$query = "SELECT lan_name FROM language WHERE LOWER(lan_name) LIKE :query";
$stid = oci_parse($conn, $query);
$searchTerm = '%' . $q . '%';
oci_bind_by_name($stid, ":query", $searchTerm);
oci_execute($stid);

$results = [];
while ($row = oci_fetch_assoc($stid)) {
    $results[] = $row['LAN_NAME'];
}

echo json_encode($results);
?>
