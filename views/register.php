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
<nav class="navbar">
    <div class="navbar-left">
        <a href="../index.php" class="logo">HireMePls</a>
    </div>

    <div class="navbar-center">
        <a href="user/showJobs.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'showJobs.php') ? 'active' : '' ?>">Állások</a>
        <a href="applications.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'applications.php') ? 'active' : '' ?>">Jelentkezéseim</a>
    </div>

    <div class="navbar-right">
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'user'): ?>
            <div class="dropdown">
                <button class="dropbtn">Profilom ▼</button>
                <div class="dropdown-content">
                    <a href="user/dashboard.php">Profilom</a>
                    <a href="user/cvupload.php">Önéletrajz</a>
                    <a href="user/lang_examupload.php">Új nyelvvizsga</a>
                    <a href="user/newschool.php">Új képzettség</a>
                    <a href="../controllers/logout.php" class="logout">Kijelentkezés</a>
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

    <h1>Regisztráció</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <form action="../controllers/registerController.php" method="post" id="regForm" novalidate>
        <label for="lastname">Vezetéknév:</label>
        <input type="text" name="lastname" required>
        <br>
        <label for="firstname">Keresztnév:</label>
        <input type="text" name="firstname" required>
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
</body>
</html>
