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
            include "../handlerData/buildForm.php";
            include "../handlerData/check.php";
            include "../handlerData/dataTest.php";
            include "../../connectionDB.php";

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
                    /* controllo che il test possieda quesiti e che gli stessi abbiano almeno una soluzione */
                    if(checkCompletedTest($conn, $row -> TITOLO)) {
                        /* acquisizione dello stato di completamento del Test, da cui sarà diversificata la visualizzazione dei bottoni */
                        $stateTest = checkStateTest($conn, $_SESSION["emailStudente"], $row -> TITOLO);
                        echo '
                            <div class="div-td">
                                <table class="table-list">
                                    <tr>
                                        <th>'.$row -> TITOLO.'</th>
                                        <th>'.$row -> DATA_CREAZIONE.'</th>
                                        <th>'.$stateTest.'</th>
                                        '.buildButtonForm($conn, $_SESSION["emailStudente"], $row -> TITOLO, $stateTest).'
                                    </tr>
                                </table>
                            </div>
                        ';
                    }
                }
            }
        ?>
    </body>
</html>