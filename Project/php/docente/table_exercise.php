<?php
    session_start();

    if(!isset($_SESSION["emailDocente"])) {
        header("Location: ../login/login.php");
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
        <form action="insert/insertTable.php" method="POST">
            <div class="navbar">
                <a><img class="zoom-on-img" width="112" height="48" src="../style/img/ESQL.png"></a>
                <a><button class="button-navbar" type="submit" name="btnInsertTable">Add Table</button></a>
                <a href="handlerDocente.php"><img class="zoom-on-img undo" width="32" height="32" src="../style/img/undo.png"></a>
            </div>
        </form>
        <div>
            <?php 
                $conn = openConnection();

                $sql = "SELECT * FROM Tabella_Esercizio WHERE (EMAIL_DOCENTE=:emailDocente);";
                
                $email = $_SESSION["emailDocente"];
                
                try {
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":emailDocente", $email);

                    $result -> execute();
                    $numRows = $result -> rowCount();

                    if($numRows > 0) {
                        echo '
                            <div class="div-th"> 
                                <table class="table-head-exercise">   
                                    <tr>  
                                        <th>Nome tabella</th>
                                        <th>Data creazione</th>
                                        <th>Numero righe</th>
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
                                            <form action="specifics/specificTable.php" method="POST">
                                                <th><button class="table-button" type="submit" name="btnSpecificTable" value='.$row -> ID.'>Specifics</button></th>
                                            </form>
                                            <form action="specifics/specificRow.php" method="POST">
                                                <th><button class="table-button" type="submit" name="btnViewRow" value='.$row -> ID.'>View Table</button></th>
                                            </form>
                                            <form action="delete/deleteTable.php" method="POST">
                                                <th><button class="table-button" type="submit" name="btnDropTable" value='.$row -> ID.'>Drop Table</button></th>
                                            </form>
                                        </tr>
                                    </table>
                                </div>
                            ';
                        }
                    }

                    closeConnection($conn);
                } catch (PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }
            ?>
        </div>
    </body>
</html>