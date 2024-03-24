<?php 
    include "../../connectionDB.php";

    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnDropTest"])) {
            deleteTest($conn, $manager, $_POST["btnDropTest"]);
        } elseif(isset($_POST["btnUpdateTest"])) {
            updateTest($conn, $manager, $_POST["btnUpdateTest"]);
        } elseif(isset($_POST["btnDropQuestion"])){
            deleteQuestion($conn, $manager, $_POST["btnDropQuestion"]);
        }

        header("Location: ../test.php");
        exit; 
    }

    /* metodo in grado di eliminare test appartenenti al database */
    function deleteTest($conn, $manager, $titleTest) {
        $storedProcedure = "CALL Eliminazione_Test(:titolo);";
        
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titolo", $titleTest);

            $stmt -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* scrittura log --> eliminazione di un test dalla collezione Test */
        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione test titolo: '.$titleTest.'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document);
    }

    function updateTest($conn, $manager, $titleTest) {
        $storedProcedure = "CALL Aggiornamento_Test(:titolo);";
        
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titolo", $titleTest);

            $stmt -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* scrittura log --> eliminazione di una tabella appartenente a Tabella_Esercizio */
        $document = ['Tipo log' => 'Aggiornamento', 'Log' => 'Aggiornamento visualizza risposta test titolo: '.$titleTest.'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document);
    }

    function deleteQuestion($conn, $manager, $varQuestion) {
        $valuesQuestion = explode('?', $varQuestion);
        
        $storedProcedure = "CALL Eliminazione_Composizione(:titolo, :idQuesito);";
            
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titolo", $valuesQuestion[0]);              
            $stmt -> bindValue(":idQuesito", $valuesQuestion[1]);

            $stmt -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* scrittura log --> eliminazione di una tabella appartenente a Tabella_Esercizio */
        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione quesito id: '.$valuesQuestion[1].' Referenziato tabella id: '.$valuesQuestion[0].'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document);
    }

    closeConnection($conn);
?>