<?php
session_start();
require 'database.php';


$stmt = $conn->prepare("SELECT * FROM locations");
$stmt->execute();
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);



require 'header.php';
?>


<main>
    <div class="container">

      

        <?php foreach ($locations as $location): ?>
            <div class="product">
                <img src="<?php echo isset($tlocation['photo_url']) ? 'images/' . htmlspecialchars($location['tool_image']) : 'https://placehold.co/200' ?>"
                    alt="<?php echo htmlspecialchars($location['number']) ?>">
                <h3> location number: <?php echo htmlspecialchars($location['id']) ?></h3>
                <h3> maximum guests: <?php echo htmlspecialchars($location['max_guests']) ?></h3>
                <p> per night € <?php echo number_format($location['price_per_night']) ?></p>
                <a href="Location_detail.php?id=<?php echo urlencode($location['id']) ?>">Bekijk</a>
            </div>

        <?php endforeach; ?>

    </div>

</main>

<?php require 'footer.php' ?>