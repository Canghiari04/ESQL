<?php
    include 'connectionDB.php';
    $conn = OpenConnection();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST["txtEmailLogin"])) {
            $email = $_POST["txtEmailLogin"];
            $password = $_POST["txtPasswordLogin"];

            /* query definita per la ricerca dell'utente nelle tabelle Docente e Studente*/
            $sql = "SELECT EMAIL FROM Utente WHERE (EMAIL='$email') AND (PSWD='$password')";
            
            try {
                $result = mysqli_query($conn, $sql);
                $numRows = mysqli_num_rows($result);

                if($numRows > 0){
                    $tipo =  loginUtente($conn, $email);
                    
                    if($tipo == "Studente") {
                        echo($tipo);
                        /* reindirizzare la pagina rispetto alla sezione studente */                        
                    } else {
                        /* reindirizzare la pagina rispetto alla sezione docente */                        
                        echo($tipo);
                    }
                } else {
                    /* messageBox a video, data la presenza già dell'utente */
                }
            } catch(Exception $e) {
                echo 'Eccezione individuata: '. $e -> getMessage();
            }   
        } elseif (isset($_POST["txtEmailSignupStudente"])) {
            $email = $_POST["txtEmailSignupStudente"];
            $password = $_POST["txtPasswordSignupStudente"];
            $nome = $_POST["txtNomeSignupStudente"];
            $cognome = $_POST["txtCognomeSignupStudente"];

            /* permettere l'inserimento null del numero di telefofono */
            $telefono = $_POST["txtTelefonoSignupStudente"];

            $annoImmatricolazione = $_POST["txtAnnoImmatricolazione"];
            $codice = $_POST["txtCodice"];
            
            /* mai mettere COUNT(*) nelle query di login, restituisce sempre 1*/
            $sql = "SELECT EMAIL FROM Utente JOIN Studente ON (Utente.EMAIL=Studente.EMAIL_STUDENTE) WHERE (EMAIL='$email') AND (PSWD='$password')";
            
            try {
                $result = mysqli_query($conn, $sql);
                $numRows = mysqli_num_rows($result);
                echo($numRows);
                if($numRows > 0){
                    /* messageBox che evidenzia la presenza dell'utente, magari con un suggerimento dopo errore ripetuto */
                } else{
                    insertStudente($conn, $email, $password, $nome, $cognome, $telefono, $annoImmatricolazione, $codice);
                }
            } catch(Exception $e) {
                echo 'Eccezione individuata: '. $e -> getMessage();
            }  
        } elseif (isset($_POST["txtEmailSignupDocente"])) {
            $email = $_POST["txtEmailSignupDocente"];
            $password = $_POST["txtPasswordSignupDocente"];
            $nome = $_POST["txtNomeSignupDocente"];
            $cognome = $_POST["txtCognomeSignupDocente"];

            /* permettere l'inserimento null del numero di telefofono */
            $telefono = $_POST["txtTelefonoSignupDocente"];

            $corso = $_POST["txtCorso"];
            $dipartimento = $_POST["txtDipartimento"];

            /* mai mettere COUNT(*) nelle query di login, restituisce sempre 1 a livello di numero di righe*/
            $sql = "SELECT EMAIL FROM Utente JOIN Docente ON (Utente.EMAIL=Docente.EMAIL_DOCENTE) WHERE (EMAIL = '$email') AND (PSWD = '$password')";
            
            try {
                $result = mysqli_query($conn, $sql);
                $numRows = mysqli_num_rows($result);

                if($numRows > 0) {
                    /* messageBox che evidenzia la presenza dell'utente, magari con un suggerimento dopo errore ripetuto */
                } else {
                    insertDocente($conn, $email, $password, $nome, $cognome, $telefono, $dipartimento, $corso);
                }
            } catch(Exception $e) {
                echo 'Eccezione individuata: '. $e -> getMessage();
            }  
        } 
    }

    function loginUtente($conn, $email){
        $sql = "SELECT EMAIL FROM Utente JOIN Studente ON (EMAIL=EMAIL_STUDENTE) WHERE (EMAIL='$email')";
        $result = mysqli_query($conn, $sql);
        $numRows = mysqli_num_rows($result);

        if($numRows > 0) {
            return "Studente";
        } else {
            return "Docente";
        }
    }

    function insertStudente($conn, $email, $password, $nome, $cognome, $telefono, $annoImmatricolazione, $codice) {
        $storedProcedure = "CALL Registrazione_Studente('$email', '$password', '$nome', '$cognome', '$telefono', '$annoImmatricolazione','$codice')";
        $stmt = mysqli_prepare($conn, $storedProcedure);

        /* a quanto pare, per modificare $stmt non serve un "override", come si vede da riga ... */
        mysqli_stmt_execute($stmt);
    }

    function insertDocente($conn, $email, $password, $nome, $cognome, $telefono, $dipartimento, $corso) {
        $storedProcedure = "CALL Registrazione_Docente('$email', '$password', '$nome', '$cognome', '$telefono', '$corso', '$dipartimento');";
        $stmt = mysqli_prepare($conn, $storedProcedure);

        mysqli_stmt_execute($stmt);
    }
?>
