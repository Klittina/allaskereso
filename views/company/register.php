<p?php
session_start();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Cég Regisztráció</title>
    <link rel="stylesheet" href="../../assets/styles.css">
    <script src="../../assets/js/formValidation.js"></script>
</head>
<body>
<nav>
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Ha a felhasználó be van jelentkezve -->
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <!-- Ha admin a felhasználó, akkor az admin dashboardra irányítunk -->
            <a href="views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/admin/admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
        <?php else: ?>
            <!-- Ha sima felhasználó a bejelentkezett felhasználó, akkor a sima dashboardra -->
            <a href="views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/dashboard.php') ? 'active' : '' ?>">Dashboard</a>
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
    
        <h1>Cég regisztráció</h1>
        <?php if (isset($_SESSION['error'])): ?>
    <div class="error-message"><?= htmlspecialchars($_SESSION['error']) ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

        <form action="../../controllers/company/registerCompanyController.php" method="POST" id="regForm" novalidate>
            
            <h3>Kapcsolattartó személy</h3>

            <label for="co_firstname">Vezetéknév:</label>
            <input type="text" id="co_firstname" name="co_firstname" placeholder="pl.: Nagy">

            <label for="co_lastname">Keresztnév:</label>
            <input type="text" id="co_lastname" name="co_lastname" placeholder="pl.: Lajos" required>

            <label for="email">Hivatalos E-mail cím:</label>
            <input type="email" id="email" name="email" placeholder="pl.: nagy.lajos@hiremepls.hu" required>
            
            <label for="password">Jelszó:</label>
            <input class="passwd" type="password" id="password" name="password" required>

            <label for="password_confirm">Jelszó megerősítése:</label>
            <input class="passwd" type="password" id="password_confirm" name="password_confirm" required>
            
            <label for="co_phone">Kapcsolattartó telefonszáma:</label>
            <input type="tel" id="co_phone" name="co_phone" placeholder="pl.: +36201234567" required>

            
            <h3>Cég adatok</h3>

            <label for="name">Cég neve:</label>
            <input type="text" id="name" name="name" placeholder="pl.: Hiremepls Kft." required>

            <label for="tax_num">Adószám:</label>
            <input type="text" id="tax_num" name="tax_num" placeholder="pl.: 01234567890" required>

            <label for="country">Ország:</label>
            <input type="text" id="country" name="country" placeholder="pl.: Magyarország" required>

            <label for="city">Város:</label>
            <input type="text" id="city" name="city" placeholder="pl.: Budapest" required>

            <label for="zipcode">Irányítószám:</label>
            <input type="text" id="zipcode" name="zipcode" placeholder="pl.: 6720" required>

            <label for="street">Utca:</label>
            <input type="text" id="street" name="street" placeholder="pl.: Szent János u." required>

            <label for="num">Házszám:</label>
            <input type="text" id="num" name="num" placeholder="pl.: 34/B" required>

            <button type="submit">Regisztráció</button>
        </form>
</body>
</html>
