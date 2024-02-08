<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <style>
            <?php
                include 'css/signUpDocente.css';
            ?>
        </style>
    </head>
    <body>
        <div class="navbar">
            <a href="index.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
            <form action="login.php">
                <button class="button-Login" type="submit">Login</button>
            </form>
        </div>
        <div>
            <div class="center">
                <form action="authentication.php" method="POST">
                    <div style="margin-top: 30px;">
                        <label>Email<span>*</span></label>
                        <input type="email" id="txtEmailSignupDocente" name="txtEmailSignupDocente" required>
                    </div>
                    <div style="margin-top: 8px;">
                        <label>Password<span>*</span></label>
                        <input type="password" id="txtPasswordSignupDocente" name="txtPasswordSignupDocente" required>
                    </div>
                    <div style="margin-top: 8px;">
                        <label>Nome<span>*</span></label>
                        <input type="text" id="txtNomeSignupDocente" name="txtNomeSignupDocente" required>
                    </div>
                    <div style="margin-top: 8px;">
                        <label>Cognome<span>*</span></label>
                        <input type="text" id="txtCognomeSignupDocente" name="txtCognomeSignupDocente" required>
                    </div>
                    <div style="margin-top: 8px;">
                        <label>Recapito telefonico</label>
                        <input type="numeric" id="txtTelefonoSignupDocente" name="txtTelefonoSignupDocente" value="NULL">
                    </div>
                    <div style="margin-top: 8px;">
                        <label>Corso<span>*</span></label>
                        <input type="text" id="txtCorso" name="txtCorso" required>
                    </div>
                    <div style="margin-top: 8px; margin-bottom: 20px;">
                        <label>Dipartimento<span>*</span></label>
                        <input type="text" id="txtDipartimento" name="txtDipartimento" required>
                    </div>
                    <div>
                        <button type="submit" style="margin-right:25px;" name="btnSignup" value="Sign Up">Sign Up</button>
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>