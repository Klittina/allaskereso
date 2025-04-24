<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: ../login.php');
    exit();
}

include('../../config/config.php');

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM user WHERE user_id = :user_id";
$stid = oci_parse($conn, $sql);
oci_bind_by_name($stid, ":user_id", $user_id);
oci_execute($stid);
$user = oci_fetch_assoc($stid);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adatok szerkesztése</title>
    <link rel="stylesheet" href="../../assets/styles.css">
    <script>
        function toggleEdit() {
            const form = document.getElementById('userForm');
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
<h1>Üdvözöljük, <?= htmlspecialchars($user['NAME']) ?>!</h1>

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
    <a href="./views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
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

<h1>Adatok szerkesztése</h1>

<form id="userForm" action="../../controllers/user/modifyController.php" method="post">
        <label>Keresztnév: <input type="text" name="first_name" value="<?= htmlspecialchars($user['firstname']) ?>" disabled></label><br>
        <label>Vezetéknév: <input type="text" name="last_name" value="<?= htmlspecialchars($user['lastname']) ?>" disabled></label><br>
        <label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" disabled></label><br>
        <label>Jelszó: <input type="password" name="password" value="" disabled></label><br>
        <label>Telefonszám: <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" disabled></label><br>
        <label>Születési dátum: <input type="date" name="birth_date" value="<?= htmlspecialchars($user['birth_date']) ?>" disabled></label><br>

        <button type="button" id="editBtn" onclick="toggleEdit()">Szerkesztés</button>
        <button type="submit" id="saveBtn" style="display:none;">Mentés</button>
        <button type="button" id="cancelBtn" onclick="cancelEdit()" style="display:none;">Mégse</button>
    </form>

    <h2>Profil törlése</h2>
    <form action="../../controllers/user/deleteProfile.php" method="post" onsubmit="return confirm('Biztosan törölni szeretné a profilját?');">
        <button type="submit" class="btn-danger">Profil törlése</button>
    </form>    

</body>
</html>