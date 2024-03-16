<?php
    session_start();

    if(!isset($_SESSION["emailStudente"])) {
        header("Location: ../../shared/login/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar_button_undo.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
        </div>
            <form action="" method="POST">
                <div class="container">
                    <?php
                        include "evaluateTest.php";
                        include "../handlerData/buildForm.php";      
                        include "../handlerData/dataTest.php";      
                        include "../handlerData/manageTest.php";
                        include "../../connectionDB.php";
                        
                        $conn = openConnection();  

                        if ($_SERVER["REQUEST_METHOD"] == "POST") {   
                            if(isset($_POST["btnStartTest"])) {
                                $titleTest = $_POST["btnStartTest"];
                                $_SESSION["titleTestTested"] = $titleTest; 

                                /* metodo che richiama la stored procedure per l'inserimento dello studente all'interno della tabella Completamento, in cui verrà impostato lo stato di avanzamento ad APERTO  */
                                openTest($conn, $_SESSION["emailStudente"], $titleTest);

                                /* innescata la costruzione del form contenente tutti i quesiti del test selezionato */
                                buildForm($conn, $titleTest);                        
                            } elseif(isset($_POST["btnRestartTest"])){
                                $titleTest = $_POST["btnRestartTest"];
                                $_SESSION["titleTestTested"] = $titleTest; 
                                
                                buildForm($conn, $titleTest);
                            } elseif(isset($_POST["btnSendExitTest"])) {
                                /* vettore contenente l'insieme dei numeri progressivi dei quesiti che compongano il test designato */
                                $arrayIdQuestion = getQuestionTest($conn, $_SESSION["titleTestTested"]);
                                                
                                /* metodi restituenti di mappe, le quali contengono rispettivamente le soluzioni dei quesiti e le risposte dello studente ai quesiti */
                                $mapArrayAnswer = setValueSentMap($arrayIdQuestion);
                                $mapArraySolution = setValueSolutionMap($conn, $arrayIdQuestion, $_SESSION["titleTestTested"]); 
                                                
                                /* correzione delle risposte date dallo studente */
                                checkAnswer($conn, $arrayIdQuestion, $mapArrayAnswer, $mapArraySolution);
                                                
                                header("Location: ../view/viewTest.php");
                                exit();
                            } elseif(isset($_POST["btnCheckSketch"])) {
                                /* ramo del costrutto condizionale, definito per simulare la correzione di una singola domanda di codice  */

                                $idQuestion = $_POST["btnCheckSketch"];
                                $titleTest = $_SESSION["titleTestTested"];

                                $textArea = "txtAnswerSketch";
                                $textArea = $textArea."".$idQuestion;

                                /* valutazione della query scritta dallo studente, rispetto alla soluzione mantenuta nel database */
                                [$outcome, $textMessage] = checkSketch($conn, $idQuestion, $titleTest, $_POST[$textArea]);
                                insertAnswer($conn, $_SESSION["emailStudente"], $idQuestion, $titleTest, $_POST[$textArea], $outcome); 
                                buildForm($conn, $titleTest);   

                                if($outcome == 1) {
                                    echo "<script type='text/javascript'>alert('QUERY CORRETTA');</script>";
                                } elseif(isset($textMessage)) {
                                    echo "<script type='text/javascript'>alert(".json_encode($textMessage).");</script>";
                                } else {
                                    echo "<script type='text/javascript'>alert('QUERY ERRATA');</script>";
                                }
                            }
                        }

                        function buildForm($conn, $titleTest) {
                            /* funzione restituente l'insieme degli id dei quesiti che compongono il test selezionato */
                            $arrayIdQuestion = getQuestionTest($conn, $titleTest);
            
                            foreach($arrayIdQuestion as $i) {
                                /* condizione necessaria per differenziare la visualizzazione dei quesiti, qualora si tratti di una domanda chiusa piuttosto che aperta */
                                if(getTypeQuestion($conn, $i, $titleTest) == "CHIUSA"){
                                    buildFormCheck($conn, $i, $titleTest, true, false);
                                } else {
                                    buildFormQuery($conn, $i, $titleTest, true, false);
                                }
                            }
                        }

                        closeConnection($conn);
                    ?>
                </div>
                <div class="div-button">
                    <button class="button-final" type="submit" name="btnSendExitTest">Send & Exit</button>
                </div>
        </form>
    </body>
</html>