<?php 
    include "../../connectionDB.php";

    session_start();

    $conn = openConnection();
    $manager = openConnectionMongoDB();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public San" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/log_error.css">
    </head>
</html>
    <body>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["txtEmailLogin"])) {
                    $sql = "SELECT * FROM Utente WHERE (Utente.EMAIL=:email) AND (Utente.PSWD=:pswd);";

                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":email", $_POST["txtEmailLogin"]);
                        $result -> bindValue(":pswd", $_POST["txtPasswordLogin"]);

                        $result -> execute();
                    } catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }  
                        
                    $numRows = $result -> rowCount();
                    if($numRows > 0) {
                        $type =  typeUtente($conn, $_POST["txtEmailLogin"]);

                        if($type == "Studente") { // diversificazione del reindirizzamento a seconda della tipologia
                            $_SESSION["emailStudente"] = $_POST["txtEmailLogin"];                                
                            header("Location: ../../studente/handlerStudente.php");  
                            exit();
                        } else {
                            $_SESSION["emailDocente"] = $_POST["txtEmailLogin"];
                            header("Location: ../../docente/handlerDocente.php");
                            exit();
                        }
                    } else {
                        loginError();
                    } 
                } elseif(isset($_POST["txtEmailSignupStudente"])) {
                    $sql = "SELECT Utente.EMAIL FROM Utente WHERE (Utente.EMAIL=:email);";

                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":email", $_POST["txtEmailSignupStudente"]);
                    
                        $result -> execute();
                    } catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }  
                    
                    $numRows = $result -> rowCount();
                    if($numRows > 0) {
                        signUpError("signUpStudente.php");
                    } else {
                        insertStudente($conn, $_POST["txtEmailSignupStudente"], $_POST["txtPasswordSignupStudente"], $_POST["txtNomeSignupStudente"], $_POST["txtCognomeSignupStudente"], checkTelephone($_POST["txtTelefonoSignupStudente"]), $_POST["txtAnnoImmatricolazione"], $_POST["txtCodice"]);

                        $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento studente: '.$_POST["txtEmailSignupStudente"].'', 'Timestamp' => date('Y-m-d H:i:s')];
                        writeLog($manager, $document); // scrittura log inserimento di un nuovo studente
                        header("Location: login.php");
                        exit();
                    }
                } elseif(isset($_POST["txtEmailSignupDocente"])) {
                    $sql = "SELECT Utente.EMAIL FROM Utente WHERE (Utente.EMAIL=:email);";

                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":email", $_POST["txtEmailSignupDocente"]);

                        $result -> execute();
                    } catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }  
                    
                    $numRows = $result -> rowCount();
                    if($numRows > 0) {
                        signUpError("signUpDocente.php");
                    } else {
                        insertDocente($conn, $_POST["txtEmailSignupDocente"], $_POST["txtPasswordSignupDocente"], $_POST["txtNomeSignupDocente"], $_POST["txtCognomeSignupDocente"], checkTelephone($_POST["txtTelefonoSignupDocente"]), $_POST["txtDipartimento"], $_POST["txtCorso"]);

                        $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento docente: '.$_POST["txtEmailSignupDocente"].'', 'Timestamp' => date('Y-m-d H:i:s')];
                        writeLog($manager, $document); // scrittura log inserimento di un nuovo docente
                        header("Location: login.php");
                        exit();
                    }
                } 
            }

            function typeUtente($conn, $email) {
                $sql = "SELECT Utente.EMAIL FROM Utente JOIN Studente ON (Utente.EMAIL=Studente.EMAIL_STUDENTE) WHERE (Utente.EMAIL=:labelEmail);";
                
                try {
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":labelEmail", $email);
                    
                    $result -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
                
                $numRows = $result -> rowCount();
                if($numRows > 0) {
                    return "Studente";
                } else {
                    return "Docente";
                }
            }

            function loginError() {
                echo '
                    <form action="login.php">
                        <div>
                            <h4>Utente non esistente o credenziali errate</h4>
                            <button type="submit">Login</button>
                        </div>
                    </form>
                ';
            }

            function signUpError($namePage) {
                echo '
                    <form action="'.$namePage.'">
                        <div>
                            <h4>Credenziali esistenti, riprova la registrazione</h4>
                            <button type="submit">Sign Up</button>
                        </div>
                    </form>
                ';
            }

            function checkTelephone($telefono) {
                if($telefono == "NULL") {
                    return NULL;
                }
            }

            function insertStudente($conn, $email, $password, $nome, $cognome, $telefono, $annoImmatricolazione, $codice) {
                $storedProcedure = "CALL Inserimento_Studente(:labelEmail, :labelPassword, :labelNome, :labelCognome, :labelTelefono, :labelAnno, :labelCodice);";
                
                try {    
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue(":labelEmail", $email);
                    $stmt -> bindValue(":labelPassword", $password);
                    $stmt -> bindValue(":labelNome", $nome);
                    $stmt -> bindValue(":labelCognome", $cognome);
                    $stmt -> bindValue(":labelTelefono", $telefono);
                    $stmt -> bindValue(":labelAnno", $annoImmatricolazione);
                    $stmt -> bindValue(":labelCodice", $codice);
                    
                    $stmt -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
            }

            function insertDocente($conn, $email, $password, $nome, $cognome, $telefono, $dipartimento, $corso) {
                $storedProcedure = "CALL Inserimento_Docente(:labelEmail, :labelPassword, :labelNome, :labelCognome, :labelTelefono, :labelDipartimento, :labelCorso);";
                
                try {        
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue(":labelEmail", $email);
                    $stmt -> bindValue(":labelPassword", $password);
                    $stmt -> bindValue(":labelNome", $nome);
                    $stmt -> bindValue(":labelCognome", $cognome);
                    $stmt -> bindValue(":labelTelefono", $telefono);
                    $stmt -> bindValue(":labelDipartimento", $dipartimento);
                    $stmt -> bindValue(":labelCorso", $corso);
                    
                    $stmt -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
            }

            closeConnection($conn);
        ?>
    </body>
</html>