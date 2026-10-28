<?php
session_start();
http_response_code(500);
require 'header.php';
?>
<main class="error-page" style="text-align: center;">
    <h1 style="font-size: 120px; color: #e67e22; margin: 0;">500</h1>
    <h2 style="color: #333;">Server Error</h2>
    <p style="color: #666; font-size: 18px;">
        Er is iets fout gegaan op de server.
    </p>
    <p style="color: #999;">
        Probeer het later opnieuw of neem contact op met de beheerder.
    </p>
    <div style="margin-top: 40px;">
        <a href="index.php" class="btn">Terug naar Home</a>
    </div>
</main>
<?php require 'footer.php'; ?>