<?php session_start(); ?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="../assets/styles.css">
</head>
<body>
<nav>
    <a href="../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Ha a felhasználó be van jelentkezve -->
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <!-- Ha admin a felhasználó, akkor az admin dashboardra irányítunk -->
            <a href="views/admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/admin/admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
        <?php else: ?>
            <!-- Ha sima felhasználó a bejelentkezett felhasználó, akkor a sima dashboardra -->
            <a href="views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/dashboard.php') ? 'active' : '' ?>">Dashboard</a>
        <?php endif; ?>
        <a href="../controllers/logout.php" class="logout">Kijelentkezés</a>
    <?php else: ?>
        <!-- Ha a felhasználó nincs bejelentkezve -->
         <!-- 🔽 Bejelentkezés dropdown -->
         <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == 'login.php') ? 'active' : '' ?>">Bejelentkezés</a>
            <div class="dropdown-content">
                <a href="login.php?type=user">Bejelentkezés magánszemélyként</a>
                <a href="company/login.php?type=company">Bejelentkezés cégként</a>
            </div>
        </div>
        <!-- Regisztráció dropdown menü -->
        <div class="dropdown">
            <a href="#" class="dropdown-toggle <?= (basename($_SERVER['PHP_SELF']) == './views/register.php') ? 'active' : '' ?>">Regisztráció</a>
            <div class="dropdown-content">
                <a href="register.php?type=individual">Regisztráció magánszemélyként</a>
                <a href="company/register.php?type=company">Regisztráció cégként</a>
            </div>
        </div>
    <?php endif; ?>
</nav>
    <h1>Bejelentkezés</h1>
    <?php if (isset($_SESSION['error'])): ?>
        <p style="color:red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    <form action="../controllers/loginController.php" method="post">
        <input type="email" name="email" placeholder="Email cím" required><br>
        <input type="password" name="password" placeholder="Jelszó" required><br>
        <button type="submit">Bejelentkezés</button>
    </form>
    <p><a href="../index.php">⬅Vissza a főoldalra</a></p>
</body>
</html>
