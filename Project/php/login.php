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

        label {
            display: relative;
            float: left;
        }
        
        label:hover {
            color: #ff3131;
        }

        input {
            background-color: transparent;
            border-color: transparent;
            border-radius: 10px;
        }
        
        input:hover {
            border:2px solid #ff3131;
        }
        
        h3 {
            color: darkgrey;
            font-weight: bold;
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

        .dropdown {
            float: right;
            margin-top: 18px;
            margin-right: 45px;
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
            float: left;
            position: absolute;
            display: none;
            background-color: white;
            min-width: 90px;
            z-index: 1;
        }
        
        .dropdown-content a {
            float: left;
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
        
        .background {
            height: 100%; 
            background-image: url(img/background.png);
            background-repeat: no-repeat;
            background-size: cover; /*  */
        }
        
        .center {
            position: relative; /* Relative è l'unica posizione che puù essere spostata */
            display: block;
            top: 225px; 
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

        .input {
            font-size: 12px;
            width: 100%;
            padding: 3px 3px;
            margin: 1px 0;
            border-bottom: 1px solid darkgrey;
            border-radius: 0px;
            outline: none;
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
    </style>
</head>

<body>
    <div class="navbar">
        <a href="index.php?"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
        <div class="dropdown">
            <button class="dropbtn">Sign Up</button>
            <div class="dropdown-content">
                <a href="signUpStudente.php">Studente</a>
                <a href="signUpDocente.php">Docente</a>
            </div>
        </div>
    </div>
    
    <div class="background">
        <div class="center">
            <form action="authentication.php" method="POST">
                <div style="margin-top: 30px;">
                    <label>Email<span>*</span></label>
                    <input class="input" type="email" id="txtEmailLogin" name="txtEmailLogin" required>
                </div>
                <div style="margin-top: 8px; margin-bottom: 20px;">
                    <label>Password<span>*</span></label>
                    <input class="input" type="password" id="txtPasswordLogin" name="txtPasswordLogin" required>
                </div>
                <div>
                    <button type="submit" class="button-Accedi" style="margin-right:25px;" name="btnAccedi" value="Accedi">Accedi</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>