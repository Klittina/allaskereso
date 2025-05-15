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
