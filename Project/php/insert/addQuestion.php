<?php
    function checkTable($conn, $email) {
        $sql = 'SELECT * FROM Tabella_Esercizio WHERE (EMAIL_DOCENTE=:email);';

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(':email', $email);
            
            $result -> execute();
            $numRows = $result -> rowCount();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        return ($numRows > 0);
    }

    function insertQuestion($conn, $type, $difficulty, $numAnswers, $description) {
        $storedProcedure = 'CALL Inserimento_Quesito(:difficolta, :numRisposte, :descrizione)';

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(':difficolta', $difficulty);
            $stmt -> bindValue(':numRisposte', $numAnswers);
            $stmt -> bindValue(':descrizione', $description);
            
            $stmt -> execute();
        } catch (PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        /* query che permette di risalire all'ID dell'ultimo record inserito */
        $sql = 'SELECT LAST_INSERT_ID() AS ID';

        try {
            $result = $conn -> prepare($sql);
            $result -> execute();
            
            $row = $result -> fetch(PDO::FETCH_ASSOC);
            $id = $row['ID'];
        } catch (PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }

        /* viene salvato l'id dell'ultima domanda inserita, dato che conseguentemente dovranno essere inseriti riferimenti delle risposte e delle tabelle */
        $_SESSION['idCurrentQuestion'] = $id;
        addQuestion($conn, strtoupper($type), $id);
    }

    /* funzione utilizzata per smistare l'inserimento della domanda a seconda della tipologia */
    function addQuestion($conn, $type, $id) {
        if($type == 'CHIUSA') {
            $storedProcedure = 'CALL Inserimento_Domanda_Chiusa(:id);';
        } elseif($type == 'CODICE') {
            $storedProcedure = 'CALL Inserimento_Domanda_Codice(:id);';
        }

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(':id', $id);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
    }
?>