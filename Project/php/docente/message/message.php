<?php
    session_start();
    if(!isset($_SESSION['emailDocente'])) {
        header('Location: ../../shared/login/login.php');
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Public Sans' rel='stylesheet'>
        <link rel="stylesheet" type="text/css" href="../style/css/specific_linear.css">
    </head>
    <body>
        <div class="navbar">
            <a href="index.php?"><img class="zoom-on-img ESQL" width="112" height="48" src="../style/img/ESQL.png"></a>
            <a class="a-href" href="newMessage.php">Nuovo Messaggio</a>
            <a class="a-href" href="messageReceived.php">Entrata</a>
            <a class="a-href" href="message.php">Uscita</a>
        </div>
        <div class="center">
            <?php 
                include '../../connectionDB.php';

                $conn = openConnection();

                $sql = "SELECT * FROM Messaggio WHERE EMAIL_DOCENTE = :emailDocente AND ID NOT IN (SELECT ID_MESSAGGIO_STUDENTE FROM Messaggio_Studente) ;";
                
                try{
                    $result = $conn -> prepare($sql);
                    $result -> bindValue(":emailDocente", $_SESSION["emailDocente"]);

                    $result -> execute();

                } catch(PDOException $e) {
                    echo "Eccezione ".$e -> getMessage()."<br>";
                }

                echo '
                    <div class="div-th"> 
                        <table class="table-head">   
                            <tr>  
                                <th>Titolo</th>
                                <th>Test Riferimento</th>
                                <th>Data</th>
                            </tr>
                        </table>
                    </div>
                ';

                if(isset($result)) {
                    while($row = $result->fetch(PDO::FETCH_OBJ)) {
                        echo '
                            <div class="div-td">
                                <table class="table-list">   
                                    <tr>  
                                        <th>'.$row -> TITOLO.'</th>
                                        <th>'.$row -> TITOLO_TEST.'</th>
                                        <th>'.$row -> DATA_INSERIMENTO.'</th>
                                        <th>
                                            <form action="viewMessage.php" method="POST">
                                                <button class="button-navbar-second" type="submit" name="btnViewMessage" value= "'.$row -> ID.'">Visualizza Messaggio</button>
                                            </form>
                                        </th>
                                    </tr>
                                </table>
                            </div>
                        ';
                    }
                }       
            ?>       
    </body>
</html>