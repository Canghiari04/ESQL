<?php
    session_start();
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
            include "../../connectionDB.php";

            $conn = openConnection();   

            $sql = "SELECT * FROM Test;";

            try {
                $result = $conn -> prepare($sql);

                $result -> execute();
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

                    checkStateTest($conn, $_SESSION["emailStudente"], $row -> TITOLO);

                    while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                        echo '
                            <div class="div-td">
                                <table class="table-list">
                                    <tr>
                                        <th>'.$row -> TITOLO.'</th>
                                        <th>'.$row -> DATA_CREAZIONE.'</th>
                                        <th>'.$row -> TITOLO.'</th>
                                        '.checkStateTest($conn, $_SESSION["emailStudente"], $row -> TITOLO).'
                                    </tr>
                                </table>
                            </div>
                        ';
                    }
                }
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }

            /* metodo utilizzato per rendere dinamica la stampa dei bottoni, a seconda dello stato del test, da cui successivamente sarà possibile conseguire in differenti funzionalità */
            function checkStateTest($conn, $email, $titleTest) {
                $sql = "SELECT TITOLO_TEST, STATO FROM Completamento WHERE (Completamento.EMAIL_STUDENTE=:emailStudente) AND (Completamento.TITOLO_TEST=:titoloTest);";

                try {
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":emailStudente", $email);
                    $result -> bindValue(":titoloTest", $titleTest);
                    
                    $result -> execute();
                    $row = $result -> fetch(PDO::FETCH_OBJ);
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";  
                }

                $stateTest = $row -> STATO;

                /* rispetto allo stato del test circoscritto al tentativo sostenuto dallo studente in questione, si differenziano le funzionalità che possono susseguirsi, abilitando o meno il bottone di riferimento */
                switch($stateTest) {
                    case "APERTO":
                        return '<form action="viewRisposte.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$row -> TITOLO_TEST.'" disabled>View Answers</button></th>
                                </form>
                                <form action="../test/buildTest.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$row -> TITOLO_TEST.'">Restart Test</button></th>
                                </form>';
                        break;
                    case "INCOMPLETAMENTO":
                        return '<form action="viewRisposte.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$row -> TITOLO_TEST.'">View Answers</button></th>
                                </form>
                                <form action="../test/buildTest.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$row -> TITOLO_TEST.'">Restart Test</button></th>
                                </form>';
                        break;
                    case "CONCLUSO":
                        return '<form action="viewRisposte.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$row -> TITOLO_TEST.'">View Answers</button></th>
                                </form>
                                <form action="../test/buildTest.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnRestartTest" value="'.$row -> TITOLO_TEST.'" disabled>Closed Test</button></th>
                                </form>';
                        break;
                    default:
                        return '<form action="viewRisposte.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$row -> TITOLO_TEST.'" disabled>View Answers</button></th>
                                </form>
                                <form action="../test/buildTest.php" method="POST">
                                    <th><button class="table-button" type="submit" name="btnStartTest" value="'.$row -> TITOLO_TEST.'" disabled>Start Test</button></th>
                                </form>';
                        break;      
                } 
            }
        ?>
    </body>
</html>