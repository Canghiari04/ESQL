<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public San" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_dropdown.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/login.css">
    </head>
    <body>
        <div class="navbar">
            <a href="../../index.php?"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
            <div class="dropdown">
                <button class="dropbtn">Sign Up</button>
                <div class="dropdown-content">
                    <a href="signUpStudente.php">Studente</a>
                    <a href="signUpDocente.php">Docente</a>
                </div>
            </div>
        </div>
        <div>
            <div class="center">
                <form action="authentication.php" method="POST">
                    <div>
                        <label>Email<span>*</span></label>
                        <input class="input" type="email" id="txtEmailLogin" name="txtEmailLogin" required>
                    </div>
                    <div>
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