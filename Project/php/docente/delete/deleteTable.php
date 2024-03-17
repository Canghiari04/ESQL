<?php
    include "../../connectionDB.php";
 
    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnDropTable"])) {
            deleteTable($conn, $idTable = $_POST["btnDropTable"]);
            deleteTableExercise($conn, $idTable = $_POST["btnDropTable"]);

            /* scrittura log eliminazione di un record appartenente a Tabella_Esercizio */
            $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione tabella id: '.$idTable.'', 'Timestamp' => date('Y-m-d H:i:s')];
            writeLog($manager, $document);

            header("Location: ../table_exercise.php");
            exit;
        } 
    }

    /* funzione che permette di cancellare la tabella dal database partendo dall'id all'interno di Tabella_Esercizio */
    function deleteTable($conn, $idTable) {
        $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (ID=:idTabella);";
        
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idTabella", $idTable);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $row = $result -> fetch(PDO::FETCH_ASSOC);
        $nome = $row["NOME"];

        $sql = "DROP TABLE ".$nome.";";

        try {
            $result = $conn -> prepare($sql);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }

     /* funzione che permette di cancellare i quesiti che riguardano la tabella eliminata */
    function checkAfferent($conn, $idTable){
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

                /* una volta ottenuti i quesisiti interessati, vengono eliminati uno alla volta */
                $sql = "DELETE FROM Quesito WHERE ID = :idQuesito AND TITOLO_TEST = :titoloTest";

                try {
                    $resultDelete = $conn -> prepare($sql);
                    $resultDelete -> bindValue(":idQuesito", $idQuestion);
                    $resultDelete -> bindValue(":titoloTest", $titleTest);
        
                    $resultDelete -> execute();
                } catch (PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
            }
        }

    }

    /* funzione che permette di cancellare i dati della tabella all'interno della collezione Tabella_Esercizio */
    function deleteTableExercise($conn, $idTable) {
        checkAfferent($conn, $idTable);
        $storedProcedure = "CALL Eliminazione_Tabella_Esercizio(:idTabella);";
            
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":idTabella", $idTable);

            $stmt -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }

    closeConnection($conn);
?>