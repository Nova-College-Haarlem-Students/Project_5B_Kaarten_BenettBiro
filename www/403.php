<?php
session_start();
http_response_code(403);
require 'header.php';
?>
<main class="error-page error-403">
    <h1 class="error-code">403</h1>
    <h2>Toegang Geweigerd</h2>
    <p>Je hebt geen toestemming om deze pagina te bekijken.</p>
    <p>
        <?php if (isset($_SESSION['user_id'])): ?>
            Je bent ingelogd als <strong><?php echo htmlspecialchars($_SESSION['email'] ?? 'gebruiker'); ?></strong>
            <br>maar je hebt niet de juiste rechten voor deze actie.
        <?php else: ?>
            Je bent momenteel niet ingelogd.
        <?php endif; ?>
    </p>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="index.php" class="btn">Terug naar Home</a>
        <?php else: ?>
            <a href="login.php" class="btn">Inloggen</a>
        <?php endif; ?>
    </div>
</main>
<?php require 'footer.php'; ?>