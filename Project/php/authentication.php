<?php
    include 'connectionDB.php';
    $conn = openConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["txtEmailLogin"])) {
            $email = $_POST["txtEmailLogin"];
            $password = $_POST["txtPasswordLogin"];

            /* mai mettere COUNT(*) nelle query di login, restituisce sempre un valore maggiore di 0 */
            $sql = "SELECT EMAIL FROM Utente WHERE (EMAIL=:labelEmail) AND (PSWD=:labelPassword)";
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":labelEmail", $email);
                $result -> bindValue(":labelPassword", $password);
                $result -> execute();
                $numRows = $result -> rowCount();
                
                if($numRows > 0){
                    $tipo =  loginUtente($conn, $email);
                    
                    if($tipo == "Studente") {
                        /* metodo per reindirizzare tramite uso di HTTP */
                        header("Location: handlerStudente.php");                      
                    } else {
                        header("Location: handlerDocente.php");
                    }
                } else {
                    /* messageBox a video, data la presenza già dell'utente */
                }
            } catch(Exception $e) {
                echo 'Eccezione individuata: '. $e -> getMessage();
            }   

            /* chiusura necessaria, poichè se si indirizza in una nuova pagina php-html potrebbe riportare errori a livello di definition-manipulation del db */
            closeConnection($conn);
        } elseif (isset($_POST["txtEmailSignupStudente"])) {
            $email = $_POST["txtEmailSignupStudente"];
            $password = $_POST["txtPasswordSignupStudente"];
            $nome = $_POST["txtNomeSignupStudente"];
            $cognome = $_POST["txtCognomeSignupStudente"];
            $telefono = $_POST["txtTelefonoSignupStudente"];
            $annoImmatricolazione = $_POST["txtAnnoImmatricolazione"];
            $codice = $_POST["txtCodice"];
            
            $sql = "SELECT EMAIL FROM Utente JOIN Studente ON (Utente.EMAIL=Studente.EMAIL_STUDENTE) WHERE (EMAIL=:labelEmail) AND (PSWD=:labelPassword)";
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":labelEmail", $email);
                $result -> bindValue(":labelPassword", $password);
                $result -> execute();
                $numRows = $result -> rowCount();
                
                if($numRows > 0){
                    /* messageBox che evidenzia la presenza dell'utente, magari con un suggerimento dopo errore ripetuto */
                } else{
                    /* controllo per verifica assenza di recapito telefonico */
                    $telefono = controlTelefono($telefono);
                    insertStudente($conn, $email, $password, $nome, $cognome, $telefono, $annoImmatricolazione, $codice);
                    header("Location: login.php");
                }
            } catch(Exception $e) {
                echo 'Eccezione individuata: '. $e -> getMessage();
            }  

            closeConnection($conn);
        } elseif (isset($_POST["txtEmailSignupDocente"])) {
            $email = $_POST["txtEmailSignupDocente"];
            $password = $_POST["txtPasswordSignupDocente"];
            $nome = $_POST["txtNomeSignupDocente"];
            $cognome = $_POST["txtCognomeSignupDocente"];
            $telefono = $_POST["txtTelefonoSignupDocente"];
            $corso = $_POST["txtCorso"];
            $dipartimento = $_POST["txtDipartimento"];

            $sql = "SELECT EMAIL FROM Utente JOIN Docente ON (Utente.EMAIL=Docente.EMAIL_DOCENTE) WHERE (EMAIL = :labelEmail) AND (PSWD = :labelPassword)";
            
            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":labelEmail", $email);
                $result -> bindValue(":labelPassword", $password);
                $result -> execute();
                $numRows = $result -> rowCount();
        
                if($numRows > 0) {
                    /* messageBox che evidenzia la presenza dell'utente, magari con un suggerimento dopo errore ripetuto */
                } else {
                    $telefono = controlTelefono($telefono);
                    insertDocente($conn, $email, $password, $nome, $cognome, $telefono, $dipartimento, $corso);
                    header("Location: login.php");
                }
            } catch(Exception $e) {
                echo 'Eccezione individuata: '. $e -> getMessage();
            }  

            closeConnection($conn);
        } 
    }

    function loginUtente($conn, $email){
        $sql = "SELECT EMAIL FROM Utente JOIN Studente ON (EMAIL=EMAIL_STUDENTE) WHERE (EMAIL=:labelEmail)";
        
        $result = $conn -> prepare($sql);
        $result -> bindValue(":labelEmail", $email);
        $result -> execute();
        
        $numRows = $result -> rowCount();
        
        if($numRows > 0) {
            return "Studente";
        } else {
            return "Docente";
        }
    }

    function insertStudente($conn, $email, $password, $nome, $cognome, $telefono, $annoImmatricolazione, $codice) {
        /* string per richiamare la stored procedure, senza che sia posti i campi */
        $storedProcedure = "CALL Registrazione_Studente(:labelEmail, :labelPassword, :labelNome, :labelCognome, :labelTelefono, :labelAnno, :labelCodice)";
        
        /* si crea lo statement necessario per richiamare la stored procedure  */
        $stmt = $conn -> prepare($storedProcedure);

        /* si associano i valori della stored procedure rispetto ai campi estrapolati */
        $stmt -> bindValue(":labelEmail", $email);
        $stmt -> bindValue(":labelPassword", $password);
        $stmt -> bindValue(":labelNome", $nome);
        $stmt -> bindValue(":labelCognome", $cognome);
        $stmt -> bindValue(":labelTelefono", $telefono);
        $stmt -> bindValue(":labelAnno", $annoImmatricolazione);
        $stmt -> bindValue(":labelCodice", $codice);

        /* si esegue la stored procedure */
        $stmt -> execute();
    }

    function insertDocente($conn, $email, $password, $nome, $cognome, $telefono, $dipartimento, $corso) {
        $storedProcedure = "CALL Registrazione_Docente(:labelEmail, :labelPassword, :labelNome, :labelCognome, :labelTelefono, :labelDipartimento, :labelCorso)";
        $stmt = $conn -> prepare($storedProcedure);

        $stmt -> bindValue(":labelEmail", $email);
        $stmt -> bindValue(":labelPassword", $password);
        $stmt -> bindValue(":labelNome", $nome);
        $stmt -> bindValue(":labelCognome", $cognome);
        $stmt -> bindValue(":labelTelefono", $telefono);
        $stmt -> bindValue(":labelDipartimento", $dipartimento);
        $stmt -> bindValue(":labelCorso", $corso);

        $stmt -> execute();
    }

    function controlTelefono($telefono) {
        /* funzione necessaria, dato che i tag possono restituire solo valori di default di tipo stringa */
        if($telefono == "NULL") {
            return NULL;
        }
    }
?>
