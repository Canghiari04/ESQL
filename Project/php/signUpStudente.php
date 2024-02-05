<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
    <style>
        html, body {
            height: 100%;
            font-family: 'Public Sans';
        }

        label {
            display: relative;
            float: left;
        }

        label:hover {
            color: #ff3131;
        }

        .navbar {
            overflow: hidden;
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .undo {
            float: left;
            margin-left: 25px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .center {
            position: relative; /* Relative è l'unica posizione che puù essere spostata */
            display: block;
            top: 115px; 
            padding-top: 15px;
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

        .zoom-on-img {
            transition: transform .4s;
        }

        .zoom-on-img:hover {
            transform: scale(1.099);
        }

        .background {
            height: 100%;
            background-image: url(img/background.png);
            height: 100%;
            font-family: 'Public Sans';
        }
            
        label {
            display: relative;
            float: left;
        }
            
        label:hover {
            color: #ff3131;
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

        span {
            color: #ff3131;
        }
        
        .navbar {
            overflow: hidden;
            background-color: white;
            display: flex;
            justify-content: space-between;
            align-items: center; /* Center items vertically */
        }
        
        .zoom-on-img {
            transition: transform .4s;
        }
        
        .zoom-on-img:hover {
            transform: scale(1.099);
        }
        
        .undo {
            float: left;
            margin-left: 25px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .button-Login {
            margin-top: 10px;
            margin-right: 15px;
        }

        .background {
            height: 100%;
            background-image: url("img/background.png");
            background-repeat: no-repeat;
            background-size: cover;
        }

        .center {
            position: relative; /* Relative è l'unica posizione che puù essere spostata */
            display: block;
            top: 115px; 
            padding-top: 15px;
            padding-bottom: 15px;
            padding-left: 15px;
            background-repeat: no-repeat;
            background-size: cover; /*  */
        }

        .input-Required {
            font-size: 12px;
            width: 100%;
            padding: 3px 3px;
            margin: 1px 0;
            background-color: transparent;
            border-color: transparent;
            border-radius: 0px;
            border-bottom: 1px solid darkgrey;
            outline: none;
        }

        .no-Required {
            font-size: 12px;
            width: 100%;
            padding: 3px 3px;
            margin: 1px 0;
            background-color: transparent;
            border-color: transparent;
            border-bottom: 1px solid darkgrey;
            border-radius: 0px;
            outline: none;
        }

        .input-Required:hover {
            border:2px solid #ff3131;
        }

        .no-Required:hover {
            border-bottom: 2px solid #ff3131;
        }
    </style>
</head>

<body>
    <div class="navbar">
        <a href="index.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
        <form action="login.php">
            <button type="submit">Login</button>
        </form>
    </div>

    <div class="background">
        <div class="center">
            <form action="authentication.php" method="POST">
                <div style="margin-top: 30px;">
                    <label>Email<span>*</span></label>
                    <input class="input-Required" type="text" id="txtEmailSignupStudente" name="txtEmailSignupStudente" required>
                </div>
                <div style="margin-top: 8px;">
                    <label>Password<span>*</span></label>
                    <input class="input-Required" type="password" id="txtPasswordSignupStudente" name="txtPasswordSignupStudente" required>
                </div>
                <div style="margin-top: 8px;">
                    <label>Nome<span>*</span></label>
                    <input class="input-Required" type="text" id="txtNomeSignupStudente" name="txtNomeSignupStudente" required>
                </div>
                <div style="margin-top: 8px;">
                    <label>Cognome<span>*</span></label>
                    <input class="input-Required" type="text" id="txtCognomeSignupStudente" name="txtCognomeSignupStudente" required>
                </div>
                <div style="margin-top: 8px;">
                    <label>Recapito telefonico</label>
                    <input class="no-Required" type="numeric" id="txtTelefonoSignupStudente" name="txtTelefonoSignupStudente" value="NULL">
                </div>
                <div style="margin-top: 8px;">
                    <label>Anno immatricolazione<span>*</span></label>
                    <input class="input-Required" type="numeric" id="txtAnnoImmatricolazione" name="txtAnnoImmatricolazione" required>
                </div>
                <div style="margin-top: 8px; margin-bottom: 20px;">
                    <label>Codice<span>*</span></label>
                    <input class="input-Required" type="numeric" id="txtCodice" name="txtCodice" required>
                </div>
                <div>
                    <button type="submit" class="button-Signup" style="margin-right:25px;" name="btnSignup" value="Sign Up">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>