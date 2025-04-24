<?php
session_start();
include('../../config/config.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'company') {
    header('Location: ../../views/login.php');
    exit();
}

function getOptions($conn, $table, $idField, $nameField) {
    $query = "SELECT $idField, $nameField FROM $table";
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    $results = [];
    while ($row = oci_fetch_assoc($stid)) {
        $results[] = $row;
    }
    return $results;
}

$positions = getOptions($conn, 'job_positions', 'job_id', 'job_name');
$schedules = getOptions($conn, 'job_schedule', 'sch_id', 'sch_name');
$qualifications = getOptions($conn, 'qualification', 'qu_id', 'qu_type'); 
$languages = getOptions($conn, 'language', 'lan_id', 'lan_name');
$natures = getOptions($conn, 'job_nature', 'nat_id', 'nat_name');

if (isset($_SESSION['message'])) {
    echo "<div class='success-message'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']); 
}

if (isset($_SESSION['error_message'])) {
    echo "<div class='error-message'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']); 
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../../assets/styles.css">
    <script>
document.addEventListener("DOMContentLoaded", () => {
    const input = document.getElementById("position-input");
    const suggestionsBox = document.getElementById("suggestions");

    input.addEventListener("input", function () {
        const query = this.value;
        if (query.length < 1) {
            suggestionsBox.innerHTML = "";
            return;
        }

        fetch(`../../controllers/company/positionAutocomplete.php?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                suggestionsBox.innerHTML = "";
                data.forEach(item => {
                    const div = document.createElement("div");
                    div.textContent = item;
                    div.addEventListener("click", () => {
                        input.value = item;
                        suggestionsBox.innerHTML = "";
                    });
                    suggestionsBox.appendChild(div);
                });
            });
    });

    document.addEventListener("click", function (e) {
        if (!suggestionsBox.contains(e.target) && e.target !== input) {
            suggestionsBox.innerHTML = "";
        }
    });

    const languageInput = document.getElementById("language-input");
    const languageSuggestions = document.getElementById("language-suggestions");

    languageInput.addEventListener("input", function () {
        const query = this.value;
        if (query.length < 1) {
            languageSuggestions.innerHTML = "";
            return;
        }

        fetch(`../../controllers/company/languageAutocomplete.php?q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                languageSuggestions.innerHTML = "";
                data.forEach(item => {
                    const div = document.createElement("div");
                    div.textContent = item;
                    div.addEventListener("click", () => {
                        languageInput.value = item;
                        languageSuggestions.innerHTML = "";
                    });
                    languageSuggestions.appendChild(div);
                });
            });
    });

    document.addEventListener("click", function (e) {
        if (!languageSuggestions.contains(e.target) && e.target !== languageInput) {
            languageSuggestions.innerHTML = "";
        }
    });
});
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

<h2>Új álláshirdetés</h2>
<form action="../../controllers/company/createAdController.php" method="POST">
<label>Pozíció:
    <input type="text" name="position" id="position-input" autocomplete="off" required>
    <div id="suggestions" class="autocomplete-suggestions"></div>
</label><br>


    <label>Munkarend:
        <select name="schedule">
            <?php foreach ($schedules as $sch): ?>
                <option value="<?= $sch['SCH_ID'] ?>"><?= $sch['SCH_NAME'] ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Képzettség:
        <select name="qualification">
            <?php foreach ($qualifications as $qual): ?>
                <option value="<?= $qual['QU_ID'] ?>"><?= $qual['QU_TYPE'] ?></option>
            <?php endforeach; ?>
        </select>
    </label><br>

    <label>Nyelv:
    <input type="text" name="language" id="language-input" autocomplete="off" required>
    <div id="language-suggestions" class="autocomplete-suggestions"></div>
</label><br>


    <label>Bér (Ft): <input type="number" name="pay" required></label><br>

    <label>Leírás:
        <textarea name="text" rows="4" cols="40" required></textarea>
    </label><br>

    <label>Munkavégzés jellege:</label><br>
    <?php foreach ($natures as $nat): ?>
        <label>
            <input type="checkbox" name="natures[]" value="<?= $nat['NAT_ID'] ?>">
            <?= $nat['NAT_NAME'] ?>
        </label><br>
    <?php endforeach; ?>

    <button type="submit">Hirdetés közzététele</button>
</form>
</body>
</html>
