<?php
    function insertAfferent($conn, $idQuestion, $arrayIdTable) {
        $storedProcedure = 'CALL Inserimento_Afferenza(:idDomanda, :idTabella);';

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(':idDomanda', $idQuestion);
            
            foreach($arrayIdTable as $value) {
                $stmt -> bindValue(':idTabella', $value);
                
                $stmt -> execute();
            }
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
    }
?>