<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
    <style>
        body {
            font-family: 'Public Sans';
        }

        .h-p {
            color: #ff3131;
            font-size: 20px;
            font-weight: bold;
            display: inline;
        }

        .center {
            position: relative; /* Relative è l'unica posizione che puù essere spostata */
            display: block;
            top: 200px; 
            padding-top: 10px;
            padding-bottom: 15px;
            padding-left: 15px;
            padding-right: 15px;
            margin: auto;
            width: 30%; /* Larghezza del tag div */
            text-align: center;
            border-radius: 20px;
            background-color: rgba(255,255,255,0.70);
            transition: transform .4s;
        }

        .center:hover {
            transform: scale(1.03);
        }

        ul {
            position: relative;
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden; /* Necessario per spostare gli elementi nella navbar */ 
            background-color: white;
        }

        li .a {
            float: left;
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        .undo {
            float: right;
            margin-right: 15px;
            margin-top: 30px;
        }

        input {
            background-color: transparent;
            border-color: transparent;
            border-radius: 10px;
        }

        button {
            color: black;
            background-color: white;
            border: 2px solid black;
            border-radius: 10px;
            text-align: center;
            padding: 14px 32px;
            margin-top: 18px;
            margin-right: 15px;
            text-decoration: none;
        }

        button:hover {
            transition: color .4s;
            color: #ff3131;
            border-color: #ff3131;
        }

        .zoom-on-img {
            transition: transform .4s;
        }

        .zoom-on-img:hover {
            transform: scale(1.099);
        }

        .first-div {
            background: rgba(255,255,255,0.70); 
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

        .background {
            padding: auto;
            background-image: url("img/background.png");
            background-repeat: no-repeat;
            background-size: cover; /*  */
        }

        h3 {
            color: darkgrey;
            font-weight: bold;
        }

        label {
            display: relative;
            float: left;
        }

        label:hover {
            color: #ff3131;
        }

        .input {
            font-size: 12px;
            width: 100%;
            padding: 3px 3px;
            margin: 1px 0;
            border-bottom: 1px solid darkgrey;
            border-radius: 0px;
            outline: none;
        }

        .input:hover {
            border-bottom: 2px solid #ff3131;
        }

        .label- {
            margin: auto;
            margin-left: 100px;
        }
    </style>
</head>

<body class="background">
    <ul> <!-- ul riferito alla colonna, quindi creazione di una singola colonna -->
    <form action="index.php">
        <li> <!-- per applicare effetti all'interno di una navbar, occorre applicarli sulle singole righe che lo compongono ossia il tag <li> -->
            <a class="a"><img class="zoom-on-img" src="img/ESQL.png" alt="ESQL Icon" width="112" height="48"></a></li> <!-- Riga, creazione di tre righe totali -->
        </li>
        <li>
            <a href="index.php"><img class="zoom-on-img undo" src="img/undo.png" width="24" height="24"></a>
        </li>
    </form>    
    </ul>
    
    <div class="center">
        <form action="access.php" method="POST">
            <p class="h-p" style="margin-top: 10px;">Inserisci le credenziali</p>
            <div style="margin-top: 30px;">
                <label class="label-inline">Email</label>
                <input class="input" type="text" id="txtEmail" name="txtEmail">
            </div>
            <div style="margin-top: 8px; margin-bottom: 20px;">
                <label>Password</label>
                <input class="input" type="password" id="txtPassword" name="txtPassword">
            </div>
            <div>
                <button type="submit" style="margin-right:25px;" name="btnAccedi" value="Accedi">Accedi</button>
                <button type="submit" style="margin-left:25px;" name="btnIscriviti" value="Iscriviti">Iscriviti</button>
            </div>
        </form>
    </div>
</body>
</html>