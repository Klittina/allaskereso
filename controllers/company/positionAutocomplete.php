<?php
include('../../config/config.php');

$q = strtolower(trim($_GET['q'] ?? ''));

if (!$q) {
    echo json_encode([]);
    exit;
}

$query = "SELECT job_name FROM job_positions WHERE LOWER(job_name) LIKE :query";
$stid = oci_parse($conn, $query);
$searchTerm = $q . '%';
oci_bind_by_name($stid, ':query', $searchTerm);
oci_execute($stid);

$results = [];
while ($row = oci_fetch_assoc($stid)) {
    $results[] = $row['JOB_NAME'];
}

echo json_encode($results);
?>
