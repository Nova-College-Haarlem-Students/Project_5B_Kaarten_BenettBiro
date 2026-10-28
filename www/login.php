<?php ?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Sport Club</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="inloggen.css">
</head>

<body>

    <div class="login-wrapper">
        <div class="login-card">

            <h1>Inloggen</h1>

            <form action="login-process.php" method="post">

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" placeholder="jouw@email.com">
                </div>

                <div class="form-group">
                    <label for="password">Wachtwoord</label>
                    <input type="password" name="password" id="password" placeholder="*******">
                </div>

                <button type="submit" name="submit" class="login-btn">
                    Inloggen
                </button>

            </form>

            <p class="login-footer">
                Nog geen account?
                <a href="register.php">Registreer hier</a>
            </p>

        </div>
    </div>

</body>

</html>