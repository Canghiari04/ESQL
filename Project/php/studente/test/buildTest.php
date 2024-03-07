<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_option.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_query.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../view/viewTest.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
            <form action="../evaluate/evaluateTest.php" method="POST">
                <div class="container">
                    <?php
                        include "dataTest.php";      
                        include "manageTest.php";      
                        include "../../connectionDB.php";
                        
                        session_start();
                        $conn = openConnection();  

                        if ($_SERVER["REQUEST_METHOD"] == "POST") {   
                            /* avviene la diversificazione a seconda dello stato di completamento del test; da primo bottone visualizzato sarà creato il test in assenza di risposte, data la loro mancanza */
                            if(isset($_POST["btnStartTest"])) {
                                $titleTest = $_POST["btnStartTest"];
                                $_SESSION["titleTestTested"] = $titleTest; 

                                /* funzione necessaria per variare lo stato del test qualora lo studente decidesse di rispondere ai quesiti che lo compongono */
                                openTest($conn, $_SESSION["emailStudente"], $titleTest);
                                buildForm($conn, $titleTest);                        
                            }
                            /* il ramo dell'else è inerente alla creazione del test che riporti le risposte già inserite dallo studente */
                            elseif(isset($_POST["btnRestartTest"])){
                                $titleTest = $_POST["btnRestartTest"];
                                $_SESSION["titleTestTested"] = $titleTest; 
                                
                                buildForm($conn, $titleTest);
                            } elseif($_POST["btnCheckSketch"]) {
                                $values = $_POST["btnCheckSketch"];
                                $tokens = explode("|?|", $values);

                                $var = "txtAnswerSketch";
                                $var = $var."".$tokens[0];
                                $outcome = checkQuery($conn, $tokens[0], $tokens[1], $_POST[$var]);
                                
                                buildForm($conn, $tokens[1]);   
                                
                                if($outcome == true) {
                                    echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch" . $tokens[0] . "\"]').value='QUERY CORRETTA';</script>";
                                } else { 
                                    echo "<script>document.querySelector('textarea[name=\"txtAnswerSketch" . $tokens[0] . "\"]').value='QUERY ERRATA';</script>";
                                }
                            }
                        }

                        function buildForm($conn, $titleTest) {
                            /* funzione restituente l'insieme degli id dei quesiti che compongono il test selezionato */
                            $result = getQuestionTest($conn, $titleTest);
            
                            if(isset($result)) {
                                while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                                    /* condizione necessaria per differenziare la visualizzazione dei quesiti */
                                    if(getTypeQuestion($conn, $row -> ID, $titleTest) == "CHIUSA"){
                                        buildFormCheck($conn, $row -> ID, $titleTest);
                                    } else {
                                        buildFormQuery($conn, $row -> ID, $titleTest);
                                    }
                                }
                            }
                        }

                        /* creazione del form del quesito di tipologia Domanda_Chiusa */
                        function buildFormCheck($conn, $idQuestion, $titleTest) {
                            $sql = "SELECT * FROM Opzione_Risposta WHERE (ID_DOMANDA_CHIUSA=:idQuesito) AND (TITOLO_TEST=:titoloTest);";
                            
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
                    
                            $arrayChecked = checkChecked($conn, $_SESSION["emailStudente"], $idQuestion, $titleTest);
                        
                            while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                                $var = printChecked($row -> ID, $arrayChecked);
                    
                                echo '
                                    <div class="div-checkbox">
                                        <input type="checkbox" name="checkbox'.$idQuestion.'[]" value="'.$row -> ID.'" '.$var.'>
                                        <label>'.$row -> TESTO.'</label>
                                    </div>
                                ';
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
                        function buildFormQuery($conn, $idQuestion, $titleTest) {
                            $sql = "SELECT * FROM Sketch_Codice WHERE (ID_DOMANDA_CODICE=:idQuesito) AND (TITOLO_TEST=:titoloTest);";
                        
                            try {
                                $result = $conn -> prepare($sql);
                                $result -> bindValue(":idQuesito", $idQuestion);
                                $result -> bindValue(":titoloTest", $titleTest);
                    
                                $result -> execute();
                            } catch(PDOException $e) {
                                echo "Eccezione ".$e -> getMessage()."<br>";
                            }
                    
                            $nameQuestion = getQuestionDescription($conn, $idQuestion, $titleTest);
                    
                            /* vettore contenente l'insieme dei nomi delle tabelle che abbiano il riferimento al quesito visualizzato */
                            $arrayNameTable = getNameTable($conn, $idQuestion, $titleTest);
                    
                            echo '
                                <div class="div-query">
                                    <button class="button-query" name="btnCheckSketch" value="'.$idQuestion.'">Check</button>
                                    <div>
                                        <label class="label-query">'.$nameQuestion.'</label>
                            ';
                    
                            echo '
                                        <textarea class="input-solution" type="text" name="txtAnswerSketch'.$idQuestion.'"></textarea>
                                    </div>
                            ';
                    
                            /* metodo necessario per riportare le risposte immesse dallo studente in istanti differenti, attuato tramite l'utilizzo di uno script */
                            checkAnswered($conn, $_SESSION["emailStudente"], $idQuestion, $titleTest);

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

                        closeConnection($conn);
                    ?>
                </div>
                <div class="div-button">
                    <button class="button-final" type="submit" name="btnSaveExit">Exit</button>
                    <button class="button-final" type="submit" name="btnSendTest">Send</button>
                </div>
        </form>
    </body>
</html>