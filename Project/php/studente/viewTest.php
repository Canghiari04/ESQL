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
        <?php 
            include '../connectionDB.php';
        ?>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../style/img/ESQL.png"></a>
            <a href="handlerStudente.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a>
        </div>
        <?php
            $conn = openConnection();   

            $sql = getCorrectQuery($conn);

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

                    while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                        echo '
                            <div class="div-td">
                                <table class="table-list">
                                    <tr>
                                        <th>'.$row -> TITOLO.'</th>
                                        <th>'.$row -> DATA_CREAZIONE.'</th>
                                        <th>'.$row -> TITOLO.'</th>
                                        <form action="." method="POST">
                                            <th><button class="table-button" type="submit" name="." value="'.$row -> TITOLO.'">.</button></th>
                                        </form>
                                        <form action="view/viewRisposte.php" method="POST">
                                            <th><button class="table-button" type="submit" name="btnViewRisposte" value="'.$row -> TITOLO.'">View Answers</button></th>
                                        </form>
                                        <form action="test/startTest.php" method="POST">
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

            function getCorrectQuery($conn) {
                $sql = "SELECT * FROM Test JOIN Completamento ON (Test.TITOLO=Completamento.TITOLO_TEST);";

                try {
                    $result = $conn -> prepare($sql);
                    
                    $result -> execute();
                    $numRows = $result -> rowCount();
                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                if($numRows > 0) {
                    return "SELECT * FROM Test JOIN Completamento ON (Test.TITOLO=Completamento.TITOLO_TEST);";
                } else {
                    return "SELECT * FROM Test;";
                }
            }
        ?>
    </body>
</html>