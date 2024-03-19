<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_option.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_query.css">
    </head>
    <body>
        <?php            
            /* creazione del form del quesito di tipologia Domanda_Chiusa */
            function buildFormCheck($conn, $idQuestion, $titleTest, $enabled, $solution) {
                /* struttura condizionale attuata per distinguere la visualizzazione del form inerente ai quesiti */
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
                    /* abilitate oppure disabilitate le checkbox, a seconda della pagina che abbia precedentemente richiamato il metodo */
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
                buildTable($conn, $arrayNameTable);

                echo '
                    </div>
                ';
            }

            /* creazione del form del quesito di tipologia Domanda_Codice */
            function buildFormQuery($conn, $idQuestion, $titleTest, $enabled, $solution) {
                /* costrutto condizionale attuato per distinguere la visualizzazione delle proprie risposte e delle soluzioni inerenti ai quesiti di codice */
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
                
                /* abilita oppure disabilita la textarea, a seconda della pagina che abbia precedentemente richiamato il metodo */
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
                    /* metodo necessario per riportare le risposte immesse dallo studente in istanti differenti, attuato tramite l'utilizzo di uno script */
                    checkAnswered($conn, $_SESSION["emailStudente"], $idQuestion, $titleTest);
                } else {
                    checkSolution($conn, $idQuestion, $titleTest);
                }

                /* vettore contenente l'insieme dei nomi delle tabelle che abbiano il riferimento al quesito visualizzato */
                $arrayNameTable = getNameTable($conn, $idQuestion, $titleTest);

                /* stampa delle tabelle che siano collegate al quesito mediante la tabella Afferenza */
                buildTable($conn, $arrayNameTable);

                echo '
                    </div>      
                ';
            }

            function buildTable($conn, $arrayNameTable) {
                foreach($arrayNameTable as $nameTable) {
                    echo '
                        <div class="div-table">
                            <table>
                    ';
                    
                    /* sono acquisiti prima i field delle tabelle, associate ad una visualizzazione preventiva */
                    $rowsHeaderTable = getHeaderTable($conn, $nameTable);

                    foreach($rowsHeaderTable as $row) {
                        echo '
                            <th>'.$column = $row["Field"].'</th>
                        ';
                    }

                    /* attuata la visualizzazione dei field, si passa all'acquisizione e visualizzazione successiva dei singoli record che compongano la collezione in questione */
                    $rowsContentTable = getContentTable($conn, $nameTable);

                    foreach($rowsContentTable as $row) {
                        echo '
                            <tr>
                        ';

                        foreach($row as $value) {
                            echo '
                                <td>'.$value.'</td>
                            ';
                        }

                        echo '
                            </tr>
                        ';
                    }

                    echo '
                            </table>
                        </div>
                    ';
                }
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

                $rowState = $resultState -> fetch(PDO::FETCH_OBJ);

                $sqlViewAnswer = "SELECT VISUALIZZA_RISPOSTE FROM Test WHERE (Test.TITOLO=:titoloTest);";

                try {
                    $resultViewAnswer = $conn -> prepare($sqlViewAnswer);
                    $resultViewAnswer -> bindValue(":titoloTest", $titleTest);

                    $resultViewAnswer -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                $rowViewAnswer = $resultViewAnswer -> fetch(PDO::FETCH_OBJ);

                echo '
                    <form action="viewSolution.php" method="POST">
                ';

                if((($rowState -> STATO) == "CONCLUSO") && (($rowViewAnswer -> VISUALIZZA_RISPOSTE) == 1)) {
                    $namePage = "viewAnswer.php";
                    echo ' 
                        <div class="div-button">
                            <button class="button-solution" type="submit" name="btnViewSolution" value="'.$titleTest.'|?|'.$namePage.'">View Solution</button>
                        </div> 
                    ';
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
    </body>
</html>