<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
    <style>
        body {
            font-family: 'Public Sans';
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
    </div>
</body>
</html>