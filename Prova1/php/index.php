<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
    <style>
        body {
            font-family: 'Public Sans';
            background-image: url("img/background.png");
            background-repeat: no-repeat;
            background-size: cover;
            max-height: 1024px;
        }

        .h-p {
            color: #ff3131;
            font-size: 58px;
            font-weight: bold;
            display: inline;
        }

        .center {
            position: relative; /* Relative è l'unica posizione che puù essere spostata */
            top: 200px; 
            padding-top: 10px;
            padding-bottom: 15px;
            padding-left: 15px;
            padding-right: 15px;
            margin: auto;
            width: 45%; /* Larghezza del tag div */
            text-align: center;
            border-radius: 20px;
        }

        .center-center {
            width: 60%;
            position: relative; /* Relative è l'unica posizione che puù essere spostata */
            padding-top: 5px;
            padding-bottom: 15px;
            padding-left: 15px;
            padding-right: 15px;
            margin: auto;   
        }

        ul {
            position: relative;
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Necessario per spostare gli elementi nella navbar */ 
            background-color: white;
        }

        li a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .button-Login {
            float: right;
            display: block;
            color: black;
            background-color: white;
            border: 2px solid black;
            border-radius: 15px;
            text-align: center;
            padding: 14px 32px;
            margin-top: 18px;
            margin-right: 15px;
            text-decoration: none;
        }

        .button-Login:hover {
            transition: color .4s;
            color: white;
            background-color: black;
            border-color: white;
        }

        .button-Signup {
            float: right;
            display: block;
            color: black;
            background-color: transparent;
            border: 2px solid transparent;
            text-align: center;
            padding: 14px 32px;
            margin-top: 18px;
            margin-right: 25px;
            text-decoration: none;
        }

        .button-Signup:hover {
            transition: color .4s;
            color: grey;
            background-color: white;
        }

        .zoom-on-img {
            transition: transform .4s;
        }

        .zoom-on-img:hover {
            transform: scale(1.099);
        }

        .img-div {
            background: url(img/background-dotpaper.png);
            background-position: no-repeat;
            background-size: cover;
        }

        .first-div {
            background: rgba(255,255,255,0.85); 
        }

        .url-utility {
            padding-bottom: 10px;
            margin-top: 5px;
            color: black;
            background-color: transparent;
            border: 2px solid transparent;
            text-align: center;
            text-decoration: none;
        }

        .url-utility:hover {
            transition: color .4s;
            color: #ff3131;
        }

        h3 {
            color: darkgrey;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <ul> <!-- ul riferito alla colonna, quindi creazione di una singola colonna -->
        <li> <!-- per applicare effetti all'interno di una navbar, occorre applicarli sulle singole righe che lo compongono ossia il tag <li> -->
            <a><img class="zoom-on-img" src="img/ESQL.png" alt="ESQL Icon" width="112" height="48"></a></li> <!-- Riga, creazione di tre righe totali -->
        </li>
        <li>
            <button class="button-Login zoom-on-btn-Login" name="btn_Login" value="Login">Login</button>
        </li>
        <li>
            <button class="button-Signup zoom-on-btn-Signup" name="btn_SignUp" value="Sign Up">Sign Up</button>
        </li>
    </ul>

    <div>
        <div class="center first-div">
            <p class="h-p">Piattaforma ESQL</p>
            <div class="center-center"><h3>Progetto del corso di Basi di Dati (70155) anno accademico 2023/2024, realizzato dagli studenti Canghiari Matteo, De Rosa Davide e Nadifi Ossama.</h3></div>
                <a class="url-utility" href="https://github.com/Canghiari04">Link Github</a>
        </div>
    </div>
</body>
</html>