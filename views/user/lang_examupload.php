<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

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
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Nyelvvizsga hozzáadása</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-left">
        <a href="../../index.php" class="logo">HireMePls</a>
    </div>

    <div class="navbar-center">
        <a href="showJobs.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'showJobs.php') ? 'active' : '' ?>">Állások</a>
        <a href="applications.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'applications.php') ? 'active' : '' ?>">Jelentkezéseim</a>
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
        <h1>Új nyelvvizsga hozzáadása</h1>

        <form action="../../controllers/user/lang_examuploadController.php" method="POST">
            <label for="language">Nyelv:</label>
            <select name="language" id="language" required>
                <option value="">-- Válassz nyelvet --</option>
                <?php foreach ($languages as $lan): ?>
                    <option value="<?= $lan['LAN_ID'] ?>"><?= htmlspecialchars($lan['LAN_NAME']) ?></option>
                <?php endforeach; ?>
            </select>


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