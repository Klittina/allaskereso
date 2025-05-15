<?php
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
                    <a href="../login.php">Magánszemély</a>
                    <a href="login.php">Cég</a>
                </div>
            </div>

            <div class="dropdown">
                <button class="dropbtn">Regisztráció</button>
                <div class="dropdown-content">
                    <a href="../register.ph">Magánszemély</a>
                    <a href="register.php">Cég</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
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
