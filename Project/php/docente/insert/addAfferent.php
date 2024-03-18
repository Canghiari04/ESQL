<?php
    function insertAfferent($conn, $idQuestion, $titleTest, $arrayIdTable) {
        $storedProcedure = "CALL Inserimento_Afferenza(:idDomanda, :titoloTest, :idTabella);";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":idDomanda", $idQuestion);
            $stmt -> bindValue(":titoloTest", $titleTest);
            
            foreach($arrayIdTable as $value) {
                $stmt -> bindValue(":idTabella", $value);
                
                $stmt -> execute();
            }
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        header("Location: ../question.php");
        exit();
    }
?>