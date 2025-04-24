<?php
session_start();
include('../../config/config.php');

$name = 'name';
$lang = 'lang';
$path = 'path';

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    if (!empty($name) && !empty($lang) && !empty($path))
    {
        $sql = "INSERT INTO cv VALUES (:name, :language, :path)";
        $stid = oci_parse($conn, $sql);

        oci_bind_by_name($stid, ":name", $name);
        oci_bind_by_name($stid, ":language", $lang);
        oci_bind_by_name($stid, ":path", $path);

        if (oci_execute($stid))
        {
            echo "Sikeresen feltöltve.";
        }
        else
        {
            $e = oci_error($stid);
            echo "uh oh nem sikerult :D: " . $e['message'];
        }

        oci_free_statement($stid);
    }
    else
    {
        echo "Minden részét ki kell tölteni!";
    }
}
else
{
    echo "Nem POST kérés";
}
?>