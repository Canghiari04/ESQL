<?php 
    include "../../connectionDB.php";

    $conn = openConnection();
    $manager = openConnectionMongoDB();

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        if(isset($_POST["btnDropTest"])) {
            deleteTest($conn, $manager, $_POST["btnDropTest"]);
        } elseif(isset($_POST["btnUpdateTest"])) {
            updateTest($conn, $manager, $_POST["btnUpdateTest"]);
        } elseif(isset($_POST["btnDropQuestion"])){
            deleteQuestion($conn, $manager, $_POST["btnDropQuestion"]);
        }

        header("Location: ../test.php");
        exit(); 
    }

    function deleteTest($conn, $manager, $titleTest) {
        $storedProcedure = "CALL Eliminazione_Test(:titolo);";
        
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titolo", $titleTest);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione Test titolo: '.$titleTest.'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document); // scrittura log eliminazione di un test
    }

    function updateTest($conn, $manager, $titleTest) {
        $storedProcedure = "CALL Aggiornamento_Test(:titolo);";
        
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titolo", $titleTest);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $document = ['Tipo log' => 'Aggiornamento', 'Log' => 'Aggiornamento visualizza risposta del Test titolo: '.$titleTest.'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document); // scrittura log aggiornamento dello stato di un test
    }

    function deleteQuestion($conn, $manager, $varQuestion) {
        $valuesQuestion = explode("|?|", $varQuestion); // acquisiti i token necessari per l'eliminazione di un quesito dal test
        
        $storedProcedure = "CALL Eliminazione_Composizione(:titolo, :idQuesito);";
            
        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titolo", $valuesQuestion[0]);              
            $stmt -> bindValue(":idQuesito", $valuesQuestion[1]);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $document = ['Tipo log' => 'Cancellazione', 'Log' => 'Cancellazione Quesito id: '.$valuesQuestion[1].' Referenziato Tabella_Esercizio id: '.$valuesQuestion[0].'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document); // scrittura log eliminazione di un quesito referenziato ad un test
    }

    closeConnection($conn);
?>