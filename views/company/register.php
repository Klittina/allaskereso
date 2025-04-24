<?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Cég Regisztráció</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
<nav>
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/admin/admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
        <?php else: ?>
            <a href="views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/dashboard.php') ? 'active' : '' ?>">Dashboard</a>
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
    
        <h1>Cég regisztráció</h1>
        <?php if (isset($_SESSION['error'])): ?>
    <div class="error-message"><?= htmlspecialchars($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

        <form action="../../controllers/company/registerCompanyController.php" method="POST">
            <label for="name">Cég neve:</label>
            <input type="text" id="name" name="name" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Jelszó:</label>
            <input type="password" id="password" name="password" required>

            <label for="password_confirm">Jelszó megerősítése:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>

            <label for="tax_num">Adószám:</label>
            <input type="text" id="tax_num" name="tax_num" required>

            <label for="co_firstname">Kapcsolattartó első neve:</label>
            <input type="text" id="co_firstname" name="co_firstname" required>

            <label for="co_lastname">Kapcsolattartó vezetékneve:</label>
            <input type="text" id="co_lastname" name="co_lastname" required>

            <label for="co_phone">Kapcsolattartó telefonszáma:</label>
            <input type="tel" id="co_phone" name="co_phone" required>

            <label for="country">Ország:</label>
            <input type="text" id="country" name="country" required>

            <label for="city">Város:</label>
            <input type="text" id="city" name="city" required>

            <label for="zipcode">Irányítószám:</label>
            <input type="text" id="zipcode" name="zipcode" required>

            <label for="street">Utca:</label>
            <input type="text" id="street" name="street" required>

            <label for="num">Házszám:</label>
            <input type="text" id="num" name="num" required>

            <button type="submit">Regisztráció</button>
        </form>
</body>
</html>
