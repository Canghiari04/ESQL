<?php
    function insertComposition($conn, $titleTest, $arrayIdQuestion) {
        $storedProcedure = 'CALL Inserimento_Composizione(:titoloTest, :idQuesito);';

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(':titoloTest', $titleTest);
            
            foreach($arrayIdQuestion as $value) {
                $stmt -> bindValue(':idQuesito', $value);
                
                $stmt -> execute();
            }
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
    }
?>