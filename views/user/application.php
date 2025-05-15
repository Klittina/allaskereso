<?php
session_start();
include('../../config/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: login.php');
    exit();
}

if (!isset($conn) || !$conn) {
    echo "<script>alert('Nem sikerült csatlakozni az adatbázishoz!'); window.location.href='showJobs.php';</script>";
    exit();
}

if (isset($_GET['app_id'])) {
    $_SESSION['selected_job_id'] = (int) $_GET['app_id'];
}

$userId = $_SESSION['user_id'];

$sql = "SELECT id, title FROM cv WHERE user_id = :userId";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ':userId', $userId);

$cvList = [];
while ($row = oci_fetch_assoc($stmt)) {
    $cvList[] = $row;
}

oci_free_statement($stmt);
oci_close($conn);
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/styles.css">
    <title>Applikáció munkára</title>
</head>
<body>

<nav>
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <a href="./views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
<?php elseif ($_SESSION['user_role'] === 'company'): ?>
    <a href="companydashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Cég Dashboard</a>
    <a href="createad.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'createad.php') ? 'active' : '' ?>">Álláshirdetés létrehozása</a>
    <a href="companyads.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'companyads.php') ? 'active' : '' ?>">Álláshirdetések</a>
    <?php else: ?>
    <a href="../../views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="cvupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Önéletrajz</a>
    <a href="lang_examupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Új nyelvvizsga</a>
    <a href="newschool.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Új képzettség</a>
    <a href="showJobs.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Álláshirdetés</a>
<?php endif; ?>

        <a href="../../controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
          <div class="dropdown">
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

<h2>Jelentkezés megerősítése</h2>
<form method="POST" action="../../controllers/user/applicationController">
    <label for="app_cv">Válaszd ki az önéletrajzod:</label>
    <select name="app_cv" id="app_cv" required>
        <option value="">-- Válassz --</option>
        <?php foreach ($cvList as $cv): ?>
            <option value="<?= htmlspecialchars($cv['ID']) ?>">
                <?= htmlspecialchars($cv['TITLE']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br>

    <button type="submit" name="confirm">✅ Jóváhagyás</button>
    <button type="button" onclick="window.location.href='showJobs.php'">❌ Mégsem</button>
</form>

</body>
</html>
