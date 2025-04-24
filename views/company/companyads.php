<?php
session_start();
include('../../config/config.php');

// Csak cég férhet hozzá
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../login.php');
    exit();
}

// Törlés
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delId = $_POST['delete_id'];
    $query = "DELETE FROM job_advertisement WHERE ad_id = :id AND ad_co = :co";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":id", $delId);
    oci_bind_by_name($stid, ":co", $_SESSION['user_id']);
    oci_execute($stid);
    header("Location: companyads.php");
    exit();
}

// Frissítés
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $query = "UPDATE job_advertisement 
              SET ad_pay = :pay, ad_text = :text, ad_status = :status 
              WHERE ad_id = :id AND ad_co = :co";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":pay", $_POST['pay']);
    oci_bind_by_name($stid, ":text", $_POST['text']);
    $status = isset($_POST['status']) ? 1 : 0;
    oci_bind_by_name($stid, ":status", $status);
    oci_bind_by_name($stid, ":id", $_POST['update_id']);
    oci_bind_by_name($stid, ":co", $_SESSION['user_id']);
    oci_execute($stid);
    header("Location: companyads.php");
    exit();
}

// Jelentkezés státusz módosítása
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_app_id'], $_POST['status'])) {
    $query = "UPDATE application SET app_stat = :status WHERE app_id = :id";
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ":status", $_POST['status']);
    oci_bind_by_name($stid, ":id", $_POST['change_app_id']);
    oci_execute($stid);
    header("Location: companyads.php");
    exit();
}

// Hirdetések lekérése
$query = "SELECT ad.*, jp.job_name 
          FROM job_advertisement ad
          JOIN job_positions jp ON ad.ad_po = jp.job_id
          WHERE ad.ad_co = :co";
$stid = oci_parse($conn, $query);
oci_bind_by_name($stid, ":co", $_SESSION['user_id']);
oci_execute($stid);

$ads = [];
while ($row = oci_fetch_assoc($stid)) {
    $ads[] = $row;
}

// Jelentkezők lekérése
$applications = [];
$adIds = array_column($ads, 'AD_ID');
if (!empty($adIds)) {
    $inClause = implode(',', array_map('intval', $adIds));
    $queryApps = "SELECT a.app_id, a.app_ad, a.app_date, a.app_stat, u.firstname, u.lastname, cv.cv_file 
                  FROM application a
                  JOIN cv ON a.app_cv = cv.cv_id
                  JOIN users u ON cv.cv_user = u.user_id
                  WHERE a.app_ad IN ($inClause)";
    $stid = oci_parse($conn, $queryApps);
    oci_execute($stid);
    while ($row = oci_fetch_assoc($stid)) {
        $applications[$row['APP_AD']][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Saját hirdetések</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>

<nav>
    <a href="../../index.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : '' ?>">Kezdőlap</a>

    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Ha a felhasználó be van jelentkezve -->
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

<h2>Saját hirdetések</h2>

<script>
function editRow(rowId) {
    document.querySelectorAll(`#row-${rowId} input, #row-${rowId} textarea`).forEach(el => el.disabled = false);
    document.getElementById(`edit-${rowId}`).style.display = 'none';
    document.getElementById(`save-${rowId}`).style.display = 'inline';
    document.getElementById(`cancel-${rowId}`).style.display = 'inline';
}
function cancelEdit(rowId) {
    window.location.reload();
}
function confirmDelete(formId) {
    if (confirm("Biztosan törölni szeretnéd ezt a hirdetést?")) {
        document.getElementById(formId).submit();
    }
}
function toggleApplicants(adId) {
    const el = document.getElementById('applicants-' + adId);
    el.style.display = el.style.display === 'none' ? 'block' : 'none';
}
</script>

<?php foreach ($ads as $ad): ?>
    <form method="POST" id="form-<?= $ad['AD_ID'] ?>">
        <div id="row-<?= $ad['AD_ID'] ?>" style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <input type="hidden" name="update_id" value="<?= $ad['AD_ID'] ?>">
            <strong>Pozíció:</strong> <?= htmlspecialchars($ad['JOB_NAME']) ?><br>
            <label>Bér (Ft): <input type="number" name="pay" value="<?= $ad['AD_PAY'] ?>" disabled></label><br>
            <label>Leírás: <textarea name="text" disabled><?= htmlspecialchars($ad['AD_TEXT']) ?></textarea></label><br>
            <label>Aktív: <input type="checkbox" name="status" <?= $ad['AD_STATUS'] == 1 ? 'checked' : '' ?> disabled></label><br>

            <button type="button" id="edit-<?= $ad['AD_ID'] ?>" onclick="editRow(<?= $ad['AD_ID'] ?>)">Módosítás</button>
            <button type="submit" id="save-<?= $ad['AD_ID'] ?>" style="display:none;">Mentés</button>
            <button type="button" id="cancel-<?= $ad['AD_ID'] ?>" onclick="cancelEdit(<?= $ad['AD_ID'] ?>)" style="display:none;">Mégse</button>
        </div>
    </form>

    <form method="POST" id="delete-<?= $ad['AD_ID'] ?>">
        <input type="hidden" name="delete_id" value="<?= $ad['AD_ID'] ?>">
        <button type="button" onclick="confirmDelete('delete-<?= $ad['AD_ID'] ?>')">Törlés</button>
    </form>

    <?php
        $adId = $ad['AD_ID'];
        $adApplicants = $applications[$adId] ?? [];
        $count = count($adApplicants);
    ?>
    <p><strong>Jelentkezők száma:</strong> <?= $count ?></p>

    <?php if ($count > 0): ?>
        <button type="button" onclick="toggleApplicants(<?= $adId ?>)">+</button>
        <div id="applicants-<?= $adId ?>" style="display:none; margin-top:10px; border:1px solid #999; padding:10px;">
            <?php foreach ($adApplicants as $app): ?>
                <div style="margin-bottom: 10px;">
                    <p><strong>Név:</strong> <?= htmlspecialchars($app['FIRSTNAME'] . ' ' . $app['LASTNAME']) ?></p>
                    <p><strong>Önéletrajz:</strong> <a href="../../uploads/<?= $app['CV_FILE'] ?>" target="_blank">Megtekintés</a></p>
                    <p><strong>Jelentkezés dátuma:</strong> <?= $app['APP_DATE'] ?></p>
                    <p><strong>Státusz:</strong> <?= $app['APP_STAT'] ?></p>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="change_app_id" value="<?= $app['APP_ID'] ?>">
                        <input type="hidden" name="status" value="Elfogadva">
                        <button type="submit">Elfogadás</button>
                    </form>

                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="change_app_id" value="<?= $app['APP_ID'] ?>">
                        <input type="hidden" name="status" value="Elutasítva">
                        <button type="submit">Elutasítás</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Még nem jelentkezett senki.</p>
    <?php endif; ?>
<?php endforeach; ?>

</body>
</html>
