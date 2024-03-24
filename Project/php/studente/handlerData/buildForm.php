<?php          
    /* metodo utilizzato per rendere dinamica la stampa dei bottoni, a seconda dello stato del test e dai quesiti che lo compongono */
    function buildButtonForm($conn, $email, $titleTest, $stateTest) {
        /* rispetto allo stato del test, circoscritto al tentativo sostenuto dallo studente, si differenziano le funzionalità che possono susseguirsi, abilitando o meno il bottone di riferimento */
        switch($stateTest) {
            case "APERTO":
                return '
                    <form action="viewAnswer.php" method="POST">
                        <th><button class="table-button" type="submit" name="btnViewRisposte" disabled>Disabled Answers</button></th>
                    </form>
                    <form action="../test/buildTest.php" method="POST">
                        <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$titleTest.'">Restart Test</button></th>
                    </form>
                ';
            break;
            case "INCOMPLETAMENTO":
                return '
                    <form action="viewAnswer.php" method="POST">
                        <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$titleTest.'">View Answers</button></th>
                    </form>
                    <form action="../test/buildTest.php" method="POST">
                        <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$titleTest.'">Restart Test</button></th>
                    </form>
                ';
            break;
            case "CONCLUSO":
                if(checkNumAnswer($conn, $email, $titleTest)) {
                    return '
                        <form action="viewAnswer.php" method="POST">
                            <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$titleTest.'">View Answers</button></th>
                        </form>
                        <form action="../test/buildTest.php" method="POST">
                            <th><button class="table-button" type="submit" name="btnStartTest" disabled>Disabled Test</button></th>
                        </form>
                    ';
                } 
                /* altrimenti saranno esclusivamente visualizzate le soluzioni ai quesiti del test */
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
                /* controllo in cui si evidenzia se il campo VISUALIZZA_RISPOSTE sia settato a false oppure a true */
                if(checkViewAnswer($conn, $titleTest)) {
                    return "
                        <form action='viewSolution.php' method='POST'>
                            <th><button class='table-button' type='submit' name='btnViewSolution' value='$titleTest|?|viewTest.php'>View Solution</button></th>
                        </form>
                        <form action='../test/buildTest.php' method='POST'>
                            <th><button class='table-button' type='submit' name='btnStartTest' disabled>Disabled Test</button></th>
                        </form>
                    ";  
                } 
                /* altrimenti saranno esclusivamente visualizzate le soluzioni ai quesiti del test */
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

    /* creazione del form del quesito di tipologia Domanda_Chiusa */
    function buildFormCheck($conn, $idQuestion, $titleTest, $enabled, $solution) {
        /* struttura condizionale attuata per diversificare la visualizzazione delle soluzioni del test piuttosto che dei soli quesiti */
        if($solution == true) {
            $sql = "SELECT * FROM Opzione_Risposta WHERE (ID_DOMANDA_CHIUSA=:idQuesito) AND (TITOLO_TEST=:titoloTest) AND (SOLUZIONE=1);";
        } else {
            $sql = "SELECT * FROM Opzione_Risposta WHERE (ID_DOMANDA_CHIUSA=:idQuesito) AND (TITOLO_TEST=:titoloTest);";
        }
                
        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch (PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>"; 
        }

        /* acquisizione della domanda del quesito, successivamente visualizzata all'interno del form di riferimento */
        $descriptionQuestion = getQuestionDescription($conn, $idQuestion, $titleTest);

        echo '
            <div class="div-question">
                <label>'.$descriptionQuestion.'</label>
        ';

        /* vettore contente gli id delle opzioni di risposta dati da tentativi precedenti sostenuti dallo studente */
        $arrayChecked = checkChecked($conn, $_SESSION["emailStudente"], $idQuestion, $titleTest);
            
        while($row = $result -> fetch(PDO::FETCH_OBJ)) {                    
            /* abilitate oppure disabilitate le checkbox, a seconda della pagina che abbia richiamato il metodo, nel caso di visualizzazione delle risposte sarà pari a false contrariamente per tentativi di svolgimento del test sarà impostato a true */
            if($enabled == true) {
                echo '
                    <div class="div-checkbox">
                        <input type="checkbox" name="checkbox'.$idQuestion.'[]" value="'.$row -> ID.'"'.printChecked($row -> ID, $arrayChecked).'>
                        <label>'.$row -> TESTO.'</label>
                    </div>
                ';
            } elseif($solution == true) {
                echo '
                    <div class="div-checkbox">
                        <input type="checkbox" name="checkbox'.$idQuestion.'[]" value="'.$row -> ID.'" checked disabled>
                        <label>'.$row -> TESTO.'</label>
                    </div>
                ';
            } else {
                echo '
                    <div class="div-checkbox">
                        <input type="checkbox" name="checkbox'.$idQuestion.'[]" value="'.$row -> ID.'"'.printChecked($row -> ID, $arrayChecked).' disabled>
                        <label>'.$row -> TESTO.'</label>
                    </div>
                ';
            }
        } 
            
        /* vettore contenente l'insieme dei nomi delle tabelle che abbiano il riferimento al quesito visualizzato */
        $arrayNameTable = getNameTable($conn, $idQuestion, $titleTest);

        /* stampa delle tabelle che siano collegate al quesito mediante la tabella Afferenza */
        buildTable($conn, $arrayNameTable, null, null);

        echo '
            </div>
        ';
    }

    /* stampa della checkbox demarcata o meno */
    function printChecked($idSolution, $questionSolutions){
        if(in_array($idSolution, $questionSolutions)){
            return "checked";
        }
        else {
            return " ";
        }
    }

    /* creazione del form del quesito di tipologia Domanda_Codice */
    function buildFormQuery($conn, $idQuestion, $titleTest, $rowResult, $rowSolution, $enabled, $solution) {
        /* costrutto condizionale attuato per diversificare la visualizzazione delle soluzioni piuttosto che delle risposte date dallo studente */
        if($solution == true) {
            $sql = "SELECT * FROM Sketch_Codice WHERE (ID_DOMANDA_CODICE=:idQuesito) AND (TITOLO_TEST=:titoloTest) AND (SOLUZIONE=1);";
        } else {
            $sql = "SELECT * FROM Sketch_Codice WHERE (ID_DOMANDA_CODICE=:idQuesito) AND (TITOLO_TEST=:titoloTest);";
        }

        try {
            $result = $conn -> prepare($sql);
            $result -> bindValue(":idQuesito", $idQuestion);
            $result -> bindValue(":titoloTest", $titleTest);

            $result -> execute();
        } catch(PDOException $e) {
            echo "Eccezione ".$e -> getMessage()."<br>";
        }

        /* acquisizione della domanda del quesito, successivamente visualizzata all'interno del form di riferimento */
        $descriptionQuestion = getQuestionDescription($conn, $idQuestion, $titleTest);

        echo '
            <div class="div-query">
        ';
                
        /* abilita oppure disabilita la textarea, a seconda della pagina che abbia richiamato il metodo, nel caso di visualizzazione delle risposte sarà pari a false contrariamente per tentativi di svolgimento del test sarà impostato a true */
        if($enabled == true) {
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

        if($solution != true) {
            /* metodo necessario per riportare le risposte immesse dallo studente in istanti differenti, attuato tramite l'utilizzo di uno script all'interno della textarea */
            checkAnswered($conn, $_SESSION["emailStudente"], $idQuestion, $titleTest);
        } else {
            checkSolution($conn, $idQuestion, $titleTest);
        }

        /* vettore contenente l'insieme dei nomi delle tabelle che abbiano il riferimento al quesito visualizzato */
        $arrayNameTable = getNameTable($conn, $idQuestion, $titleTest);

        /* stampa delle tabelle che siano collegate al quesito mediante la tabella Afferenza */
        buildTable($conn, $arrayNameTable, $rowSolution, $rowResult);

        /* controllo relativo dell'evento check e della uguaglianza tra i due quesiti, per permetterne o meno la visualizzazione */
        if(isset($rowSolution) && ($_SESSION["checkedQuestion"] == $idQuestion)) {
            buildResultTables($conn, $rowResult, $rowSolution);
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
                    
            /* sono acquisiti prima i field delle tabelle, associate ad una visualizzazione preventiva */
            $rowsHeaderTable = getHeaderTable($conn, $nameTable);

            foreach($rowsHeaderTable as $row) {
                echo '<th>'.$column = $row["Field"].'</th>';
            }

            /* attuata la visualizzazione dei field, si passa all'acquisizione e visualizzazione successiva dei singoli record che compongano la collezione in questione */
            $rowsContentTable = getContentTable($conn, $nameTable);

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
        $arrayFieldAnswer = $_SESSION["fieldAnswer"];
        $arrayFieldSolution = $_SESSION["fieldSolution"];

        echo '
            <div class="div-table">
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
        ';
    }

    /* in fase di visualizzazione delle proprie risposte, sarà abilitato oppure disabilitato il bottone che renderà visibili le soluzioni dei quesiti del test */
    function buildButtonSolution($conn, $email, $titleTest) {
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

        /* tramite il controllo dello stato e del campo Visualizza_Risposte lo studente avrà o meno la possibilità di visualizzare le soluzioni */
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

    function buildButtonUndo($namePage) {
        echo '
            <form action="'.$namePage.'" method="POST">
                <button class="button-undo" type="submit" name="btnUndo"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></button>
            </form>
        ';
    }
?>