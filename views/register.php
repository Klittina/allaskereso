<?php session_start(); ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Regisztráció</title>
    <link rel="stylesheet" href="../assets/styles.css">
    <script src="../assets/js/formValidation.js"></script>
</head>
<body>
<nav>
    <a href="../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/admin/admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
        <?php else: ?>
            <a href="views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/dashboard.php') ? 'active' : '' ?>">Dashboard</a>
        <?php endif; ?>
        <a href="../controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
         <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : '' ?>">Bejelentkezés</a>
            <div class="dropdown-content">
                <a href="login.php?type=user">Bejelentkezés magánszemélyként</a>
                <a href="company/login.php?type=company">Bejelentkezés cégként</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == './views/register.php') ? 'active' : '' ?>">Regisztráció</a>
            <div class="dropdown-content">
                <a href="register.php?type=individual">Regisztráció magánszemélyként</a>
                <a href="company/register.php?type=company">Regisztráció cégként</a>
            </div>
        </div>
    <?php endif; ?>
</nav>
    <h1>Regisztráció</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <form action="../controllers/registerController.php" method="post" id="regForm" novalidate>
        <label for="firstname">Keresztnév:</label>
        <input type="text" name="firstname" required>
        <br>
        <label for="lastname">Vezetéknév:</label>
        <input type="text" name="lastname" required>
        <br>
        <label for="email">E-mail cím:</label>
        <input type="email" name="email" required>
        <br>
        <label for="password">Jelszó:</label>
        <input type="password" name="password" placeholder="" required>
        <br>
        <label for="password_confirm">Jelszó újra:</label>
        <input type="password" name="password_confirm" required>
        <br>
        <label for="phone">Telefonszám (11 számjegy):</label>
        <input type="text" name="phone" required pattern="\d{11}">
        <br>
        <label for="birth_date">Születési idő:</label>
        <input type="date" name="birth_date" required>
        <br>
        <button type="submit">Regisztráció</button>
    </form>
    <p><a href="../index.php">Vissza a főoldalra</a></p>
</body>
</html>
