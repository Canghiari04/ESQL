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
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/log_error.css">
    </head>
</html>
    <body>
        <?php
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["txtEmailLogin"])) {
                    $email = $_POST["txtEmailLogin"];
                    $password = $_POST["txtPasswordLogin"];

                    /* mai mettere COUNT(*) nelle query, restituisce sempre un valore maggiore di 0, quindi non è attendibile */
                    $sql = "SELECT Utente.EMAIL FROM Utente WHERE (Utente.EMAIL=:labelEmail) AND (Utente.PSWD=:labelPassword);";

                    try {
                        $result = $conn -> prepare($sql);

                        /* bindValue è un metodo cruciale per evitare SQL injection causando quindi possibili ritorsioni a livello di implementazione; evitando in questo modo la possibilità di oltrepassare i controlli necesssari*/
                        $result -> bindValue(":labelEmail", $email);
                        $result -> bindValue(":labelPassword", $password);

                        $result -> execute();
                    } catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }  
                        
                    $numRows = $result -> rowCount();
                    if($numRows > 0) {
                        $tipo =  typeUtente($conn, $email);

                        /* tramite lo start della sessione viene salvata la email dell'utente che abbia effettuato il login */
                        if($tipo == "Studente") {
                            $_SESSION["emailStudente"] = $email;
                                
                            /* metodo per reindirizzare tramite uso di HTTP */
                            header("Location: ../../studente/handlerStudente.php");  
                            exit();
                        } else {
                            $_SESSION["emailDocente"] = $email;
                                
                            header("Location: ../../docente/handlerDocente.php");
                            exit();
                        }
                    } else {
                        loginError();
                    } 
                } elseif(isset($_POST["txtEmailSignupStudente"])) {
                    $email = $_POST["txtEmailSignupStudente"];
                    $password = $_POST["txtPasswordSignupStudente"];
                    $nome = $_POST["txtNomeSignupStudente"];
                    $cognome = $_POST["txtCognomeSignupStudente"];
                    $telefono = $_POST["txtTelefonoSignupStudente"];
                    $annoImmatricolazione = $_POST["txtAnnoImmatricolazione"];
                    $codice = $_POST["txtCodice"];

                    $sql = "SELECT Utente.EMAIL FROM Utente WHERE (Utente.EMAIL=:labelEmail);";

                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":labelEmail", $email);
                    
                        $result -> execute();
                    } catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }  
                    
                    $numRows = $result -> rowCount();
                    if($numRows > 0) {
                        signUpError("signUpStudente.php");
                    } else {
                        /* controllo per verifica assenza di recapito telefonico */
                        $telefono = checkTelephone($telefono);
                        insertStudente($conn, $email, $password, $nome, $cognome, $telefono, $annoImmatricolazione, $codice);

                        /* scrittura log inserimento di un nuovo studente all'interno del database */
                        $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento studente: '.$email.'', 'Timestamp' => date('Y-m-d H:i:s')];
                        writeLog($manager, $document);

                        header("Location: login.php");
                        exit();
                    }
                } elseif(isset($_POST["txtEmailSignupDocente"])) {
                    $email = $_POST["txtEmailSignupDocente"];
                    $password = $_POST["txtPasswordSignupDocente"];
                    $nome = $_POST["txtNomeSignupDocente"];
                    $cognome = $_POST["txtCognomeSignupDocente"];
                    $telefono = $_POST["txtTelefonoSignupDocente"];
                    $dipartimento = $_POST["txtDipartimento"];
                    $corso = $_POST["txtCorso"];

                    $sql = "SELECT Utente.EMAIL FROM Utente WHERE (Utente.EMAIL=:labelEmail);";

                    try {
                        $result = $conn -> prepare($sql);
                        $result -> bindValue(":labelEmail", $email);

                        $result -> execute();
                    } catch(PDOException $e) {
                        echo "Eccezione ".$e -> getMessage()."<br>";
                    }  
                    
                    $numRows = $result -> rowCount();
                    if($numRows > 0) {
                        signUpError("signUpDocente.php");
                    } else {
                        $telefono = checkTelephone($telefono);
                        insertDocente($conn, $email, $password, $nome, $cognome, $telefono, $dipartimento, $corso);

                        /* scrittura log inserimento di un nuovo docente all'interno del database */
                        $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento docente: '.$email.'', 'Timestamp' => date('Y-m-d H:i:s')];
                        writeLog($manager, $document);

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

            /* message error qualora non soddisfatti requisiti di registrazione oppure di login, con successivo reindirizzamento alla pagina precedente */
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

            /* funzione necessaria, dato che i tag possono restituire solo valori di default di tipo stringa */
            function checkTelephone($telefono) {
                if($telefono == "NULL") {
                    return NULL;
                }
            }

            function insertStudente($conn, $email, $password, $nome, $cognome, $telefono, $annoImmatricolazione, $codice) {
                /* string per richiamare la stored procedure, senza che sia posti i campi */
                $storedProcedure = "CALL Inserimento_Studente(:labelEmail, :labelPassword, :labelNome, :labelCognome, :labelTelefono, :labelAnno, :labelCodice);";
                
                try {    
                    /* si crea lo statement necessario per richiamare la stored procedure */
                    $stmt = $conn -> prepare($storedProcedure);
                    $stmt -> bindValue(":labelEmail", $email);
                    $stmt -> bindValue(":labelPassword", $password);
                    $stmt -> bindValue(":labelNome", $nome);
                    $stmt -> bindValue(":labelCognome", $cognome);
                    $stmt -> bindValue(":labelTelefono", $telefono);
                    $stmt -> bindValue(":labelAnno", $annoImmatricolazione);
                    $stmt -> bindValue(":labelCodice", $codice);
                    
                    /* si esegue la stored procedure */
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

            /* chiusura necessaria, poichè se si indirizza in una nuova pagina php potrebbe riportare errori a livello db */
            closeConnection($conn);
        ?>
    </body>
</html>