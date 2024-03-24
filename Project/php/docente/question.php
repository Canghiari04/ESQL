<?php
    include "../connectionDB.php";
    
    session_start();
    $conn = openConnection();
    
    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../shared/login/login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../style/css/table_view_linear.css">
    </head>
    <body>
        <div class="navbar">
            <a><img class="zoom-on-img" width="112" height="48" src="../style/img/ESQL.png"></a>
            <form action="insert/insertQuestion.php" method="POST">
                <button class="button-navbar-second" type="submit" name="btnInsertQuestion" value="CHIUSA">Add Chiusa</button>
                <button class="button-navbar-first" type="submit" name="btnInsertQuestion" value="CODICE">Add Codice</button>
            </form>
            <a href="test.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a>
        </div>
        <?php 
            if($_SERVER["REQUEST_METHOD"] == "POST") {
                if(isset($_POST["btnQuestionTest"])) {
                    $_SESSION["titleCurrentTest"] = $_POST["btnQuestionTest"];
                    buildQuestionTest($conn, $_SESSION["titleCurrentTest"]);
                } elseif(isset($_POST["btnUndo"])) {
                    buildQuestionTest($conn, $_SESSION["titleCurrentTest"]);
                }
            } else {
                buildQuestionTest($conn, $_SESSION["titleCurrentTest"]);
            }

            function buildQuestionTest($conn, $titleTest) {
                $sql = "SELECT * FROM Quesito WHERE (Quesito.TITOLO_TEST=:titoloTest);";

                try {
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":titoloTest", $titleTest);

                    $result -> execute();
                } catch (PDOException $e) {
                    echo "Eccezione ". $e -> getMessage()."<br>";
                }
                
                $numRows = $result -> rowCount();
                if($numRows > 0) {
                    echo '
                        <div class="div-th"> 
                            <table class="table-head-question">   
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
                                            <th><button class="table-button" type="submit" name="btnSpecificQuestion" value="'.$row -> ID.'|?|'.$row -> TITOLO_TEST.'|?|'.$row -> DESCRIZIONE.'">Options</button></th>
                                        </form>
                                        <form action="delete/deleteQuestion.php" method="POST">
                                            <th><button class="table-button" type="submit" name="btnDropQuestion" value="'.$row -> ID.'|?|'.$row -> TITOLO_TEST.'">Drop Question</button></th>
                                        </form>
                                    </tr>
                                </table>
                            </div>
                        ';
                    }
                }
            }

            closeConnection($conn);
        ?>
    </body>
</html>