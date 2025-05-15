<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

include('../../config/config.php');

// Iskolák (csak valid)
$sql_schools = "SELECT sc_id, name, istheregrade FROM schools WHERE sc_name_valid = 1 ORDER BY name";
$stid_schools = oci_parse($conn, $sql_schools);
oci_execute($stid_schools);

$schools = [];
while ($row = oci_fetch_assoc($stid_schools)) {
    $schools[] = $row;
}
oci_free_statement($stid_schools);

// Végzettség típusok
$sql_qualifications = "SELECT qu_id, qu_type FROM qualification ORDER BY qu_type";
$stid_qual = oci_parse($conn, $sql_qualifications);
oci_execute($stid_qual);

$qualifications = [];
while ($row = oci_fetch_assoc($stid_qual)) {
    $qualifications[] = $row;
}
oci_free_statement($stid_qual);

// Végzettség nevek (csak valid)
$sql_qualnames = "SELECT quna_id, quna_name FROM qualification_name WHERE quna_name_valid = 1 ORDER BY quna_name";
$stid_qualnames = oci_parse($conn, $sql_qualnames);
oci_execute($stid_qualnames);

$qualnames = [];
while ($row = oci_fetch_assoc($stid_qualnames)) {
    $qualnames[] = $row;
}
oci_free_statement($stid_qualnames);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Iskolai végzettség hozzáadása</title>
    <link rel="stylesheet" href="../../assets/styles.css">
    <script>
        function toggleGrade() {
            const schoolSelect = document.getElementById('school');
            const selectedOption = schoolSelect.options[schoolSelect.selectedIndex];
            const istheregrade = selectedOption.getAttribute('data-istheregrade');
            const gradeContainer = document.getElementById('grade-container');
            if (istheregrade === '1') {
                gradeContainer.style.display = 'block';
                document.getElementById('grade').setAttribute('required', 'required');
            } else {
                gradeContainer.style.display = 'none';
                document.getElementById('grade').removeAttribute('required');
                document.getElementById('grade').value = "";
            }
        }

        window.onload = function() {
            toggleGrade();
        }
    </script>
</head>
<body>
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
            <a href="../../views/dashboard.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Dashboard</a>
            <a href="cvupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Önéletrajz</a>
            <a href="lang_examupload.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Új nyelvvizsga</a>
            <a href="newschool.php" class="<?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : '' ?>">Új képzettség</a>
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

<h1>Iskolai végzettség hozzáadása</h1>

<form action="../../controllers/user/studyuploadController.php" method="POST" novalidate>
    <label for="school">Iskola:</label>
    <select name="school" id="school" onchange="toggleGrade()" required>
        <option value="">-- Válassz iskolát --</option>
        <?php foreach ($schools as $school): ?>
            <option value="<?= $school['SC_ID'] ?>" data-istheregrade="<?= $school['ISTHEREGRADE'] ?>">
                <?= htmlspecialchars($school['NAME']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="qualification_type">Végzettség típusa:</label>
    <select name="qualification_type" id="qualification_type" required>
        <option value="">-- Válassz végzettség típust --</option>
        <?php foreach ($qualifications as $q): ?>
            <option value="<?= $q['QU_ID'] ?>"><?= htmlspecialchars($q['QU_TYPE']) ?></option>
        <?php endforeach; ?>
    </select>

    <label for="qualification_name">Végzettség neve:</label>
    <select name="qualification_name" id="qualification_name" required>
        <option value="">-- Válassz végzettség nevet --</option>
        <?php foreach ($qualnames as $qn): ?>
            <option value="<?= $qn['QUNA_ID'] ?>"><?= htmlspecialchars($qn['QUNA_NAME']) ?></option>
        <?php endforeach; ?>
    </select>

    <div id="grade-container" style="display:none;">
        <label for="grade">Értékelés (jegy):</label>
        <select name="grade" id="grade">
            <option value="">-- Válassz jegyet --</option>
            <option value="jeles">jeles</option>
            <option value="jó">jó</option>
            <option value="közepes">közepes</option>
            <option value="elégséges">elégséges</option>
            <option value="elégtelen">elégtelen</option>
        </select>
    </div>

    <label for="start_date">Kezdés dátuma:</label>
    <input type="date" name="start_date" id="start_date" required>

    <label for="end_date">Befejezés dátuma:</label>
    <input type="date" name="end_date" id="end_date" required>

    <button type="submit">Hozzáadás</button>
</form>
</body>
</html>
