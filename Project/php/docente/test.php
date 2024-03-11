<?php
    session_start();
    
    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../shared/login/login.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://fonts.googleapis.com/css?family=Public Sans" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="../style/css/navbar_button_undo.css">
        <link rel="stylesheet" type="text/css" href="../style/css/table_view_linear.css">
        <?php 
            include "../connectionDB.php";
        ?>
    </head>
    <body>
        <form action="insert/insertTest.php" method="POST">
            <div class="navbar">
                <a><img class="zoom-on-img" width="112" height="48" src="../style/img/ESQL.png"></a>
                <a><button class="button-navbar" type="submit" name="btnInsertTest">Add Test</button></a>
                <a href="handlerDocente.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a>
            </div>
        </form>
        <?php
            $conn = openConnection();   

            $sql = "SELECT * FROM Test WHERE (EMAIL_DOCENTE=:emailDocente);";

            try {
                $result = $conn -> prepare($sql);
                $result -> bindValue(":emailDocente", $_SESSION["emailDocente"]);

                $result -> execute();
            } catch(PDOException $e) {
                echo "Eccezione ".$e -> getMessage()."<br>";
            }
                
            $numRows = $result -> rowCount();
            
            if($numRows > 0) {
                echo '
                    <div class="div-th"> 
                        <table class="table-head">   
                            <tr>  
                                <th>Nome test</th>
                                <th>Data creazione</th>
                                <th>Visualizza risposte</th>
                            </tr>
                        </table>
                    </div>
                ';

                while($row = $result -> fetch(PDO::FETCH_OBJ)) {
                    $var = convertToString($row -> VISUALIZZA_RISPOSTE);

                    echo '
                        <div class="div-td">
                            <table class="table-list">
                                <tr>
                                    <th>'.$row -> TITOLO.'</th>
                                    <th>'.$row -> DATA_CREAZIONE.'</th>
                                    <th>'.$var.'</th>
                                    <form action="question.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnQuestionTest" value="'.$row -> TITOLO.'">Questions</button></th>
                                    </form>
                                    <form action="delete/deleteTest.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnUpdateTest" value="'.$row -> TITOLO.'">Update View</button></th>
                                    </form>
                                    <form action="delete/deleteTest.php" method="POST">
                                        <th><button class="table-button" type="submit" name="btnDropTest" value="'.$row -> TITOLO.'">Drop Test</button></th>
                                    </form>
                                </tr>
                            </table>
                        </div>
                    ';
                }
            }

            function convertToString($var) {
                if($var == 0) {
                    return "false";
                } else {
                    return "true";
                }
            }
        ?>
    </body>
</html>