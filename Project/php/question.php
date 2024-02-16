<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <style>
            <?php
                include 'connectionDB.php';
                include 'css/question.css';
            ?>
        </style>
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="img/ESQL.png"></a>
            <div class="dropdown">
                <button class="dropbtn">Add Question</button>
                <div class="dropdown-content">
                    <a href="insert/insertQuestion.php?chiusa">Chiusa</a>
                    <a href="insert/insertQuestion.php?codice">Codice</a>
                </div>
            </div>
            <a href="handlerDocente.php"><img class="zoom-on-img undo" width="32" height="32" src="img/undo.png"></a>
        </div>
        <?php 
            $conn = openConnection();

            $sql = "SELECT * FROM Quesito";
                
            try {
                $result = $conn -> prepare($sql);

                $result -> execute();
                $numRows = $result -> rowCount();
            } catch (PDOException $e) {
                echo 'Eccezione: '. $e -> getMessage();
            }

            if($numRows > 0) {
                echo '
                    <div class="div-th"> 
                        <table class="table-head">   
                            <tr>  
                                <th>Descrizione</th>
                                <th>Difficoltà</th>
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
                                    <th>'.$row -> DESCRIZIONE.'</th>
                                    <th>'.$row -> DIFFICOLTA.'</th>
                                    <th>'.$row -> NUM_RISPOSTE.'</th>
                                    <form action="specifics/specificQuestion.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnSpecificQuestion" value='.$row -> ID.'>Options</button></th>
                                    </form>
                                    <form action="delete/deleteQuestion.php" method="POST">
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
        ?>
    </body>
</html>