<?php
require 'data_dashboard.php';


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
    <title>Mijn Dashboard</title>
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
                <a href="index.php" class="btn-blue">Home</a>
                 <a href="create_location.php" class="btn-blue"> new location</a>
                <a href="logout.php?logout=1" class="btn-red">Uitloggen</a>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-inner">
            <h1>Locations-stats</h1>
            
        </div>
    </section>

    <main class="main-content">
        <div class="detail-card">
            <div class="detail-content">
                <h2 class="detail-title">information- locations</h2>
                <p class="detail-type">information about locations are found beneath this text.</p>

                <div class="table-card">
                    <h2>Gebruikers</h2>

                    <div class="table-scroll">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Location type id</th>
                                    <th>number</th>
                                    <th>name</th>
                                    <th>size m2</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fetch_locations as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id']) ?></td>
                                        <td><?php echo htmlspecialchars($row['location_type_id']) ?></td>
                                        <td><?php echo htmlspecialchars($row['number']) ?></td>
                                        <td><?php echo htmlspecialchars($row['name'] ?? '-') ?></td>
                                        <td><?php echo htmlspecialchars($row['size_m2']) ?></td>
                                        <td>
                                            <a href="Update_location.php?id=<?php echo (int) $row['id'] ?>">Bewerken</a>

                                            
                                                <form method="post" action="deleted_locations_process.php" style="display:inline"
                                                    onsubmit="return confirm('Deze location verwijderen?')">
                                                    <input type="hidden" name="id" value="<?php echo (int) $row['id'] ?>">
                                                    <button type="submit" name="delete">Verwijderen</button>
                                                </form>
                                           
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
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