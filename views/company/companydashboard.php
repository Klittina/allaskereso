<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../login.php');
    exit();
}

include('../../config/config.php');

// Adatok lekérése
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
    <script>
        function toggleEdit() {
            const form = document.getElementById('companyForm');
            const inputs = form.querySelectorAll('input');
            const saveBtn = document.getElementById('saveBtn');
            const cancelBtn = document.getElementById('cancelBtn');
            const editBtn = document.getElementById('editBtn');

            inputs.forEach(input => input.disabled = !input.disabled);
            saveBtn.style.display = saveBtn.style.display === 'none' ? 'inline' : 'none';
            cancelBtn.style.display = cancelBtn.style.display === 'none' ? 'inline' : 'none';
            editBtn.style.display = editBtn.style.display === 'none' ? 'inline' : 'none';
        }

        function cancelEdit() {
            window.location.reload();
        }
    </script>
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
    <a href="companydashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Cég Dashboard</a>
    <a href="createad.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'createad.php') ? 'active' : '' ?>">Álláshirdetés létrehozása</a>
    <a href="companyads.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'companyads.php') ? 'active' : '' ?>">Álláshirdetések</a>
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

    <h2>Cégprofil</h2>

    <form id="companyForm" action="../../controllers/company/updateProfile.php" method="post">
        <label>Név: <input type="text" name="name" value="<?= htmlspecialchars($company['NAME']) ?>" disabled></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($company['EMAIL']) ?>" disabled></label><br>
        <label>Adószám: <input type="text" name="tax_num" value="<?= $company['TAX_NUM'] ?>" disabled></label><br>
        <label>Kapcsolattartó:
            <input type="text" name="co_firstname" value="<?= htmlspecialchars($company['CO_FIRSTNAME']) ?>" disabled>
            <input type="text" name="co_lastname" value="<?= htmlspecialchars($company['CO_LASTNAME']) ?>" disabled>
        </label><br>
        <label>Telefon: <input type="text" name="co_phone" value="<?= $company['CO_PHONE'] ?>" disabled></label><br>
        <label>Város: <input type="text" name="city" value="<?= htmlspecialchars($company['CITY']) ?>" disabled></label><br><br>

        <button type="button" id="editBtn" onclick="toggleEdit()">Szerkesztés</button>
        <button type="submit" id="saveBtn" style="display:none;">Mentés</button>
        <button type="button" id="cancelBtn" onclick="cancelEdit()" style="display:none;">Mégse</button>
    </form>

    <h2>Profil törlése</h2>
    <form action="../../controllers/company/deleteProfile.php" method="post" onsubmit="return confirm('Biztosan törölni szeretné a profilját?');">
        <button type="submit" class="btn-danger">Profil törlése</button>
    </form>
</body>
</html>
