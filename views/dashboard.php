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
    <a href="admin/admindashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'admindashboard.php') ? 'active' : '' ?>">Admin Dashboard</a>
<?php elseif ($_SESSION['user_role'] === 'company'): ?>
    <a href="companydashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Cég Dashboard</a>
    <a href="createad.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'createad.php') ? 'active' : '' ?>">Álláshirdetés létrehozása</a>
    <a href="companyads.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'companyads.php') ? 'active' : '' ?>">Álláshirdetések</a>
    <?php else: ?>
    <a href="dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="user/cvupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Önéletrajz</a>
<?php endif; ?>

        <a href="../controllers/logout.php" class="logout">Kijelentkezés</a>
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
<div style="display: flex; flex-wrap: wrap; gap: 1rem;">
    <?php while ($row = oci_fetch_assoc($stid_cv)): ?>
        <div style="border: 1px solid #ccc; padding: 1rem; border-radius: 8px; width: 250px; box-shadow: 2px 2px 6px #ddd;">
            <p><strong>Nyelv:</strong> <?= $row['LAN_NAME'] ?? 'Nincs megadva' ?></p>
            <a href="<?= htmlspecialchars($row['CV_PATH']) ?>" target="_blank" style="color: blue; text-decoration: underline;">Megnyitás</a>
            <form action="../controllers/user/delete_cv.php" method="POST" style="margin-top: 0.5rem;">
                <input type="hidden" name="cv_path" value="<?= htmlspecialchars($row['CV_PATH']) ?>">
                <button type="submit" style="background-color: red; color: white; border: none; padding: 0.5rem; border-radius: 4px; cursor: pointer;">Törlés</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
