<?php
session_start();
http_response_code(404);
require 'header.php';
?>
<main class="error-page error-404">
    <h1 class="error-code">404</h1>
    <h2>Pagina Niet Gevonden</h2>
    <p>De pagina die je zoekt bestaat niet (meer).</p>
    <p>Mogelijk is de link verouderd of is de pagina verwijderd.</p>
    <div>
        <a href="index.php" class="btn">Terug naar Home</a>
        <a href="tools_index.php" class="btn">Bekijk Tools</a>
    </div>
</main>
<?php require 'footer.php'; ?>