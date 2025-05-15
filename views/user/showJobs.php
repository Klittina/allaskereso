<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'user') {
   header('Location: ../login.php');
   exit();
}

include('../../config/config.php');

// ✳️ selected() függvény definiálása
function selected($name, $value) {
    return (isset($_GET[$name]) && $_GET[$name] == $value) ? 'selected' : '';
}

$user_id = $_SESSION['user_id'];

// Felhasználó lekérdezése
$user_sql = "SELECT * FROM users WHERE user_id = :id";
$stid = oci_parse($conn, $user_sql);
oci_bind_by_name($stid, ":id", $user_id);
oci_execute($stid);
$user = oci_fetch_assoc($stid);


// Dummy adatok – ha valódi adatbázisból jönnek, cseréld ki ezekre a lekérdezésekre!
$scheduleOptions = ['Teljes munkaidő', 'Részmunkaidő', 'Gyakornoki'];
$qualificationOptions = ['Érettségi', 'Felsőfokú', 'Szakmunkás'];
$natureOptions = ['Alkalmi', 'Állandó', 'Távmunka'];

// Szűrés eredményei – ez csak példa, cseréld le a saját lekérdezésedre
$data = []; // Például: itt tölthetnéd fel az adatbázis találatokkal

$sql = "SELECT
    ja.ad_id,
    jp.job_name AS positionName,
    js.sch_name AS schedule,
    q.qu_type AS qualification,
    l.lan_name AS languageName,
    ja.ad_pay AS pay,
    ja.ad_text AS text,
    LISTAGG(jn.nat_name, ', ') WITHIN GROUP (ORDER BY jn.nat_name) AS natures
FROM job_advertisement ja
LEFT JOIN job_positions jp ON ja.ad_po = jp.job_id
LEFT JOIN job_schedule js ON ja.ad_sch = js.sch_id
LEFT JOIN qualification q ON ja.ad_qualification = q.qu_id
LEFT JOIN language l ON ja.ad_lan = l.lan_id
LEFT JOIN job_ad_nature jan ON ja.ad_id = jan.job_ad_id
LEFT JOIN job_nature jn ON jan.nat_id = jn.nat_id
WHERE ja.ad_status = 1
GROUP BY
    ja.ad_id,
    jp.job_name,
    js.sch_name,
    q.qu_type,
    l.lan_name,
    ja.ad_pay,
    ja.ad_text
ORDER BY ja.ad_id DESC";

$stmt = oci_parse($conn, $sql);
if (!oci_execute($stmt)) {
    $e = oci_error($stmt);
    die("SQL hiba: " . $e['message']);
}

$data = [];
while ($row = oci_fetch_assoc($stmt)) {
    $data[] = $row;
}

oci_free_statement($stmt);
oci_close($conn);

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
                <td><a href="application.php?app_id=' . $row[ja.ad_id] . '"><?= htmlspecialchars($row['POSITIONNAME']) ?></a></td>
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
