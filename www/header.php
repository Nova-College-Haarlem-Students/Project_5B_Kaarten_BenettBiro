<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camping</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <a href="index.php" class="logo">Camping</a>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>


                <li>
                    <a href="my_reservations.php">
                        Mijn reserveringen
                        <span class="count-badge"
                            id="header-reservation-count"><?php echo $_SESSION['reservation_count'] ?? 0 ?></span>
                    </a>
                </li>

                <?php if (isset($_SESSION['role_id']) && ($_SESSION['role_id'] == 2 || $_SESSION['role_id'] == 3)): ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li class="dropdown">
                        <a href="#">Plekken</a>
                        <div class="dropdown-content">
                            <a href="locations_index.php">Bekijken</a>
                            <a href="locations_create.php">Toevoegen</a>
                        </div>
                    </li>
                    <li class="dropdown">
                        <a href="#">Reserveringen</a>
                        <div class="dropdown-content">
                            <a href="reserve.php">Alle reserveringen</a>
                            <a href="locations_free.php">Niet gereserveerde plekken</a>
                            <a href="index.php">Home</a>
                        </div>
                    </li>
                    <li><a href="changes_index.php">Wijzigingen</a></li>
                <?php endif; ?>

                <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 3): ?>
                    <li class="dropdown">
                        <a href="#">Gebruikers</a>
                        <div class="dropdown-content">
                            <a href="users_index.php">Bekijken</a>
                            <a href="users_create.php">Toevoegen</a>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="logout.php" class="btn btn-danger">Uitloggen</a></li>
                <?php else: ?>
                    <li><a href="register.php">Account aanmaken</a></li>
                    <li><a href="login.php" class="btn btn-success">Inloggen</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>