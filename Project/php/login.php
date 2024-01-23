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
            float: left;
            margin-left: 25px;
            margin-top: 30px;
        }

        input {
            background-color: transparent;
            border-color: transparent;
            border-radius: 10px;
        }

        .button-Accedi {
            color: black;
            background-color: white;
            border: 2px solid black;
            border-radius: 10px;
            text-align: center;
            padding: 14px 32px;
            text-decoration: none;
        }

        .button-Accedi:hover {
            transition: color .4s;
            color: #ff3131;
            border-color: #ff3131;
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
            margin-bottom: 20px;
            text-decoration: none;
        }

        .button-Signup:hover {
            transition: color .4s;
            color: #ff3131;
            background-color: white;
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
    <form action="signUp.php">
        <li>
            <a href="index.php"><img class="zoom-on-img undo" src="img/undo.png" width="24" height="24"></a>
        </li>
        <li>
            <button type="submit" class="button-Signup zoom-on-btn-Signup" name="btn_SignUp" value="Sign Up">Sign Up</button>
        </li>
    </form>    
    </ul>
    
    <div class="center">
        <form action="authentication.php" method="POST">
            <p class="h-p" style="margin-top: 10px;">Inserisci le credenziali</p>
            <div style="margin-top: 30px;">
                <label class="label-inline">Email</label>
                <input class="input" type="text" id="txtEmailLogin" name="txtEmailLogin">
            </div>
            <div style="margin-top: 8px; margin-bottom: 20px;">
                <label>Password</label>
                <input class="input" type="password" id="txtPasswordLogin" name="txtPasswordLogin">
            </div>
            <div>
                <button type="submit" class="button-Accedi" style="margin-right:25px;" name="btnAccedi" value="Accedi">Accedi</button>
            </div>
        </form>
    </div>
</body>
</html>