<?php
session_start();

// Ellenőrizzük, hogy a felhasználó cégként van-e bejelentkezve
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../login.php');
    exit();
}

include('../../config/config.php');

// Ha a formot elküldték, akkor végezzük el az adatbázis frissítést vagy törlést
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // Profil törlés
        $company_id = $_SESSION['user_id'];

        // SQL lekérdezés a cég profiljának törlésére
        $sql = "DELETE FROM company WHERE co_id = :co_id";
        $stid = oci_parse($conn, $sql);
        oci_bind_by_name($stid, ":co_id", $company_id);

        if (oci_execute($stid)) {
            // Ha sikeres a törlés, kijelentkeztetjük a felhasználót és visszairányítjuk a bejelentkezési oldalra
            session_destroy();
            header('Location: ../login.php');
            exit();
        } else {
            echo "Hiba történt a profil törlésénél!";
        }
    } else {
        // Profil módosítása
        $name = $_POST['name'];
        $email = $_POST['email'];
        $tax_num = $_POST['tax_num'];
        $co_firstname = $_POST['co_firstname'];
        $co_lastname = $_POST['co_lastname'];
        $co_phone = $_POST['co_phone'];
        $city = $_POST['city'];

        $company_id = $_SESSION['user_id'];

        // SQL lekérdezés a cég adatainak frissítésére
        $sql = "UPDATE company 
                SET NAME = :name, EMAIL = :email, TAX_NUM = :tax_num, CO_FIRSTNAME = :co_firstname, 
                    CO_LASTNAME = :co_lastname, CO_PHONE = :co_phone, CITY = :city
                WHERE co_id = :co_id";

        $stid = oci_parse($conn, $sql);
        oci_bind_by_name($stid, ":name", $name);
        oci_bind_by_name($stid, ":email", $email);
        oci_bind_by_name($stid, ":tax_num", $tax_num);
        oci_bind_by_name($stid, ":co_firstname", $co_firstname);
        oci_bind_by_name($stid, ":co_lastname", $co_lastname);
        oci_bind_by_name($stid, ":co_phone", $co_phone);
        oci_bind_by_name($stid, ":city", $city);
        oci_bind_by_name($stid, ":co_id", $company_id);

        if (oci_execute($stid)) {
            // Ha sikeres a frissítés, akkor irányítjuk a felhasználót a cég dashboardjára
            header('Location: ../../views/company/companydashboard.php');
            exit();
        } else {
            echo "Hiba történt a profil frissítésekor!";
        }
    }
}
?>
