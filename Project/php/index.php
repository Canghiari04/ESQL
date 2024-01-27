<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
    <style>
        html, body {
            font-family: 'Public Sans';
            height: 100%;
        }

        p {
            color: #ff3131;
            font-size: 58px;
            font-weight: bold;
            display: inline;
        }

        h3 {
            color: darkgrey;
            font-weight: bold;
        }

        .navbar {
            overflow: hidden;
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .zoom-on-img {
            margin-top: 10px;
            margin-left: 15px;
            transition: transform .4s;
        }

        .zoom-on-img:hover {
            transform: scale(1.099);
        }

        .dropdown {
            float: right;
            margin-left: 1000px;
            margin-top: 20px;
        }

        .dropdown .dropbtn {
            border: none;
            outline: none;
            color: darkgray;
            background-color: inherit;
            margin: 0;
            margin-bottom: 10px;
        }

        .dropbtn:hover {
            transition: color .4s;
            color: #ff3131;
        }

        .dropdown-content {
            float: right;
            position: absolute;
            display: none;
            background-color: white;
            min-width: 90px;
            z-index: 1;
        }

        .dropdown-content a {
            float: none;
            font-size: 13px;
            color: darkgray;
            padding: 8px 12px;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .dropdown-content a:hover {
            transition: color .4s;
            background-color: rgba(195, 195, 195, 0.70);
            color: #ff3131;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .button-Login {
            float: right;
            display: block;
            color: black;
            background-color: white;
            border: 2px solid black;
            border-radius: 10px;
            text-align: center;
            padding: 14px 32px;
            margin-top: 10px;
            margin-right: 15px;
            text-decoration: none;
        }

        .button-Login:hover {
            transition: color .4s;
            color: #ff3131;
            border-color: #ff3131;
        }

        .background {
            height: 100%;
            background-image: url("img/background.png");
            background-repeat: no-repeat;
            background-size: cover;
        }

        .center {
            position: relative;
            background-color: rgba(255, 255, 255, 0.70);
            top: 200px;
            padding-top: 10px;
            padding-bottom: 15px;
            padding-left: 15px;
            padding-right: 15px;
            margin: auto;
            width: 45%;
            text-align: center;
            border-radius: 20px;
            transition: transform .4s;
        }

        .center:hover {
            transform: scale(1.03);
        }

        .center-center {
            width: 60%;
            display: block;
            position: relative;
            padding-top: 5px;
            padding-bottom: 15px;
            padding-left: 15px;
            padding-right: 15px;
            margin: auto;
        }

        .url-utility {
            padding-bottom: 10px;
            color: darkgray;
            margin-top: 5px;
            border: 2px solid transparent;
            text-align: center;
            text-decoration: none;
        }

        .url-utility:hover {
            transition: color .4s;
            color: #ff3131;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
        <div class="dropdown">
            <button class="dropbtn">Sign Up</button>
            <div class="dropdown-content">
                <a href="signUpStudente.php">Studente</a>
                <a href="signUpDocente.php">Docente</a>
            </div>
        </div>
        <form action="login.php">
            <button class="button-Login" type="submit">Login</button>
        </form>
    </div>

    <div class="background">
        <div class="center">
            <p>Piattaforma ESQL</p>
            <div class="center-center">
                <h3>Progetto del corso di Basi di Dati (70155) anno accademico 2023/2024, realizzato dagli studenti
                    Canghiari Matteo, De Rosa Davide e Nadifi Ossama.</h3>
            </div>
            <a class="url-utility" href="https://github.com/Canghiari04">Link Github</a>
        </div>
    </div>
</body>

</html>
