<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../login.php');
    exit();
}

include('../../config/config.php');

// Cég adatainak lekérdezése
$company_id = $_SESSION['user_id'];
$sql = "SELECT * FROM company WHERE co_id = :co_id";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":co_id", $company_id);
oci_execute($stid);
$company = oci_fetch_assoc($stid);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Cég Dashboard</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
    <h1>Üdvözöljük, <?= htmlspecialchars($company['NAME']) ?>!</h1>
    
    <nav>
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Ha a felhasználó be van jelentkezve -->
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <a href="./views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
<?php elseif ($_SESSION['user_role'] === 'company'): ?>
    <a href="./views/company/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Cég Dashboard</a>
<?php else: ?>
    <a href="./views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
<?php endif; ?>

        <a href="../../controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
        <!-- Ha a felhasználó nincs bejelentkezve -->
          <!-- 🔽 Bejelentkezés dropdown -->
          <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : '' ?>">Bejelentkezés</a>
            <div class="dropdown-content">
                <a href="../login.php?type=user">Bejelentkezés magánszemélyként</a>
                <a href="login.php?type=company">Bejelentkezés cégként</a>
            </div>
        </div>
        <!-- Regisztráció dropdown menü -->
        <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == './views/register.php') ? 'active' : '' ?>">Regisztráció</a>
            <div class="dropdown-content">
                <a href="../register.php?type=individual">Regisztráció magánszemélyként</a>
                <a href="register.php?type=company">Regisztráció cégként</a>
            </div>
        </div>
    <?php endif; ?>
</nav>

    <h2>Adatok módosítása</h2>
    <form action="../../controllers/company/updateProfile.php" method="post">
        <label>Név: <input type="text" name="name" value="<?= htmlspecialchars($company['NAME']) ?>"></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($company['EMAIL']) ?>"></label><br>
        <label>Adószám: <input type="text" name="tax_num" value="<?= $company['TAX_NUM'] ?>"></label><br>
        <label>Kapcsolattartó: <input type="text" name="co_firstname" value="<?= htmlspecialchars($company['CO_FIRSTNAME']) ?>"> 
        <input type="text" name="co_lastname" value="<?= htmlspecialchars($company['CO_LASTNAME']) ?>"></label><br>
        <label>Telefon: <input type="text" name="co_phone" value="<?= $company['CO_PHONE'] ?>"></label><br>
        <label>Város: <input type="text" name="city" value="<?= htmlspecialchars($company['CITY']) ?>"></label><br>
        <button type="submit">Mentés</button>
    </form>

    <h2>Profil törlése</h2>
    <form action="../../controllers/company/deleteProfile.php" method="post" onsubmit="return confirm('Biztosan törölni szeretné a profilját?');">
        <button type="submit" class="btn-danger">Profil törlése</button>
    </form>
</body>
</html>
