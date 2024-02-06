<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <style>
            <?php 
                include 'css/table_exercise.css';
            ?>
        </style>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
            <a><button class="navbar-button" type="submit">Add Table</button></a>
            <a href="handlerDocente.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
        </div>
        <div>
            <?php 
                include 'connectionDB.php';
                $conn = openConnection();

                $sql = "SELECT * FROM Tabella_Esercizio";
                
                try {
                    $result = $conn -> prepare($sql);
                    $result -> execute();
                    $numRows = $result -> rowCount();

                    if($numRows > 0) {
                        echo'
                            <div class="div-th"> 
                                <table class="table-head">   
                                    <tr>  
                                        <th> Nome tabella </th>
                                        <th> Data creazione </th>
                                        <th> Numero righe </th>
                                    </tr>
                                </table>
                            </div>
                        ';

                        while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                            echo '
                                <div class="div-td">
                                    <table class="table-list">
                                        <tr>
                                            <th>'.$row -> NOME.'</th>
                                            <th>'.$row -> DATA_CREAZIONE.'</th>
                                            <th>'.$row -> NUM_RIGHE.'</th>
                                            <form action="specifics.php" method="GET">
                                                <th><button class="table-button" type="submit" name="btnSpecificTable" value='.$row -> ID.'>Specifics</button></th>
                                            </form>
                                            <form action="deleted.php" method="GET">
                                                <th><button class="table-button" type="submit" name="btnDropTable" value='.$row -> ID.'>Drop Table</button></th>
                                            </form>
                                            <form action="insert.php" method="GET">
                                                <th><button class="table-button" type="submit" name="btnInsertTableRow" value='.$row -> ID.'>Insert Row</button></th>
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