<?php

$sql_size = "SELECT DISTINCT size_m2 FROM locations WHERE size_m2 IS NOT NULL";
$sql_electricity = "SELECT DISTINCT has_electricity FROM locations WHERE has_electricity IS NOT NULL";
$sql_water= "SELECT DISTINCT near_water FROM locations WHERE near_water IS NOT NULL";

$size_m2 = $conn->query($sql_size)->fetchAll(PDO::FETCH_ASSOC);
$has_electricity = $conn->query($sql_electricity)->fetchAll(PDO::FETCH_ASSOC);
$near_water = $conn->query($sql_water)->fetchAll(PDO::FETCH_ASSOC);
?>

<select name="size">
    <option value="">-- All Sizes --</option>
    <?php foreach ($size_m2 as $Size_m2): ?>
        <option value="<?php echo htmlspecialchars($Size_m2['size_m2']); ?>" <?php echo (($_GET['size_m2'] ?? '') == $Size_m2['size_m2']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($Size_m2['size_m2']); ?>
            
        </option>
    <?php endforeach; ?>
</select>

<select name="electricity">
    <option value="">-- Does it have Electricity --</option>
    <?php foreach ($has_electricity as $Has_electricity): ?>
        <option value="<?php echo htmlspecialchars($Has_electricity['has_electricity']); ?>" <?php echo (($_GET['has_electricity'] ?? '') == $Has_electricity['has_electricity']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($Has_electricity['has_electricity']); ?>
        </option>
    <?php endforeach; ?>
</select>

<select name="water">
    <option value="">-- Is it near water --</option>
    <?php foreach ($near_water as $Near_water): ?>
        <option value="<?php echo htmlspecialchars($Near_water['near_water']); ?>" <?php echo (($_GET['near_water'] ?? '') == $Near_water['near_water']) ? 'selected' : ''; ?>>
            <?php echo htmlspecialchars($Near_water['near_water']); ?> pagina's
        </option>
    <?php endforeach; ?>
</select>