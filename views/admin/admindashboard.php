<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

include('../../config/config.php');

$sql = "SELECT * FROM company ORDER BY accepted ASC, co_id DESC";
$stid = oci_parse($conn, $sql);

if (!$stid) {
    $e = oci_error($conn);
    die("❌ SQL előkészítési hiba: " . $e['message']);
}

if (!oci_execute($stid)) {
    $e = oci_error($stid);
    die("❌ Lekérdezési hiba: " . $e['message']);
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
<?php if (isset($_SESSION['success'])): ?>
    <div class="success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

    <h1>Admin Dashboard</h1>
    <nav>
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
    <a href="admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
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

<h2>Regisztrált Cégek</h2>

    <table>
        <thead>
            <tr>
                <th>Név</th>
                <th>Email</th>
                <th>Adószám</th>
                <th>Kapcsolattartó</th>
                <th>Telefon</th>
                <th>Város</th>
                <th>Státusz</th>
                <th>Művelet</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = oci_fetch_assoc($stid)): ?>
            <tr>
                <td><?= $row['NAME'] ?></td>
                <td><?= $row['EMAIL'] ?></td>
                <td><?= $row['TAX_NUM'] ?></td>
                <td><?= $row['CO_FIRSTNAME'] . ' ' . $row['CO_LASTNAME'] ?></td>
                <td><?= $row['CO_PHONE'] ?></td>
                <td><?= $row['CITY'] ?></td>
                <td><?= $row['ACCEPTED'] == 1 ? 'Elfogadva' : 'Függőben' ?></td>
                <td>
                    <?php if ($row['ACCEPTED'] == 0): ?>
                        <form action="../../controllers/admin/acceptCompany.php" method="POST">
                            <input type="hidden" name="co_id" value="<?= $row['CO_ID'] ?>">
                            <button class="btn btn-accept" type="submit">Elfogad</button>
                        </form>
                    <?php else: ?>
                        ✔
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Új Admin Felvétele</h2>
    <form action="../../controllers/admin/adminRegisterController.php" method="post">
    <label for="firstname">Keresztnév:</label>
        <input type="text" id="firstname" name="firstname" required><br><br>

        <label for="lastname">Vezetéknév:</label>
        <input type="text" id="lastname" name="lastname" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Jelszó:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="password_confirm">Jelszó megerősítése:</label>
        <input type="password" id="password_confirm" name="password_confirm" required><br><br>

        <label for="phone">Telefonszám:</label>
        <input type="text" id="phone" name="phone" required><br><br>

        <label for="birth_date">Születési dátum:</label>
        <input type="date" id="birth_date" name="birth_date" required><br><br>

        <button type="submit">Regisztrálás</button>
    </form>
</body>
</html>
