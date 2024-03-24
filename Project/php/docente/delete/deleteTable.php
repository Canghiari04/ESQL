<?php
    include "../../connectionDB.php";
 
    session_start();
    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnDropTable"])) {
            deleteTable($conn, $_POST["btnDropTable"]);
            deleteTableExercise($conn, $manager, $_POST["btnDropTable"]);
            header("Location: ../table_exercise.php");
            exit();
        } elseif(isset($_POST["btnDeleteRecord"])) {
            $values = $_POST["btnDeleteRecord"];
            $arrayDeleteTokens = explode("|?|", $values);

            /* settato campo della sessione affinchè possa essere compiuto il corretto reindirizzamento alla pagina */
            $_SESSION["recordDeleted"] = "true";
            
            deleteRecord($conn, $arrayDeleteTokens);
            header("Location: ../specifics/specificRow.php");
            exit;
        }
    }

    /* metodo che permette di cancellare la tabella effettiva dal database */
    function deleteTable($conn, $idTable) {
        /* in base all'id della tabella contenuto all'interno della collezione Tabella_Esercizio è acquisito il nome della stessa */
        $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (ID=:idTabella);";
        
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idTabella", $idTable);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);
        $nome = $row -> NOME;

        /* dal nome acquisito mediante la query precedente, è formulato il codice SQL che ne comporti l'eliminazione dal database */
        $sql = "DROP TABLE ".$nome.";";

        try {
            $result = $conn -> prepare($sql);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }

    /* metodo che permette di cancellare i dati della tabella all'interno della collezione Tabella_Esercizio */
    function deleteTableExercise($conn, $manager, $idTable) {
        /* prima dell'eliminazione della tabella, sono rimossi dai test tutti i quesiti che abbiano un riferimento alla stessa */
        deleteAfferent($conn, $manager, $idTable);

        $storedProcedure = "CALL Eliminazione_Tabella_Esercizio(:idTabella);";
                
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":idTabella", $idTable);

            $stmt -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* scrittura log --> eliminazione di una tabella appartenente a Tabella_Esercizio */
        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione tabella id: '.$idTable.'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document);
    }

    /* funzione che permette di cancellare i quesiti referenziati alla tabella */
    function deleteAfferent($conn, $manager, $idTable){
        $sql = "SELECT ID_QUESITO, TITOLO_TEST FROM Afferenza WHERE ID_TABELLA = :idTabella";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idTabella", $idTable);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        if($result -> rowCount() > 0){
            while($row = $result -> fetch(PDO::FETCH_OBJ)){
                $idQuestion = $row -> ID_QUESITO;
                $titleTest = $row -> TITOLO_TEST;

                /* una volta ottenuti i quesisiti interessati, vengono eliminati uno per uno */
                $sql = "DELETE FROM Quesito WHERE ID = :idQuesito AND TITOLO_TEST = :titoloTest";

                try {
                    $resultDelete = $conn -> prepare($sql);
                    $resultDelete -> bindValue(":idQuesito", $idQuestion);
                    $resultDelete -> bindValue(":titoloTest", $titleTest);
        
                    $resultDelete -> execute();
                } catch (PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                /* scrittura log --> eliminazione del quesito referenziato alla tabella */
                $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione quesito id: '.$idQuestion.'. Referenziato tabella id: '.$idTable.'', 'Timestamp' => date('Y-m-d H:i:s')];
                writeLog($manager, $document);
            }
        }
    }

    /* metodo che acquisiti i domini circoscritti formula e attua la query per l'eliminazione di record all'interno delle tabelle effettive */
    function deleteRecord($conn, $arrayDeleteTokens) {
        $nameTable = $arrayDeleteTokens[0];

        /* acquisizione degli attributi che formulano la chiave primaria della tabella */
        $arrayNamePrimaryKey = getNamePrimaryKey($conn, $nameTable);
        
        /* costruzione dinamica della query, adattiva rispetto alle caratteristiche della collezione */
        $sql = "DELETE FROM ".$nameTable." WHERE";

        for($i = 0; $i < sizeof($arrayNamePrimaryKey); $i++) {
            $sql = $sql.'('.$arrayNamePrimaryKey[$i].' = "'.$arrayDeleteTokens[$i+1].'") AND';
        }

        $sql = rtrim($sql, " AND");
        $sql = $sql.";";

        try {
            $result = $conn -> prepare($sql);
    
            $result -> execute();

            /* se e solo se l'eliminazione del record va a buon fine è possibile variare il numero di righe relative alla collezione in questione */
            $stmt = "CALL Eliminazione_Manipolazione_Riga(:idTabella)";

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

    /* funzione restituente l'insieme degli attributi che compongono la chiave primaria della tabella */
    function getNamePrimaryKey($conn, $nameTable) {
        $arrayNamePrimaryKey = array();
        
        $sql = "SHOW KEYS FROM ".$nameTable.";";

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