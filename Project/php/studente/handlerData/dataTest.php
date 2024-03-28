<?php   
    function getQuestionTest($conn, $titleTest) { // funzione attuata per acquisire tutti i quesiti che compongono il test
        $arrayIdQuestion = array();

        $sql = "SELECT Quesito.ID FROM Quesito WHERE (Quesito.TITOLO_TEST=:titoloTest);";       

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>"; 
        }

        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
            array_push($arrayIdQuestion, $row -> ID);
        }

        return $arrayIdQuestion;
    }

    function getAnswerTest($conn, $email, $titleTest) { // funzione ideata per estrapolare tutte le risposte date dallo studente ai quesiti dei test
        $sql = "SELECT * FROM Risposta WHERE (Risposta.EMAIL_STUDENTE=:emailStudente) AND (Risposta.TITOLO_TEST=:titoloTest);";
                    
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":emailStudente", $email);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        return $result;
    }

    function getTypeQuestion($conn, $idQuestion, $titleTest) {
        $sql = "SELECT * FROM Domanda_Chiusa WHERE (Domanda_Chiusa.ID_DOMANDA_CHIUSA=:idQuesito) AND (Domanda_Chiusa.TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $numRows = $result -> rowCount();
        if($numRows > 0) {
            return "CHIUSA";
        } else {
            return "CODICE";
        }
    }

    function getQuestionDescription($conn, $idQuestion, $titleTest) { // estrapolata la descrizione della domanda, successivamente visualizzata all'interno delle textarea
        $sql = "SELECT DESCRIZIONE FROM Quesito WHERE (ID=:idQuesito) AND (TITOLO_TEST=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(':idQuesito', $idQuestion);
            $result -> bindValue(':titoloTest', $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>'; 
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);
        return $row -> DESCRIZIONE;
    }

    function getIdTable($conn, $nameTable) {
        $sql = "SELECT ID FROM Tabella_Esercizio WHERE (Tabella_Esercizio.NOME=:nomeTabella);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":nomeTabella", $nameTable);

            $result -> execute();
        } catch(PDOException $e) {
            echo 'Eccezione '.$e -> getMessage().'<br>'; 
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);
        return $row -> ID;
    }

    function getNameTable($conn, $idQuestion, $titleTest) { // funzione attuata per risalire a tutte le tabelle che abbiano un'associazione con il quesito circoscritto
        $arrayNameTable = array();

        $sql = "SELECT ID_TABELLA FROM Afferenza WHERE (ID_QUESITO=:idQuesito) AND (TITOLO_TEST=:titoloTest);"; 

        try {
            $resultIdTable = $conn -> prepare($sql);
            $resultIdTable -> bindValue(":idQuesito", $idQuestion);
            $resultIdTable -> bindValue(":titoloTest", $titleTest);            
            
            $resultIdTable -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        } 

        while($row = $resultIdTable -> fetch(PDO::FETCH_OBJ)) {
            $idTable = $row -> ID_TABELLA;

            $sql = "SELECT NOME FROM Tabella_Esercizio WHERE (ID=:idTabella);";

            try {
                $resultNameTable = $conn -> prepare($sql);
                $resultNameTable -> bindValue(":idTabella", $idTable);

                $resultNameTable -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            $row = $resultNameTable -> fetch(PDO::FETCH_OBJ);
            array_push($arrayNameTable, $row -> NOME);
        }
        
        return $arrayNameTable;
    }

    function getHeaderTable($conn, $nameTable) {
        $sql = "SHOW COLUMNS FROM ".$nameTable.";"; // query definita per estrapolare i nomi dei domini che compongono la tabella

        try {
            $result = $conn -> prepare($sql);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $rows = $result -> fetchAll(PDO::FETCH_ASSOC); 
        return $rows;
    }

    function getContentTable($conn, $nameTable) { // funzione attuata per estrapolare tutti i record che contraddistinguono la collezione data in input
        $sql = "SELECT * FROM ".$nameTable.";";

        try {
            $result = $conn -> prepare($sql);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $rows = $result -> fetchAll(PDO::FETCH_ASSOC); 
        return $rows;
    }

    function getFieldName($resultAnswer, $resultSolution) { // funzione definita per acquisire i field che compongono gli oggetti stdClass, affinchè siano manipolabili 
        $fieldNameAnswer = array(); // array contenenti i nomi dei field che compongono la query immessa dallo studente e la query soluzione appartenente al database
        $fieldNameResult = array();

        for ($i = 0; $i < ($resultAnswer -> columnCount()); $i++) {
            $field = $resultAnswer -> getColumnMeta($i);
            array_push($fieldNameAnswer, $field["name"]);
        }

        for ($i = 0; $i < ($resultSolution -> columnCount()); $i++) {
            $field = $resultSolution -> getColumnMeta($i);
            array_push($fieldNameResult, $field["name"]);
        }

        return [$fieldNameAnswer, $fieldNameResult];
    }
?>