<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../../login.php');
    exit();
}

include('../../config/config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        $user_id = $_SESSION['user_id'];

        $sql = "DELETE FROM user WHERE user_id = :user_id";
        $stid = oci_parse($conn, $sql);
        oci_bind_by_name($stid, ":user_id", $user_id);

        if (oci_execute($stid)) {
            session_destroy();
            header('Location: ../../login.php');
            exit();
        } else {
            echo "Hiba történt a profil törlésénél!";
        }
    } else {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $phone = $_POST['phone'];
        $birth_date = $_POST['birth_date'];

        $user_id = $_SESSION['user_id'];

        $sql = "UPDATE user 
                SET FIRSTNAME = :first_name, LASTNAME = :last_name, EMAIL = :email, PASSWORD = :password,
                    EMAIL = :email, PHONE = :phone, BIRTH_DATE = :birth_date;
                WHERE user_id = :user_id";

        $stid = oci_parse($conn, $sql);
        oci_bind_by_name($stid, ":first_name", $first_name);
        oci_bind_by_name($stid, ":last_name", $last_name);
        oci_bind_by_name($stid, ":email", $email);
        oci_bind_by_name($stid, ":password", $password);
        oci_bind_by_name($stid, ":phone", $phone);
        oci_bind_by_name($stid, ":birth_date", $birth_date);
        oci_bind_by_name($stid, ":user_id", $user_id);

        if (oci_execute($stid)) {
            header('Location: ../../views/dashboard.php');
            exit();
        } else {
            echo "Hiba történt a profil frissítésekor!";
        }
    }
}
?>
