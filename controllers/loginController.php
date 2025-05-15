<?php
session_start();
include("../config/config.php");

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Minden mezőt ki kell tölteni!";
    header("Location: ../views/login.php");
    exit();
}

$sql = "SELECT * FROM users WHERE email = :email AND status = 1";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":email", $email);
oci_execute($stid);

$user = oci_fetch_assoc($stid);

if ($user && password_verify($password, $user['PASSWORD'])) {
    $_SESSION['user_id'] = $user['USER_ID'];
    $_SESSION['user_name'] = $user['FIRSTNAME'] . " " . $user['LASTNAME'];
    $_SESSION['user_role'] = $user['ROLE']; 

    $update_sql = "UPDATE users SET lastloggedin = CURRENT_TIMESTAMP WHERE user_id = :uid";
    $update_stid = oci_parse($conn, $update_sql);
    oci_bind_by_name($update_stid, ":uid", $user['USER_ID']);
    oci_execute($update_stid);

    if ($user['ROLE'] === 'admin') {
        header("Location: ../views/admin/admindashboard.php");
        exit();
    } else {
        header("Location: ../views/dashboard.php");
        exit();
    }
} else {
    $_SESSION['error'] = "Hibás email vagy jelszó!";
    header("Location: ../views/login.php");
}

oci_free_statement($stid);
oci_close($conn);
?>
