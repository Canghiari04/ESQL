<?php
    include "../../connectionDB.php";
 
    session_start();
    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnDropTable"])) {
            deleteTable($conn, $manager, $_POST["btnDropTable"]);
            header("Location: ../table_exercise.php");
            exit();
        } elseif(isset($_POST["btnDeleteRecord"])) {
            $values = $_POST["btnDeleteRecord"];
            $arrayDeleteTokens = explode("|?|", $values); // acquisiti i token necessari per la cancellazione del record

            $_SESSION["recordDeleted"] = "true"; // inizializzazione del campo della sessione per corretto reindirizzamento alla pagina chiamante
            
            deleteRecord($conn, $arrayDeleteTokens);
            header("Location: ../specifics/specificRow.php");
            exit;
        }
    }

    function deleteTable($conn, $manager, $idTable) {
        $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (ID=:idTabella);";
        
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idTabella", $idTable);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);
        $nome = $row -> NOME; // acquisito il nome della tabella per eliminazione effettiva dal database
        
        $sql = "DROP TABLE ".$nome.";";
        
        try {
            $result = $conn -> prepare($sql);
            
            $result -> execute();
            deleteTableExercise($conn, $manager, $idTable); // eliminazione dalla collezione Tabella_Esercizio solamente se non violati vincoli di integrità
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }


    }

    function deleteTableExercise($conn, $manager, $idTable) {
        deleteAfferent($conn, $manager, $idTable); // eliminazione di tutti i quesiti referenziati alla tabella circoscritta

        $storedProcedure = "CALL Eliminazione_Tabella_Esercizio(:idTabella);";
                
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":idTabella", $idTable);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione tabella id: '.$idTable.'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document); // scrittura log eliminazione di una tabella 
    }

    function deleteAfferent($conn, $manager, $idTable) {
        $sql = "SELECT ID_QUESITO, TITOLO_TEST FROM Afferenza WHERE ID_TABELLA = :idTabella";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idTabella", $idTable);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        if($result -> rowCount() > 0) {
            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                $idQuestion = $row -> ID_QUESITO;
                $titleTest = $row -> TITOLO_TEST;

                $sql = "DELETE FROM Quesito WHERE (Quesito.ID=:idQuesito) AND (Quesito.TITOLO_TEST=:titoloTest)"; // acquisiti i parametri è attuata l'eliminazione del quesito dal database

                try {
                    $resultQuestion = $conn -> prepare($sql);
                    $resultQuestion -> bindValue(":idQuesito", $idQuestion);
                    $resultQuestion -> bindValue(":titoloTest", $titleTest);
        
                    $resultQuestion -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione quesito id: '.$idQuestion.'. Referenziato tabella id: '.$idTable.'', 'Timestamp' => date('Y-m-d H:i:s')];
                writeLog($manager, $document); // scrittura log eliminazione di un quesito referenziato ad una tabella
            }
        }
    }

    function deleteRecord($conn, $arrayDeleteTokens) {
        $nameTable = $arrayDeleteTokens[0];
        $arrayNamePrimaryKey = getNamePrimaryKey($conn, $nameTable); // acquisiti i field che compongono la chiave primaria della tabella
        
        $sql = "DELETE FROM ".$nameTable." WHERE"; 

        for($i = 0; $i < sizeof($arrayNamePrimaryKey); $i++) {
            $sql = $sql.'('.$arrayNamePrimaryKey[$i].' = "'.$arrayDeleteTokens[$i+1].'") AND';
        }

        $sql = rtrim($sql, " AND");
        $sql = $sql.";"; // costruzione dinamica della query, adattiva a seconda delle caratteristiche della tabella 

        try {
            $result = $conn -> prepare($sql);
    
            $result -> execute();

            $stmt = "CALL Eliminazione_Manipolazione_Riga(:idTabella)"; // modifica del numero di righe della tabella solamente se l'eliminazione del record dovesse andare a buon fine 

            try {
                $resultProcedure = $conn -> prepare($stmt);
                $resultProcedure -> bindValue(":idTabella", $_SESSION["idCurrentTable"]);
        
                $resultProcedure -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }

    function getNamePrimaryKey($conn, $nameTable) {
        $arrayNamePrimaryKey = array();
        
        $sql = "SHOW KEYS FROM ".$nameTable.";"; // acquisiti i nomi degli attributi che compongono la chiave primaria della tabella

        try {
            $result = $conn -> prepare($sql);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()." <br>";
        }

        $numRows = $result -> rowCount();
        if($numRows > 0) {    
            while($row = $result -> fetch(PDO::FETCH_ASSOC)) {
                array_push($arrayNamePrimaryKey, $row["Column_name"]);
            } 
        }

        return $arrayNamePrimaryKey; 
    }

    closeConnection($conn);
?>