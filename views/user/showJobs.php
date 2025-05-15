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
    <title>Álláshirdetések megtekintése</title>
    <link rel="stylesheet" href="../../assets/styles.css">
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

<body>
    <h1>Pozíciók</h1>

    <form method="GET">
        <label for="positionName">Pozíció neve:</label>
        <input type="text" name="positionName" id="positionName" value="<?= htmlspecialchars($_GET['positionName'] ?? '') ?>"><br><br>

        <label for="schedule">Munkarend:</label>
        <select name="schedule" id="schedule">
            <option value="">-- Mind --</option>
            <?php foreach ($scheduleOptions as $option): ?>
                <option value="<?= htmlspecialchars($option) ?>" <?= selected('schedule', $option) ?>><?= htmlspecialchars($option) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="qualification">Képesítés:</label>
        <select name="qualification" id="qualification">
            <option value="">-- Mind --</option>
            <?php foreach ($qualificationOptions as $option): ?>
                <option value="<?= htmlspecialchars($option) ?>" <?= selected('qualification', $option) ?>><?= htmlspecialchars($option) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="languageName">Nyelv:</label>
        <input type="text" name="languageName" id="languageName" value="<?= htmlspecialchars($_GET['languageName'] ?? '') ?>"><br><br>

        <label for="pay">Fizetés:</label>
        <input type="text" name="pay" id="pay" value="<?= htmlspecialchars($_GET['pay'] ?? '') ?>"><br><br>

        <label for="text">Leírás:</label>
        <input type="text" name="text" id="text" value="<?= htmlspecialchars($_GET['text'] ?? '') ?>"><br><br>

        <label for="natures">Jelleg:</label>
        <select name="natures" id="natures">
            <option value="">-- Mind --</option>
            <?php foreach ($natureOptions as $option): ?>
                <option value="<?= htmlspecialchars($option) ?>" <?= selected('natures', $option) ?>><?= htmlspecialchars($option) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <input type="submit" value="Szűrés">
    </form>

    <h2>Találatok</h2>
    <table>
        <thead>
            <tr>
                <th>Pozíció neve</th>
                <th>Munkarend</th>
                <th>Képesítés</th>
                <th>Nyelv</th>
                <th>Fizetés</th>
                <th>Leírás</th>
                <th>Jelleg</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($data)): ?>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['POSITIONNAME']) ?></td>
                    <td><?= htmlspecialchars($row['SCHEDULE']) ?></td>
                    <td><?= htmlspecialchars($row['QUALIFICATION']) ?></td>
                    <td><?= htmlspecialchars($row['LANGUAGENAME']) ?></td>
                    <td><?= htmlspecialchars($row['PAY']) ?></td>
                    <td><?= htmlspecialchars($row['TEXT']) ?></td>
                    <td><?= htmlspecialchars($row['NATURES']) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Nincs találat.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>