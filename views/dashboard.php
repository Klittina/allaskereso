<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
    header('Location: login.php');
    exit();
}

include('../config/config.php');

// Lekérdezzük a felhasználó adatait
$user_id = $_SESSION['user_id'];

// Felhasználó alapadatok
$user_sql = "SELECT * FROM users WHERE user_id = :id";
$stid = oci_parse($conn, $user_sql);
oci_bind_by_name($stid, ":id", $user_id);
oci_execute($stid);
$user = oci_fetch_assoc($stid);

// Iskolák
$school_sql = "
SELECT s.name, s.country, st.stud_start, st.stud_end, st.stud_grade 
FROM study st
JOIN schools s ON st.stud_where = s.sc_id
WHERE st.stud_who = :id";
$stid_sch = oci_parse($conn, $school_sql);
oci_bind_by_name($stid_sch, ":id", $user_id);
oci_execute($stid_sch);

// Nyelvvizsgák
$lang_sql = "
SELECT l.lan_name, le.ex_level 
FROM language_exam le
JOIN language l ON le.ex_lan = l.lan_id
WHERE le.ex_user = :id";
$stid_lang = oci_parse($conn, $lang_sql);
oci_bind_by_name($stid_lang, ":id", $user_id);
oci_execute($stid_lang);

// Önéletrajzok
$cv_sql = "
SELECT c.cv_path, l.lan_name 
FROM cv c
LEFT JOIN language l ON c.cv_lan = l.lan_id
WHERE c.cv_user = :id";
$stid_cv = oci_parse($conn, $cv_sql);
oci_bind_by_name($stid_cv, ":id", $user_id);
oci_execute($stid_cv);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
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
        <a href="login.php" class="<?= (basename($_SERVER['PHP_SELF']) == './views/login.php') ? 'active' : '' ?>">Bejelentkezés</a>
        
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
    <h1>Felhasználói adatok</h1>
    <p><strong>Név:</strong> <?= htmlspecialchars($user['FIRSTNAME'] . ' ' . $user['LASTNAME']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['EMAIL']) ?></p>
    <p><strong>Telefonszám:</strong> <?= htmlspecialchars($user['PHONE']) ?></p>
    <p><a href="../logout.php">Kijelentkezés</a></p>

    <h2>Iskolai végzettségek</h2>
    <ul>
        <?php while ($row = oci_fetch_assoc($stid_sch)): ?>
            <li><?= $row['NAME'] ?> (<?= $row['COUNTRY'] ?>), <?= $row['STUD_START'] ?> - <?= $row['STUD_END'] ?> (<?= $row['STUD_GRADE'] ?>)</li>
        <?php endwhile; ?>
    </ul>

    <h2>Nyelvvizsgák</h2>
    <ul>
        <?php while ($row = oci_fetch_assoc($stid_lang)): ?>
            <li><?= $row['LAN_NAME'] ?> (<?= $row['EX_LEVEL'] ?>)</li>
        <?php endwhile; ?>
    </ul>

    <h2>Önéletrajzok</h2>
    <ul>
        <?php while ($row = oci_fetch_assoc($stid_cv)): ?>
            <li><a href="<?= $row['CV_PATH'] ?>" target="_blank"><?= $row['LAN_NAME'] ?? 'Nincs nyelv megadva' ?></a></li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
