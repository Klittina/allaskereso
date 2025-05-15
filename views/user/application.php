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

oci_execute($stmt);

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

<nav class="navbar">
    <div class="navbar-left">
        <a href="../../index.php" class="logo">HireMePls</a>
    </div>

    <div class="navbar-center">
        <a href="showJobs.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'showJobs.php') ? 'active' : '' ?>">Állások</a>
        <a href="application.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'applications.php') ? 'active' : '' ?>">Jelentkezéseim</a>
    </div>

    <div class="navbar-right">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'user'): ?>
            <div class="dropdown">
                <button class="dropbtn">Profilom ▼</button>
                <div class="dropdown-content">
                    <a href="../../views/dashboard.php">Profilom</a>
                    <a href="cvupload.php">Önéletrajz</a>
                    <a href="lang_examupload.php">Új nyelvvizsga</a>
                    <a href="newschool.php">Új képzettség</a>
                    <a href="../../controllers/logout.php" class="logout">Kijelentkezés</a>
                </div>
            </div>
        <?php else: ?>
            <!-- Bejelentkezés/Regisztráció ha nincs bejelentkezve -->
            <div class="dropdown">
                <button class="dropbtn">Bejelentkezés</button>
                <div class="dropdown-content">
                    <a href="login.php?type=user">Magánszemély</a>
                    <a href="company/login.php?type=company">Cég</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Regisztráció</button>
                <div class="dropdown-content">
                    <a href="register.php?type=individual">Magánszemély</a>
                    <a href="company/register.php?type=company">Cég</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
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
