<?php
    function insertComposition($conn, $titleTest, $arrayIdQuestion) {
        $storedProcedure = 'CALL Inserimento_Composizione(:titoloTest, :idQuesito);';

        echo $titleTest;

        foreach($arrayIdQuestion as $s) {
            echo $s;
        }

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(':titoloTest', $titleTest);
            
            foreach($arrayIdQuestion as $value) {
                $stmt -> bindValue(':idQuesito', $value);
                
                $stmt -> execute();
            }

            header('Location: ../test.php');
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>';
        }
    }
?>