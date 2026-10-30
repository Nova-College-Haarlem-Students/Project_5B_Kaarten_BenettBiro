<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: inloggen.php');
    exit;
}

if (isset($_SESSION['rol_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_SESSION['user_id'])) {
    header('Location: dashboard.php?id=' . $_SESSION['user_id']);
    exit;
}

require 'database.php';

$id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute(['id' => $id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>

    <nav>
        <div class="nav-inner">
            <div class="nav-logo">Camping</div>
            <div class="nav-right">
                <span class="timer-label">
                    Welkom, <?php echo $user['name']; ?>
                </span>
                <span class="timer-label">
                    Tijd op pagina: <span id="timer" class="timer">00:00</span>
                </span>
                <a href="guestboard.php" class="btn-blue">gegevens bewerken</a>

                <a href="index.php" class="btn-blue">Home</a>
                <a href="stats_locations.php" class="btn-blue"> location stats</a>
                <a href="logout.php?logout=1" class="btn-red">Uitloggen</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-inner">
            <h1>Mijn Dashboard</h1>
            <p>Beheer hier je persoonlijke gegevens.</p>
        </div>
    </section>

    <main class="main-content">
        <div class="detail-card">
            <div class="detail-content">
                <h2 class="detail-title">Employee Gegevens</h2>
                <p class="detail-type">Hier vind je een overzicht van jouw accountinformatie.</p>

                <div class="detail-info">

                    <div class="detail-box">
                        <strong>Naam</strong>
                        <p><?php echo $user['name']; ?></p>
                    </div>
                    <div class="detail-box">
                        <strong>Email</strong>
                        <p><?php echo $user['email']; ?></p>
                    </div>
                    <div class="detail-box">
                        <strong>user id </strong>
                        <p><?php echo $user['id']; ?></p>
                    </div>

                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-inner">
            <div>
                <h4>Over Ons</h4>
                <p></p>
            </div>
            <div>
                <h4>Snelle Links</h4>
                <ul class="footer-links">
                   
                </ul>
            </div>
            <div></div>
        </div>
    </footer>

    <script>
        let seconden = 0;
        function updateTimer() {
            seconden++;
            const minuten = Math.floor(seconden / 60);
            const restSeconden = seconden % 60;
            document.getElementById('timer').textContent =
                String(minuten).padStart(2, '0') + ':' + String(restSeconden).padStart(2, '0');
        }
        setInterval(updateTimer, 1000);
    </script>

</body>

</html>