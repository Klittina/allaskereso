<?php
session_start();
<<<<<<< HEAD

=======
>>>>>>> 161bd45b772712d73f556cea9d38b19688d4845d
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
<<<<<<< HEAD
?>

=======

include('../../config/config.php');

// Lekérjük a nyelveket, amik érvényesek
$sql = "SELECT lan_id, lan_name FROM language WHERE lan_name_valid = 1 ORDER BY lan_name";
$stid = oci_parse($conn, $sql);
oci_execute($stid);

$languages = [];
while ($row = oci_fetch_assoc($stid)) {
    $languages[] = $row;
}
oci_free_statement($stid);
?>
>>>>>>> 161bd45b772712d73f556cea9d38b19688d4845d
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Nyelvvizsga hozzáadása</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
<nav>
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
<<<<<<< HEAD
            <a href="views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/admin/admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
        <?php else: ?>
            <a href="views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/dashboard.php') ? 'active' : '' ?>">Dashboard</a>
        <?php endif; ?>
        <a href="../controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
         <div class="dropdown">
=======
    <a href="./views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
<?php elseif ($_SESSION['user_role'] === 'company'): ?>
    <a href="companydashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Cég Dashboard</a>
    <a href="createad.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'createad.php') ? 'active' : '' ?>">Álláshirdetés létrehozása</a>
    <a href="companyads.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'companyads.php') ? 'active' : '' ?>">Álláshirdetések</a>
    <?php else: ?>
    <a href="../../views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="cvupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Önéletrajz</a>
    <a href="lang_examupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Új nyelvvizsga</a>
<?php endif; ?>

        <a href="../../controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
          <div class="dropdown">
>>>>>>> 161bd45b772712d73f556cea9d38b19688d4845d
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : '' ?>">Bejelentkezés</a>
            <div class="dropdown-content">
                <a href="../login.php?type=user">Bejelentkezés magánszemélyként</a>
                <a href="login.php?type=company">Bejelentkezés cégként</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == './views/register.php') ? 'active' : '' ?>">Regisztráció</a>
            <div class="dropdown-content">
                <a href="../register.php?type=individual">Regisztráció magánszemélyként</a>
                <a href="register.php?type=company">Regisztráció cégként</a>
            </div>
        </div>
    <?php endif; ?>
</nav>
    
        <h1>Új nyelvvizsga hozzáadása</h1>

<<<<<<< HEAD
        <form action="../../controllers/user/addLanguageCertificate.php" method="POST" id="regForm" novalidate>
            <label for="language">Nyelv:</label>
            <input type="text" name="language" id="language" placeholder="pl. angol" required>
=======
        <form action="../../controllers/user/lang_examuploadController.php" method="POST">
            <label for="language">Nyelv:</label>
            <select name="language" id="language" required>
                <option value="">-- Válassz nyelvet --</option>
                <?php foreach ($languages as $lan): ?>
                    <option value="<?= $lan['LAN_ID'] ?>"><?= htmlspecialchars($lan['LAN_NAME']) ?></option>
                <?php endforeach; ?>
            </select>
>>>>>>> 161bd45b772712d73f556cea9d38b19688d4845d


            <select name="level" id="level" required>
    <option value="">-- Válassz szintet --</option>
    <option value="alap">alap</option>
    <option value="közép">közép</option>
    <option value="emelt">emelt</option>
</select>

            <label for="exam_date">Vizsga dátuma:</label>
            <input type="date" name="exam_date" id="exam_date" required>

            <button type="submit">Hozzáadás</button>
        </form>

        <br>
</body>
</html>