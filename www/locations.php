<?php

require 'database.php';


$type = $_POST['type'] ?? '';
$electricity = $_POST['electricity'] ?? '';
$sort = $_POST['sort'] ?? '';
$zoekterm = $_POST['zoekterm'] ?? '';


$sql = "SELECT * FROM locations WHERE is_active = 1";
$params = [];

if ($type !== '') {
    $sql .= " AND location_type_id = :type";
    $params['type'] = $type;
}

if ($electricity !== '') {
    $sql .= " AND has_electricity = :electricity";
    $params['electricity'] = $electricity;
}

if ($zoekterm !== '') {
    $sql .= " AND (name LIKE :zoekterm1 OR number LIKE :zoekterm2)";
    $params['zoekterm1'] = "%$zoekterm%";
    $params['zoekterm2'] = "%$zoekterm%";
}

if ($sort === 'price_desc') {
    $sql .= " ORDER BY price_per_night DESC";
} elseif ($sort === 'size_asc') {
    $sql .= " ORDER BY size_m2 ASC";
} elseif ($sort === 'size_desc') {
    $sql .= " ORDER BY size_m2 DESC";
} else {
    $sql .= " ORDER BY price_per_night ASC";
}

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$locations = $stmt->fetchAll(PDO::FETCH_ASSOC);


$stmt = $conn->prepare("SELECT id, name FROM location_types");
$stmt->execute();
$location_types = $stmt->fetchAll(PDO::FETCH_ASSOC);

require 'header.php';
?>

<main>
    <form method="post" action="locations.php" class="filter-form">
        <label>
            Zoeken
            <input type="text" name="zoekterm" value="<?php echo htmlspecialchars($zoekterm) ?>"
                placeholder="Naam of nummer">
        </label>

        <label>
            Soort plek
            <select name="type">
                <option value="">Alle soorten</option>
                <?php foreach ($location_types as $location_type): ?>
                    <option value="<?php echo $location_type['id'] ?>" <?php if ($type == $location_type['id'] && $type !== '')
                           echo 'selected' ?>>
                        <?php echo htmlspecialchars($location_type['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Stroom
            <select name="electricity">
                <option value="">Maakt niet uit</option>
                <option value="1" <?php if ($electricity === '1')
                    echo 'selected' ?>>Met stroom</option>
                    <option value="0" <?php if ($electricity === '0')
                    echo 'selected' ?>>Zonder stroom</option>
                </select>
            </label>

            <label>
                Sorteren op
                <select name="sort">
                    <option value="price_asc" <?php if ($sort === 'price_asc')
                    echo 'selected' ?>>Prijs (laag - hoog)</option>
                    <option value="price_desc" <?php if ($sort === 'price_desc')
                    echo 'selected' ?>>Prijs (hoog - laag)
                    </option>
                    <option value="size_asc" <?php if ($sort === 'size_asc')
                    echo 'selected' ?>>Grootte (klein - groot)
                    </option>
                    <option value="size_desc" <?php if ($sort === 'size_desc')
                    echo 'selected' ?>>Grootte (groot - klein)
                    </option>
                </select>
            </label>

            <button type="submit">Filteren</button>
        </form>

        <div class="container">
        <?php foreach ($locations as $location): ?>
            <div class="product">
                <img src="<?php echo htmlspecialchars($location['photo_url']) ?>"
                    alt="Plek <?php echo htmlspecialchars($location['number']) ?>">
                <h3>Location number: <?php echo htmlspecialchars($location['number']) ?></h3>
                <h3>Maximum guests: <?php echo htmlspecialchars($location['max_guests']) ?></h3>
                <p>€ <?php echo number_format($location['price_per_night'], 2) ?></p>
                <a href="Location_detail.php?id=<?php echo urlencode($location['id']) ?>">Bekijk</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php require 'footer.php'; ?>