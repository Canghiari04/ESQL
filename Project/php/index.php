<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="css/navbar_linear.css">
        <link rel="stylesheet" type="text/css" href="css/index.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
            <div class="dropdown">
                <button class="dropbtn">Sign Up</button>
                <div class="dropdown-content">
                    <a href="login/signUpStudente.php">Studente</a>
                    <a href="login/signUpDocente.php">Docente</a>
                </div>
            </div>
            <form action="login/login.php">
                <button class="button-navbar" type="submit">Login</button>
            </form>
        </div>
        <div>
            <div class="center">
                <p>Piattaforma ESQL</p>
                <div class="center-center">
                    <h3>Progetto del corso di Basi di Dati (70155) anno accademico 2023/2024, realizzato dagli studenti Canghiari Matteo, De Rosa Davide e Nadifi Ossama.</h3>
                </div>
                <a class="url-utility" href="https://github.com/Canghiari04">Link Github</a>
            </div>
        </div>
    </body>
</html>