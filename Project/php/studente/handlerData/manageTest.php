<?php
    /* inserimento di un tentativo di risoluzione di un test da parte di uno studente, con relativo cambiamento di stato ad "APERTO" per la tabella Completamento */
    function openTest($conn, $emailStudent, $titleTest){
        $storedProcedure = "CALL Inserimento_Completamento(:titoloTest, :email);";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titoloTest", $titleTest);
            $stmt -> bindValue(":email", $emailStudent);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }
    }
?>