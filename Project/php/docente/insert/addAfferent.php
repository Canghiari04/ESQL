<?php
    function insertAfferent($conn, $manager, $idQuestion, $titleTest, $arrayIdTable) { // inserimento del quesito, test e tabella esercizio all'interno della collezione Afferenza
        $storedProcedure = "CALL Inserimento_Afferenza(:idDomanda, :titoloTest, :idTabella);";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":idDomanda", $idQuestion);
            $stmt -> bindValue(":titoloTest", $titleTest);
            
            foreach($arrayIdTable as $value) {
                $stmt -> bindValue(":idTabella", $value);
                
                $stmt -> execute();
                $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento afferenza tra il quesito id: '.$idQuestion.' e la Tabella_Esercizio id : '.$value.'', 'Timestamp' => date('Y-m-d H:i:s')];
                writeLog($manager, $document);
            }
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        header("Location: ../question.php");
        exit();
    }
?>