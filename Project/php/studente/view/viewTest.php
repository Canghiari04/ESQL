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
        <link rel="stylesheet" type="text/css" href="../../style/css/table_view_linear.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../../style/img/ESQL.png"></a>
            <a href="../handlerStudente.php"><img class="zoom-on-img undo" width="32" height="32" src="../../style/img/undo.png"></a>
        </div>
        <?php
            include "../handlerData/dataTest.php";
            include "../../connectionDB.php";

            session_start();
            $conn = openConnection();   

            $sql = "SELECT * FROM Test;";

            try {
                $result = $conn -> prepare($sql);

                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
            
            $numRows = $result -> rowCount();
            
            if($numRows > 0) {
                echo '
                    <div class="div-th"> 
                        <table class="table-head-test">   
                            <tr>
                                <th>Nome test</th>          
                                <th>Data creazione</th>
                                <th>Stato test</th>
                            </tr>
                        </table>
                    </div>
                ';

                while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                    if(checkCompletedTest($conn, $row -> TITOLO)) {
                        echo '
                            <div class="div-td">
                                <table class="table-list">
                                    <tr>
                                        <th>'.$row -> TITOLO.'</th>
                                        <th>'.$row -> DATA_CREAZIONE.'</th>
                                        <th>'.checkStateTest($conn, $_SESSION["emailStudente"], $row -> TITOLO) -> STATO.'</th>
                                        '.checkTest($conn, $_SESSION["emailStudente"], $row -> TITOLO).'
                                    </tr>
                                </table>
                            </div>
                        ';
                    }
                }
            }

            /* metodo utilizzato per rendere dinamica la stampa dei bottoni, a seconda dello stato del test, da cui successivamente sarà possibile conseguire in differenti funzionalità */
            function checkTest($conn, $email, $titleTest) {
                $rowState = checkStateTest($conn, $email, $titleTest);

                /* rispetto allo stato del test, circoscritto al tentativo sostenuto dallo studente in questione, si differenziano le funzionalità che possono susseguirsi, abilitando o meno il bottone di riferimento */
                switch($rowState -> STATO) {
                    case "APERTO":
                        return '
                            <form action="viewAnswer.php" method="POST">
                                <th><button class="table-button" type="submit" name="btnViewRisposte" disabled>Disabled Answers</button></th>
                            </form>
                            <form action="../test/buildTest.php" method="POST">
                                <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$rowState -> TITOLO_TEST.'">Restart Test</button></th>
                            </form>
                        ';
                    break;
                    case "INCOMPLETAMENTO":
                        return '
                            <form action="viewAnswer.php" method="POST">
                                <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$rowState -> TITOLO_TEST.'">View Answers</button></th>
                            </form>
                            <form action="../test/buildTest.php" method="POST">
                                <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$rowState -> TITOLO_TEST.'">Restart Test</button></th>
                            </form>
                        ';
                    break;
                    case "CONCLUSO":
                        return '
                            <form action="viewAnswer.php" method="POST">
                                <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$rowState -> TITOLO_TEST.'">View Answers</button></th>
                            </form>
                            <form action="../test/buildTest.php" method="POST">
                                <th><button class="table-button" type="submit" name="btnRestartTest" disabled>Closed Test</button></th>
                            </form>
                        ';
                    break;
                    default:
                        $rowViewAnswer = checkViewAnswer($conn, $titleTest);
                        
                        if(($rowViewAnswer -> VISUALIZZA_RISPOSTE) == 0) {
                            if(checkNumQuestion($conn, $titleTest)) {
                                return '
                                    <form action="viewAnswer.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnViewRisposte" disabled>Disabled Answers</button></th>
                                    </form>
                                    <form action="../test/buildTest.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnStartTest" value="'.$rowViewAnswer -> TITOLO.'">Start Test</button></th>
                                    </form>
                                ';
                            } else {
                                /* ramo attuato qualora il test non abbia quesiti al suo interno, quindi non sia composto da alcuna domanda */
                                return '
                                    <form action="viewAnswer.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnViewRisposte" disabled>Disabled Answers</button></th>
                                    </form>
                                    <form action="../test/buildTest.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnStartTest" disabled>Disabled Test</button></th>
                                    </form>
                                ';
                            }
                        } elseif(checkNumAnswer($conn, $email, $titleTest)) {
                            return '
                                <form action="viewAnswer.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$rowViewAnswer -> TITOLO.'">View Answers</button></th>
                                </form>
                                <form action="../test/buildTest.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnStartTest" disabled>Disabled Test</button></th>
                                </form>
                            ';
                        } else {
                            /* stringa inviata tramite il tag value del bottone, permettendo in questo modo il corretto reindirizzamento tra file */
                            $namePage = "viewTest.php";
                            return '
                                <form action="viewSolution.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnViewSolution" value="'.$rowViewAnswer -> TITOLO.'|?|'.$namePage.'">View Solution</button></th>
                                </form>
                                <form action="../test/buildTest.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnStartTest" disabled>Disabled Test</button></th>
                                </form>
                            ';
                        }
                    break;      
                } 
            }

            /* controllo relativo allo stato del test, interrogando la collezione Completamento */
            function checkStateTest($conn, $email, $titleTest) {
                $sql = "SELECT TITOLO_TEST, STATO FROM Completamento WHERE (Completamento.EMAIL_STUDENTE=:emailStudente) AND (Completamento.TITOLO_TEST=:titoloTest);";

                try {
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":emailStudente", $email);
                    $result -> bindValue(":titoloTest", $titleTest);
                    
                    $result -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";  
                }
            
                return $result -> fetch(PDO::FETCH_OBJ);
            }

            /* metodo utilizzato per stabilire se sia possibile visualizzare le soluzioni del test */
            function checkViewAnswer($conn, $titleTest) {           
                $sql = "SELECT TITOLO, VISUALIZZA_RISPOSTE FROM Test WHERE (Test.TITOLO=:titoloTest);";

                try {
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":titoloTest", $titleTest);

                    $result -> execute();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                return $result -> fetch(PDO::FETCH_OBJ);
            }

            /* valorizzazione del numero di quesiti che compongono il test */
            function checkNumQuestion($conn, $titleTest) {
                $sql = "SELECT * FROM Quesito WHERE (Quesito.TITOLO_TEST=:titoloTest);";           

                try {
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":titoloTest", $titleTest);

                    $result -> execute();
                } catch (PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>"; 
                }
                
                $numRows = $result -> rowCount();
                return ($numRows > 0);
            }

            function checkNumAnswer($conn, $email, $titleTest) {
                $sql = "SELECT * FROM Risposta WHERE (Risposta.EMAIL_STUDENTE=:emailStudente) AND (Risposta.TITOLO_TEST=:titoloTest);";           

                try {
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":emailStudente", $email);
                    $result -> bindValue(":titoloTest", $titleTest);

                    $result -> execute();
                } catch (PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>"; 
                }
                
                $numRows = $result -> rowCount();
                return ($numRows > 0);
            }
        ?>
    </body>
</html>