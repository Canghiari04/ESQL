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
            top: 150px; 
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

        button {
            color: black;
            background-color: white;
            border: 2px solid black;
            border-radius: 10px;
            text-align: center;
            padding: 14px 32px;
            text-decoration: none;
        }

        button:hover {
            transition: color .4s;
            color: #ff3131;
            border-color: #ff3131;
        }
                
        .undo {
            float: left;
            margin-left: 25px;
            margin-top: 15px;
            margin-bottom: 15px;
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

        .navbar {
            overflow: hidden;
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center; /* Center items vertically */
        }

        .button-Login {
            margin-top: 10px;
            margin-right: 15px;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="index.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
        <form action="login.php">
            <button class="button-Login" type="submit">Login</button>
        </form>
    </div>

    <div class="background">
        <div class="center">
            <form action="authentication.php" method="POST">
                <div style="margin-top: 30px;">
                    <label class="label-inline">Email</label>
                    <input class="input" type="text" id="txtEmailSignupStudente" name="txtEmailSignupStudente">
                </div>
                <div style="margin-top: 8px;">
                    <label>Password</label>
                    <input class="input" type="password" id="txtPasswordSignupStudente" name="txtPasswordSignupStudente">
                </div>
                <div style="margin-top: 8px;">
                    <label class="label-inline">Nome</label>
                    <input class="input" type="text" id="txtNomeSignupStudente" name="txtNomeSignupStudente">
                </div>
                <div style="margin-top: 8px;">
                    <label>Cognome</label>
                    <input class="input" type="text" id="txtCognomeSignupStudente" name="txtCognomeSignupStudente">
                </div>
                <div style="margin-top: 8px;">
                    <label>Recapito telefonico</label>
                    <input class="input" type="numeric" id="txtTelefonoSignupStudente" name="txtTelefonoSignupStudente">
                </div>
                <div style="margin-top: 8px;">
                    <label>Anno immatricolazione</label>
                    <input class="input" type="numeric" id="txtAnnoImmatricolazione" name="txtAnnoImmatricolazione">
                </div>
                <div style="margin-top: 8px; margin-bottom: 20px;">
                    <label>Codice</label>
                    <input class="input" type="numeric" id="txtCodice" name="txtCodice">
                </div>
                <div>
                    <button type="submit" class="button-Signup" style="margin-right:25px;" name="btnSignup" value="Sign Up">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>