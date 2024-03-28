<?php
    include "evaluateTest.php";
    include "../handlerData/buildForm.php";
    include "../handlerData/check.php";      
    include "../handlerData/dataTest.php";      
    include "../handlerData/manageTest.php";
    include "../../connectionDB.php";

    session_start();
    $conn = openConnection();  

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
        <link rel="stylesheet" type="text/css" href="../../style/css/form_option.css">
        <link rel="stylesheet" type="text/css" href="../../style/css/form_query.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
        </div>
            <form action="" method="POST">
                <div class="container">
                    <?php
                        if($_SERVER["REQUEST_METHOD"] == "POST") {   
                            if(isset($_POST["btnStartTest"])) {
                                $_SESSION["titleTestTested"] = $_POST["btnStartTest"];
                                openTest($conn, $_SESSION["emailStudente"], $_POST["btnStartTest"]); // metodo ideato per inserire il tentativo di risoluzione dello studente all'interno della tabella Completamento
                                buildForm($conn, $_POST["btnStartTest"], null, null); // innescata la costruzione del form                        
                            } elseif(isset($_POST["btnRestartTest"])) {
                                $_SESSION["titleTestTested"] = $_POST["btnRestartTest"]; 
                                buildForm($conn, $_POST["btnRestartTest"], null, null);
                            } elseif(isset($_POST["btnSendExitTest"])) {
                                $arrayIdQuestion = getQuestionTest($conn, $_SESSION["titleTestTested"]); // array contenente l'insieme degli id dei quesiti a cui lo studente ha risposto
                                $mapArrayAnswer = setValueSentMap($arrayIdQuestion); // funzioni ideate per restituire strutture dati che contengano le risposte date dallo studente e le soluzioni dei quesiti
                                $mapArraySolution = setValueSolutionMap($conn, $arrayIdQuestion, $_SESSION["titleTestTested"]); 
                                                
                                checkAnswer($conn, $arrayIdQuestion, $mapArrayAnswer, $mapArraySolution); // metodo attuato per stabilire la correttezza del tentativo risolutivo fornito dallo studente
                                header("Location: ../view/viewTest.php");
                                exit();
                            } elseif(isset($_POST["btnCheckSketch"])) { // ramo del costrutto definito per consentire la correzione di un unico quesito di codice 
                                $_SESSION["checkedQuestion"] = $_POST["btnCheckSketch"]; 
                                $textArea = "txtAnswerSketch";
                                $textArea = $textArea."".$_POST["btnCheckSketch"];

                                if(strlen($_POST[$textArea]) > 0) { // controllo ideato per accertarsi che al textarea non sia vuota
                                    [$rowSolution, $rowAnswer] = checkSketch($conn, $_POST["btnCheckSketch"], $_SESSION["titleTestTested"], $_POST[$textArea]);

                                    if($rowSolution == 0) {
                                        echo "<script type='text/javascript'>alert(".json_encode($rowAnswer).");</script>";
                                        insertAnswer($conn, $_SESSION["emailStudente"], $_POST["btnCheckSketch"], $_SESSION["titleTestTested"], $_POST[$textArea], 0);
                                    } elseif($rowSolution == $rowAnswer) {
                                        echo "<script type='text/javascript'>alert('Query corretta.');</script>";
                                        insertAnswer($conn, $_SESSION["emailStudente"], $_POST["btnCheckSketch"], $_SESSION["titleTestTested"], $_POST[$textArea], 1);
                                    } else {
                                        echo "<script type='text/javascript'>alert('Query errata.');</script>";
                                        insertAnswer($conn, $_SESSION["emailStudente"], $_POST["btnCheckSketch"], $_SESSION["titleTestTested"], $_POST[$textArea], 0);
                                    }

                                    buildForm($conn, $_SESSION["titleTestTested"], $rowAnswer, $rowSolution);  
                                } else {
                                    echo "<script type='text/javascript'>alert('Inserire una query valida.');</script>";
                                    insertAnswer($conn, $_SESSION["emailStudente"], $_POST["btnCheckSketch"], $_SESSION["titleTestTested"], $_POST[$textArea], 0);
                                    buildForm($conn, $_SESSION["titleTestTested"], null, null);  
                                }

                                unset($_SESSION["checkedQuestion"]); // unset dei campi della sessione necessario per fronteggiare ad altre richieste di correzione
                                unset($_SESSION["fieldAnswer"]);
                                unset($_SESSION["fieldSolution"]);
                            }
                        }

                        function buildForm($conn, $titleTest, $rowAnswer, $rowSolution) {
                            $arrayIdQuestion = getQuestionTest($conn, $titleTest);
            
                            foreach($arrayIdQuestion as $i) {
                                if(getTypeQuestion($conn, $i, $titleTest) == "CHIUSA") { // diversificazione del form visualizzato a seconda della tipologia del quesito 
                                    buildFormCheck($conn, $i, $titleTest, true, false);
                                } else {
                                    buildFormQuery($conn, $i, $titleTest, $rowAnswer, $rowSolution, true, false);
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