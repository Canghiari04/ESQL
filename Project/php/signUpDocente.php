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
            align-items: center;
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
        
        .non-Required {
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

        .input-Required:hover {
            border:2px solid #ff3131;
        }

        .non-Required:hover {
            border-bottom: 2px solid #ff3131;
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
                    <label>Email<span>*</span></label>
                    <input class="input-Required" type="email" id="txtEmailSignupDocente" name="txtEmailSignupDocente" required>
                </div>
                <div style="margin-top: 8px;">
                    <label>Password<span>*</span></label>
                    <input class="input-Required" type="password" id="txtPasswordSignupDocente" name="txtPasswordSignupDocente" required>
                </div>
                <div style="margin-top: 8px;">
                    <label>Nome<span>*</span></label>
                    <input class="input-Required" type="text" id="txtNomeSignupDocente" name="txtNomeSignupDocente" required>
                </div>
                <div style="margin-top: 8px;">
                    <label>Cognome<span>*</span></label>
                    <input class="input-Required" type="text" id="txtCognomeSignupDocente" name="txtCognomeSignupDocente" required>
                </div>
                <div style="margin-top: 8px;">
                    <label>Recapito telefonico</label>
                    <input class="non-Required" type="numeric" id="txtTelefonoSignupDocente" name="txtTelefonoSignupDocente" value="NULL">
                </div>
                <div style="margin-top: 8px;">
                    <label>Corso<span>*</span></label>
                    <input class="input-Required" type="text" id="txtCorso" name="txtCorso" required>
                </div>
                <div style="margin-top: 8px; margin-bottom: 20px;">
                    <label>Dipartimento<span>*</span></label>
                    <input class="input-Required" type="text" id="txtDipartimento" name="txtDipartimento" required>
                </div>
                <div>
                    <button type="submit" style="margin-right:25px;" name="btnSignup" value="Sign Up">Sign Up</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>