<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Nyelvvizsga hozzáadása</title>
    <link rel="stylesheet" href="../../assets/styles.css">
</head>
<body>
    <div class="container">
        <h1>Új nyelvvizsga hozzáadása</h1>

        <form action="../../controllers/user/addLanguageCertificate.php" method="POST">
            <label for="language">Nyelv:</label>
            <input type="text" name="language" id="language" required>

            <label for="level">Szint:</label>
            <input type="text" name="level" id="level" placeholder="pl. B2" required>

            <label for="exam_date">Vizsga dátuma:</label>
            <input type="date" name="exam_date" id="exam_date" required>

            <button type="submit">Hozzáadás</button>
        </form>

        <br>
        <a href="dashboard.php">⬅ Vissza a főoldalra</a>
    </div>
</body>
</html>