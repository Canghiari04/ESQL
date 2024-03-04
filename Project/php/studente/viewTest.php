<?php
    session_start();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../style/css/table_view_linear.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../style/img/ESQL.png"></a>
            <a href="handlerStudente.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a>
        </div>
        <?php
            include '../connectionDB.php';

            $conn = openConnection();   

            $sql = "SELECT * FROM Test;";

            try {
                $result = $conn -> prepare($sql);

                $result -> execute();
                $numRows = $result -> rowCount();
            
                if($numRows > 0) {
                    echo '
                        <div class="div-th"> 
                            <table class="table-head">   
                                <tr>  
                                    <th>Nome test</th>          
                                    <th>Data creazione</th>
                                    <th>Stato test</th>
                                </tr>
                            </table>
                        </div>
                    ';

                    //FARE BOTTONI DINAMICI
                    while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                        echo '
                            <div class="div-td">
                                <table class="table-list">
                                    <tr>
                                        <th>'.$row -> TITOLO.'</th>
                                        <th>'.$row -> DATA_CREAZIONE.'</th>
                                        <th>'.$row -> TITOLO.'</th>
                                        <form action="view/viewRisposte.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$row -> TITOLO.'">View Answers</button></th>
                                        </form>
                                        <form action="." method="POST">
                                            <th><button class="table-button" type="submit" name="." value="'.$row -> TITOLO.'">Restart test</button></th>
                                        </form>
                                        <form action="test/buildTest.php" method="POST">
                                            <th><button class="table-button" type="submit" name="btnStartTest" value="'.$row -> TITOLO.'">Start Test</button></th>
                                        </form>
                                    </tr>
                                </table>
                            </div>
                        ';
                    }
                }
            } catch(PDOException $e) {
                echo 'Eccezione '.$e -> getMessage().'<br>';
            }
        ?>
    </body>
</html>