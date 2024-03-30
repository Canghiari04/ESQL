<?php  
    function buildButtonUndo($namePage) {
        echo '
            <form action="'.$namePage.'" method="POST">
                <button class="button-undo" type="submit" name="btnUndo"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
            </form>
        ';
    }

    function buildButtonPhoto($conn, $namePage) {
        $sql = "SELECT FOTO FROM Test WHERE (Test.TITOLO=:titoloTest);";

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":titoloTest", $_SESSION["titleTest"]);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $row = $result -> fetch(PDO::FETCH_OBJ);
        if(($row -> FOTO) != null) { // controllo attuato per accertarsi della presenza o meno di una foto associata al test
            echo ' 
                <form action="../test/photoTest.php" method="POST">
                    <button class="button-navbar" type="submit" name="btnPhotoTest" value="'.$namePage.'">View photo</button>
                </form> 
            ';
        }
    }

    function buildButtonForm($conn, $email, $titleTest, $stateTest) {
        switch($stateTest) { // switch adottato per rendere la dinamica la visualizzazione dei bottoni associati ai test
            case "APERTO":
                return '
                    <form action="viewAnswer.php" method="POST">
                        <th><button class="table-button" type="submit" name="btnViewAnswer" disabled>Disabled Answers</button></th>
                    </form>
                    <form action="../test/buildTest.php" method="POST">
                        <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$titleTest.'">Restart Test</button></th>
                    </form>
                ';
            break;
            case "INCOMPLETAMENTO":
                return '
                    <form action="viewAnswer.php" method="POST">
                        <th><button class="table-button" type="submit" name="btnViewAnswer" value="'.$titleTest.'">View Answers</button></th>
                    </form>
                    <form action="../test/buildTest.php" method="POST">
                        <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$titleTest.'">Restart Test</button></th>
                    </form>
                ';
            break;
            case "CONCLUSO":
                if(checkNumAnswer($conn, $email, $titleTest)) { // controllo definito solamente qualora lo stato sia CONCLUSO
                    return '
                        <form action="viewAnswer.php" method="POST">
                            <th><button class="table-button" type="submit" name="btnViewAnswer" value="'.$titleTest.'">View Answers</button></th>
                        </form>
                        <form action="../test/buildTest.php" method="POST">
                            <th><button class="table-button" type="submit" name="btnStartTest" disabled>Disabled Test</button></th>
                        </form>
                    ';
                } 
                else {
                    return "
                        <form action='viewSolution.php' method='POST'>
                            <th><button class='table-button' type='submit' name='btnViewSolution' value='$titleTest|?|viewTest.php'>View Solution</button></th>
                        </form>
                        <form action='../test/buildTest.php' method='POST'>
                            <th><button class='table-button' type='submit' name='btnStartTest' disabled>Disabled Test</button></th>
                        </form>
                    ";              
                }
            break;
            default:
                if(checkViewAnswer($conn, $titleTest)) { // controllo attuato per accertarsi che il campo visualizza risposte sia settato a true oppure a false
                    return "
                        <form action='viewSolution.php' method='POST'>
                            <th><button class='table-button' type='submit' name='btnViewSolution' value='$titleTest|?|viewTest.php'>View Solution</button></th>
                        </form>
                        <form action='../test/buildTest.php' method='POST'>
                            <th><button class='table-button' type='submit' name='btnStartTest' disabled>Disabled Test</button></th>
                        </form>
                    ";  
                } 
                else {
                    return '
                        <form action="viewSolution.php" method="POST">
                            <th><button class="table-button" type="submit" name="btnViewSolution" disabled>Disabled Answers</button></th>
                        </form>
                        <form action="../test/buildTest.php" method="POST">
                            <th><button class="table-button" type="submit" name="btnStartTest" value="'.$titleTest.'">Start Test</button></th>
                        </form>
                    ';              
                }
            break;      
        } 
    }

    function buildFormCheck($conn, $idQuestion, $titleTest, $enabled, $solution) {
        if($solution == true) { // diversificazione della query a seconda del valore attribuito al dominio VISUALIZZA_RISPOSTE
            $sql = "SELECT * FROM Opzione_Risposta WHERE (Opzione_Risposta.ID_DOMANDA_CHIUSA=:idQuesito) AND (Opzione_Risposta.TITOLO_TEST=:titoloTest) AND (Opzione_Risposta.SOLUZIONE=1);";
        } else {
            $sql = "SELECT * FROM Opzione_Risposta WHERE (Opzione_Risposta.ID_DOMANDA_CHIUSA=:idQuesito) AND (Opzione_Risposta.TITOLO_TEST=:titoloTest);";
        }
                
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>"; 
        }

        $descriptionQuestion = getQuestionDescription($conn, $idQuestion, $titleTest);

        echo '
            <div class="div-question">
                <label>'.$descriptionQuestion.'</label>
                <div class="div-checkbox">
        ';

        $arrayChecked = checkChecked($conn, $_SESSION["emailStudente"], $idQuestion, $titleTest); // acquisiti tutti gli id delle opzioni di risposta già sottoposte dallo studente
            
        while($row = $result -> fetch(PDO::FETCH_OBJ)) {                    
            if($enabled == true) { // costrutto condizionale ideato per visualizzare o meno le checkbox checkate
                echo '
                    <div>
                        <input type="checkbox" name="checkbox'.$idQuestion.'[]" value="'.$row -> ID.'"'.printChecked($row -> ID, $arrayChecked).'>
                        <label>'.$row -> TESTO.'</label>
                    </div>
                ';
            } elseif($solution == true) {
                echo '
                    <div>
                        <input type="checkbox" name="checkbox'.$idQuestion.'[]" value="'.$row -> ID.'" checked disabled>
                        <label>'.$row -> TESTO.'</label>
                    </div>
                ';
            } else {
                echo '
                    <div>
                        <input type="checkbox" name="checkbox'.$idQuestion.'[]" value="'.$row -> ID.'"'.printChecked($row -> ID, $arrayChecked).' disabled>
                        <label>'.$row -> TESTO.'</label>
                    </div>
                ';
            }
        } 
            
        $arrayNameTable = getNameTable($conn, $idQuestion, $titleTest); // acquisiti tutti i nomi delle tabelle che abbiano la referenza con il test in evidenza

        echo '
                </div>
        ';

        buildTable($conn, $arrayNameTable, null, null); // visualizzazione delle tabelle che compongano il test
        
        echo '
            </div>
        ';
    }

    function printChecked($idSolution, $questionSolutions){
        if(in_array($idSolution, $questionSolutions)){
            return "checked";
        }
        else {
            return " ";
        }
    }

    function buildFormQuery($conn, $idQuestion, $titleTest, $rowResult, $rowSolution, $enabled, $solution) {
        if($solution == true) { // diversificazione della query a seconda del valore attribuito al dominio VISUALIZZA_RISPOSTE
            $sql = "SELECT * FROM Sketch_Codice WHERE (Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito) AND (Sketch_Codice.TITOLO_TEST=:titoloTest) AND (Sketch_Codice.SOLUZIONE=1);";
        } else {
            $sql = "SELECT * FROM Sketch_Codice WHERE (Sketch_Codice.ID_DOMANDA_CODICE=:idQuesito) AND (Sketch_Codice.TITOLO_TEST=:titoloTest);";
        }

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $descriptionQuestion = getQuestionDescription($conn, $idQuestion, $titleTest);

        echo '
            <div class="div-query">
        ';
                
        if($enabled == true) { // costrutto attuato per disabilitare o meno la textarea
            echo '
                <div>
                    <button class="button-query" name="btnCheckSketch" value="'.$idQuestion.'">Check</button>
                    <label class="label-query">'.$descriptionQuestion.'</label>
                    <textarea class="input-solution" type="text" name="txtAnswerSketch'.$idQuestion.'"></textarea>
                </div>
            ';
        } else {
            echo '
                <div>
                    <label class="label-query">'.$descriptionQuestion.'</label>
                    <textarea class="input-solution" type="text" name="txtAnswerSketch'.$idQuestion.'" disabled></textarea>
                </div>
            ';
        }

        if($solution != true) { // controllo ideato per riportare all'interno della textarea la risposta immessa dallo studente oppure la soluzione del quesito
            checkAnswered($conn, $_SESSION["emailStudente"], $idQuestion, $titleTest);
        } else {
            checkSolution($conn, $idQuestion, $titleTest);
        }

        $arrayNameTable = getNameTable($conn, $idQuestion, $titleTest); // acquisiti tutti i nomi delle tabelle che abbiano la referenza con il test in evidenza

        buildTable($conn, $arrayNameTable, $rowSolution, $rowResult); // visualizzate le tabelle di riferimento con i propri dati

        if(isset($rowSolution) && ($_SESSION["checkedQuestion"] == $idQuestion)) { // controllo definito per accertarsi se lo studente abbiano cliccato sull'evento CHECK
            buildResultTables($conn, $rowResult, $rowSolution); // in caso affermativo saranno visualizzate la tabella risposta e la tabella soluzione
        }

        echo '
            </div>      
        ';
    }

    function buildTable($conn, $arrayNameTable, $rowResult, $rowSolution) {
        foreach($arrayNameTable as $nameTable) {
            echo '
                <div class="div-table">
                    <label>'.$nameTable.'</label>
                    <table>
            ';
                    
            $rowsHeaderTable = getHeaderTable($conn, $nameTable); // acquisiti i field della tabella

            foreach($rowsHeaderTable as $row) {
                echo '<th>'.$column = $row["Field"].'</th>';
            }

            $rowsContentTable = getContentTable($conn, $nameTable); // acquisiti i values della tabella

            foreach($rowsContentTable as $row) {
                echo '<tr>';

                foreach($row as $value) {
                    echo '
                        <td>'.$value.'</td>
                    ';
                }
                
                echo '</tr>';
            }

            echo '
                    </table>
                </div>
            ';
        }
    }

    function buildResultTables($conn, $rowResult, $rowSolution) {
        $arrayFieldAnswer = $_SESSION["fieldAnswer"]; // array contenenti i field della query risposta data dallo studente e la query soluzione già presente nel database
        $arrayFieldSolution = $_SESSION["fieldSolution"];

        echo 'ANNO
            <div class="div-table">
        ';

        if($rowResult != $rowSolution) { // diversificazione dello style a seconda della correttezza della risposta data dallo studente
            echo '<div class="div-table-answer-wrong">';
        } else {
            echo '<div class="div-table-answer-correct">';
        }

        echo '
            <label>TABELLA RISPOSTA</label>
            <table>
                <tr>
        ';

        foreach($arrayFieldAnswer as $field) {
            echo '<th>'.$field.'</th>';
        }
    
        echo '</tr>';

        foreach($rowResult as $row) {
            echo '<tr>';

            foreach($row as $value) {
                echo '
                    <td>'.$value.'</td>
                ';
            }
            
            echo '</tr>';
        }

        echo '
                </table>
            </div>
            <div class="div-table-solution">
                <label>TABELLA SOLUZIONE</label>
                <table>
                    <tr>
        ';

        foreach($arrayFieldSolution as $field) {
            echo '<th>'.$field.'</th>';
        }
    
        echo '</tr>';
        
        foreach($rowSolution as $row) {
            echo '<tr>';

            foreach($row as $value) {
                echo '
                    <td>'.$value.'</td>
                ';
            }
            
            echo '</tr>';
        }

        echo '
                    </table>
                </div>
            </div>
        ';
    }

    function buildButtonSolution($conn, $email, $titleTest) { // metodo attuato per abilitare o meno il bottone che consenta di visualizzare le soluzioni del test 
        $sqlState = "SELECT STATO FROM Completamento WHERE (Completamento.EMAIL_STUDENTE=:emailStudente) AND (Completamento.TITOLO_TEST=:titoloTest)";

        try {
            $resultState = $conn -> prepare($sqlState);
            $resultState -> bindValue(":emailStudente", $email);
            $resultState -> bindValue(":titoloTest", $titleTest);
                    
            $resultState -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        $sqlViewAnswer = "SELECT VISUALIZZA_RISPOSTE FROM Test WHERE (Test.TITOLO=:titoloTest);";

        try {
            $resultViewAnswer = $conn -> prepare($sqlViewAnswer);
            $resultViewAnswer -> bindValue(":titoloTest", $titleTest);

            $resultViewAnswer -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        echo '
            <form action="viewSolution.php" method="POST">
        ';
        
        $rowState = $resultState -> fetch(PDO::FETCH_OBJ);
        $rowViewAnswer = $resultViewAnswer -> fetch(PDO::FETCH_OBJ);

        if((($rowState -> STATO) == "CONCLUSO") && (($rowViewAnswer -> VISUALIZZA_RISPOSTE) == 1)) {
            echo "
                <div class='div-button'>
                    <button class='button-solution' type='submit' name='btnViewSolution' value='$titleTest|?|viewAnswer.php'>View Solution</button>
                </div>
            ";
        } else {
            echo ' 
                <div class="div-button">
                    <button class="button-solution" type="submit" name="btnViewSolution" disabled>Disabled Solution</button>
                </div> 
            ';
        }

        echo '
            </form>
        ';
    }
?>