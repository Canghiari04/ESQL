<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <style>
            <?php
                include 'css/question.css';
            ?>
        </style>
    </head>
    <body>
        <form action="insert.php" method="POST">
            <div class="navbar">
                <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
                <a><button class="navbar-button" type="submit" name="btnInsertDomandaChiusa">Add Chiusa</button></a>
                <a><button class="navbar-button" type="submit" name="btnInsertDomandaCodice">Add Codice</button></a>
                <a href="handlerDocente.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
            </div>
        </form>
        <div>
            <?php 
                include 'connectionDB.php';
                $conn = openConnection();

                $sql = "SELECT * FROM Quesito";
                
                try {
                    $result = $conn -> prepare($sql);
                    $result -> execute();
                    $numRows = $result -> rowCount();

                    if($numRows > 0) {
                        echo '
                            <div class="div-th"> 
                                <table class="table-head">   
                                    <tr>  
                                        <th>Difficoltà</th>
                                        <th>Descrizione</th>
                                        <th>Numero risposte</th>
                                    </tr>
                                </table>
                            </div>
                        ';

                        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                            echo '
                                <div class="div-td">
                                    <table class="table-list">
                                        <tr>
                                            <th>'.$row -> DIFFICOLTA.'</th>
                                            <th>'.$row -> DESCRIZIONE.'</th>
                                            <th>'.$row -> NUM_RISPOSTE.'</th>
                                            <form action="specifics.php" method="GET">
                                                <th><button class="table-button" type="submit" name="btnSpecificQuestion" value='.$row -> ID.'>Options</button></th>
                                            </form>
                                            <form action="deleted.php" method="GET">
                                                <th><button class="table-button" type="submit" name="btnDropQuestion" value='.$row -> ID.'>Drop Question</button></th>
                                            </form>
                                            <form action="" method="GET">
                                                <th><button class="table-button" type="submit" name="btnAddOption">Add Option</button></th>
                                            </form>
                                        </tr>
                                    </table>
                                </div>
                            ';
                        }
                    }

                    closeConnection($conn);
                } catch (PDOException $e) {
                    echo 'Eccezione: '. $e -> getMessage();
                }
            ?>
        </div>
    </body>
</html>