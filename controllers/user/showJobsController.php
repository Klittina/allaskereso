<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

include('../../config/config.php');

function getDropdownValues($column)
{
    $conn = db_connect();
    $sql = "SELECT DISTINCT $column FROM positions ORDER BY $column";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $values = [];
    while ($row = oci_fetch_array($stmt, OCI_ASSOC)) {
        $values[] = $row[$column];
    }

    oci_free_statement($stmt);
    oci_close($conn);

    return $values;
}

function getFilteredPositions($filters = [])
{
    $conn = db_connect();

    $sql = "SELECT positionName, schedule, qualification, languageName, pay, text, natures
            FROM positions
            WHERE 1=1";

    $params = [];
    foreach (['schedule', 'qualification', 'natures'] as $field)
    {
        if (!empty($filters[$field]))
        {
            $sql .= " AND $field = :$field";
            $params[":$field"] = $filters[$field];
        }
    }

    foreach (['positionName', 'languageName', 'pay', 'text'] as $field)
    {
        if (!empty($filters[$field]))
        {
            $sql .= " AND LOWER($field) LIKE :$field";
            $params[":$field"] = '%' . strtolower($filters[$field]) . '%';
        }
    }

    $stmt = oci_parse($conn, $sql);
    foreach ($params as $key => $val)
    {
        oci_bind_by_name($stmt, $key, $params[$key]);
    }

    oci_execute($stmt);
    $results = [];
    while ($row = oci_fetch_assoc($stmt))
    {
        $results[] = $row;
    }

    oci_free_statement($stmt);
    oci_close($conn);

    return $results;
}
?>