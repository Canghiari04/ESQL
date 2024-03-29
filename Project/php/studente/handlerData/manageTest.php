<?php
    function openTest($conn, $manager, $emailStudent, $titleTest) { // inserimento di un tentativo condotto dallo studente all'interno della tabella Completamento 
        $storedProcedure = "CALL Inserimento_Completamento(:titoloTest, :email);";

        try {
            $stmt = $conn -> prepare($storedProcedure);
            $stmt -> bindValue(":titoloTest", $titleTest);
            $stmt -> bindValue(":email", $emailStudent);

            $stmt -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $document = ['Tipo log' => 'Inserimento', 'Log' => 'Inserimento di completamento del test: '.$titleTest.' dallo studente: '.$emailStudent.'', 'Timestamp' => date('Y-m-d H:i:s')];
        writeLog($manager, $document);
    }
?>